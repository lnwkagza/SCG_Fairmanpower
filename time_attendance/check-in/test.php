<?php 

function decimalHours($time)
{
    $hms = explode(":", $time);
    return (floatval($hms[0]) + round($hms[1]/60,3));
}
function decimalToHours($dec)
{
    $seconds = ($dec * 3600);
    // we're given hours, so let's get those the easy way
    $hours = floor($dec);
    // since we've "calculated" hours, let's remove them from the seconds variable
    $seconds -= $hours * 3600;
    // calculate minutes left
    $minutes = floor($seconds / 60);
    // remove those from seconds as well
    $seconds -= $minutes * 60;
    if ($seconds >= 50) {
        $minutes = $minutes + 1;
        if ($minutes == 60) {
            $hours += 1;
            $minutes = 0;
        }
    }
    // return the time formatted HH:MM:SS
    return lz($hours).":".lz($minutes);
}

function decimalToHours2($decimal) {
    $hours = floor($decimal);
    $minutes = floor(($decimal - $hours) * 60);
    $seconds = floor((($decimal - $hours) * 60 - $minutes) * 60);
  
    return sprintf("%02d:%02d", $hours, $minutes);
  }

// lz = leading zero
function lz($num)
{
    return (strlen($num) < 2) ? "0{$num}" : $num;
}

function dateformat($date){
    $var = $rs->date;
    $datee = str_replace('-', '/', $date);
    $dat=date_create($datee);
    $date_format($dat,"d/m");
}

function DateThai($strDate){
    $strYear = date("Y", strtotime($strDate))+543;
    $strMonth= date("n",strtotime($strDate));
    $strDay= date("j",strtotime($strDate));
    $strHour= date("H",strtotime($strDate));
    $strMinute= date("i",strtotime($strDate));
    $strSeconds= date("s",strtotime($strDate));
    $strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
    $strMonthThai=$strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";
}

function DateThaiWithoutTime($strDate){
    $timestamp = date_create($strDate);
    $strYear = date_format($timestamp, "Y") + 543;
    $strMonth = date_format($timestamp, "n");
    $strDay = date_format($timestamp, "j");
    $strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
    $strMonthThai = $strMonthCut[$strMonth];
    return "$strDay $strMonthThai $strYear";
}


?>