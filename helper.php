<?php

class Helper{
    
    public function checkDateFormat($date = false, $output = true){
        global $config;
        
        $date_arr = explode("-", $date);
        if (!is_array($date_arr) || sizeof($date_arr)!=3 || !checkDate($date_arr[1], $date_arr[2], $date_arr[0])){
            if ($output) print('Error. Wrong date format. ');
            return false;
        }
        return true;
    }
    
}

?>