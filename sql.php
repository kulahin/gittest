<?php
 

    class Sqlme{
        
        var $cid; //connection id
        var $result; //query result
        var $size_of_result = 0; //size of query result
        var $last_id;
        var $lquery;
        var $db; //current database

        function sqlme($DBhost, $DBuser, $DBpwd, $DBname)
        {
            $this->cid = mysql_connect($DBhost, $DBuser, $DBpwd) or $this->Error();
            if (mysql_select_db($DBname)) $this->db = $DBname; else $this->db = false;
            $this->Query("SET NAMES `utf8`;");
        }

        function Error($query = '')
        {
            echo "<div style='font-family: verdana; font-size: 11px; color: #ff0000'><b>MySQL Error</b> ".mysql_error()."<br /><font color='#000000'>".$query."</font></div>";
        }

        function Query($query = '', $flag = 0)
        {
            $query = str_replace(array("\r","\n","\t"),'',$query);
            $query = preg_replace('/(,|\(){1}\s+/','$1',$query);
            if (preg_match('/^select/', strtolower(trim($query))))
            {
                $this->lquery = $query;
                $this->result = mysql_query($query) or $this->Error($query);
                $this->size_of_result = mysql_num_rows($this->result);
                if ($flag == 1) return $this->size_of_result;
                return $this->result;
            }
            else
            {
                mysql_query($query) or $this->Error($query);
                $this->size_of_result = mysql_affected_rows($this->cid);
                if (preg_match('/^insert/', strtolower(trim($query)))) $this->last_id = mysql_insert_id($this->cid);
                return $this->size_of_result;
            }
        }
        function TableStatus()
        {
            mysql_select_db($this->db,$this->cid);
            $table_res = mysql_query ('show table status');
            return $table_res;
        }
        function GetAssoc()
        {
            if($this->size_of_result != 0){
                while($row = mysql_fetch_assoc($this->result)){
                    $data[] = $row;
                }
                return $data;
            }else{
                return false;
            }
        }

        function GetFirst()
        {
            if($this->size_of_result != 0){
                return mysql_result($this->result,0);
            }else{
                return false;
            }
        }

        function Close()
        {
            mysql_close($this->cid);
        }
    }
    
?>