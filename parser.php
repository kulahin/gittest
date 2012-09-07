<?php

class Parser{
    
    public $date = "";
    
    public $page = "";
    
    public function __construct() {
        
    }
    
    public function start($date = false){
        global $config;
        
        $this->date = $date ? $date : date("Y-m-d");
        if (!Helper::checkDateFormat($this->date)) return false;
        
        if ($this->verifyDate()) return true;
        
        $page_info = $this->getPageInfo($config["url"].$this->date);
        $this->getPageHtml($page_info);
        $result = $this->parsePage();
        return $this->storeData($result);
    }
    
    private function verifyDate(){
        global $sql;
        
        $query = "SELECT `collected` FROM `dates` WHERE `date` = '".$this->date."' LIMIT 1";
        $sql->Query($query);
        if ($sql->size_of_result != 0 && $sql->GetFirst() == 1){
            return true;
        }
        return false;
    }
    
    public function getPageInfo($url)
    {
        global $sql;
        
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)");
        curl_setopt($ch, CURLOPT_FAILONERROR, 1); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            
        $result['content'] = curl_exec($ch);
        $result['errorno'] = curl_errno($ch);
        $result['error'] = curl_error($ch);
        
        $info = curl_getinfo($ch);
        curl_close($ch);
        $result['http_code'] = !empty($info['http_code']) ? $info['http_code'] : "";

        return $result;
    }

    private function getPageHtml($result)
    {
        global $sql;
        
        if ($result['errorno'])
        {
            $query = "REPLACE INTO `dates` (`date`, `collected`) VALUES ('".$this->date."', '0') ";
            $sql->Query($query);
            $this->page = false;
        }else{
            $query = "INSERT INTO `dates` (`date`, `collected`) VALUES ('".$this->date."', '1') ";
            $sql->Query($query);
            $this->page = $result['content'];
        }
        
        return $this->page;
    }

    public function parsePage($page = false){
        
        if (!$this->page) return false;
        
        if ($page) $this->page = $page;
        
        preg_match('/\id="top250_place_1"(.*)\<a id="formula"\>/s', $this->page, $list);
        $content = $list[0];
        $result = array();
        $have_result = true;
        $key = 1;
        
        while ($have_result){
            preg_match('/id="top250_place_(?:(?:(?!id="top250_place_).)*)\<s class="dot"\>/s', $content, $matches);
            if (!empty($matches)){
                $block = $matches[0];
                $result[$key] = array();
                $result[$key]["rank"] = $key;
                $result[$key]["title"] = $this->getValueByRegexpr('/\<span class="text-grey"\>((?:(?!\<\/span\>).)*)\<\/span\>/s', $block);
                if ($result[$key]["title"] == ""){
                    $result[$key]["title"] = $this->getValueByRegexpr('/class="all"\>([^\(]*)(?:(?:(?!\<\/a\>).)*)\<\/a\>/s', $block);
                }
                $result[$key]["title"] = mysql_real_escape_string(html_entity_decode(iconv("windows-1251", "UTF-8", $result[$key]["title"]), ENT_QUOTES, 'UTF-8'));
                $result[$key]["rate"] = $this->getValueByRegexpr('/class="continue"\>((?:(?!\<\/a\>).)*)\<\/a\>/s', $block);
                $result[$key]["year"] = $this->getValueByRegexpr('/class="all"\>[^\(]*\((\d{4})\)\s*\<\/a\>/s', $block);
                $result[$key]["voters"] = str_replace("&nbsp;", "", $this->getValueByRegexpr('/\<span style="color: \#777"\>\(((?:(?!\)).)*)\)\<\/span\>/s', $block));
                $key++;
                $content = str_replace($block, "", $content);
            }else{
                $have_result = false;
            }
        }
        
        return $result;
    }
    
    private function storeData($result){

        global $sql;
        
        $insert_sql = "INSERT INTO `rating` (`date`, `rank`, `movie_id`, `voters`, `rate`) VALUES ";
        
        foreach ($result as $key=>$row){
            $query = "SELECT `id` FROM `movies` WHERE `title` = '".$row["title"]."' AND `year` = '".$row["year"]."' LIMIT 1";
            $sql->Query($query);
            if ($sql->size_of_result != 0){
                $movie_id = $sql->GetFirst();
            }else{
                $query = "INSERT INTO `movies` (`title`, `year`) VALUES ('".$row["title"]."', '".$row["year"]."') ";
                $sql->Query($query);
                $movie_id = $sql->last_id;
            }
            $result[$key]["movie_id"] = $movie_id;
            $insert_sql .= " ('".$this->date."', '".$row["rank"]."', '".$movie_id."', '".$row["voters"]."', '".$row["rate"]."'),";
        }
        $insert_sql = substr($insert_sql, 0, -1);
        
        return $sql->Query($insert_sql);
    }

    private function getValueByRegexpr($regexpr, $where){
        preg_match($regexpr, $where, $matches);
        return !empty($matches) ? trim($matches[1]) : false;
    }
}
?>