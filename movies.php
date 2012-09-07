<?php

class Movies{
    
    private $date = "";
    
    public function getListByDate($date = false){
        global $config;
        global $sql;
        
        $this->date = $date ? $date : date("Y-m-d");
        
        if (!Helper::checkDateFormat($this->date)) return false;
        
        $query = "SELECT r.*, m.title, m.year FROM `rating` r LEFT JOIN `movies` m ON r.`movie_id` = m.id WHERE r.`date` = '".$this->date."'  ORDER BY r.`rank` LIMIT ".$config['movies_count'];
        $sql->Query($query);
        if ($sql->size_of_result == 0) return false;
        $list = $sql->GetAssoc();
        
        return $list;
    }
    
    public function showTable($list){
        ?>
        <table>
            <tr class="head">
                <td class="rank">&nbsp;</td>
                <td class="title">Movie</td>
                <td class="rate">Rate</td>
                <td class="voters">Voters</td>
            </tr>
        <?php
        if (!empty($list)){
            foreach ($list as $row){
                if (floor($row["rank"]/2)*2 == $row["rank"]) $class="even"; else $class = "odd";
                ?>
                <tr class="<?php echo $class; ?>">
                    <td class="rank"><?php echo $row["rank"]; ?></td>
                    <td class="title"><?php echo $row["title"]; ?> (<?php echo $row["year"]; ?>)</td>
                    <td class="rate"><?php echo $row["rate"]; ?></td>
                    <td class="voters"><?php echo $row["voters"]; ?></td>
                </tr>
                <?php
            }
        }else{
                ?>
                <tr class="odd">
                    <td colspan="4">No data for this date</td>
                </tr>
                <?php
        }
        ?>
        </table>
        <?php
    }
}
?>