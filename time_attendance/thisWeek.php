<?php
error_reporting(E_ERROR | E_PARSE); 
class week {
    
    // Properties
    public $start_monday;
    public $end_monday;
    
    // Methods
    function set_day($time_stamp) {
        // echo $time_stamp;
        if(date('D') === 'Mon'){ 
            // echo "It is Monday today\n";
            $start_monday = (new DateTime($expirydate))->format('Y-m-d');
            $end_monday = (new DateTime($expirydate))->add(new DateInterval('P7D'))->format('Y-m-d');
        }else if(date('D') === 'Tue'){ 
            // echo "It is Tuesday today\n";
            $start_monday = (new DateTime($expirydate))->sub(new DateInterval('P1D'))->format('Y-m-d');
            $end_monday = (new DateTime($expirydate))->add(new DateInterval('P6D'))->format('Y-m-d');
        }else if(date('D') === 'Wed'){ 
            // echo "It is Wednesday today\n";
            $start_monday = (new DateTime($expirydate))->sub(new DateInterval('P2D'))->format('Y-m-d');
            $end_monday = (new DateTime($expirydate))->add(new DateInterval('P5D'))->format('Y-m-d');
        }else if(date('D') === 'Thu'){ 
            // echo "It is Thuseday today\n";
            $start_monday = (new DateTime($expirydate))->sub(new DateInterval('P3D'))->format('Y-m-d');
            $end_monday = (new DateTime($expirydate))->add(new DateInterval('P4D'))->format('Y-m-d');
        }else if(date('D') === 'Fri'){ 
            // echo "It is Friday today\n";
            $start_monday = (new DateTime($expirydate))->sub(new DateInterval('P4D'))->format('Y-m-d');
            $end_monday = (new DateTime($expirydate))->add(new DateInterval('P3D'))->format('Y-m-d');
        }else if(date('D') === 'Sat'){ 
            // echo "It is Saturday today\n";
            $start_monday = (new DateTime($expirydate))->sub(new DateInterval('P5D'))->format('Y-m-d');
            $end_monday = (new DateTime($expirydate))->add(new DateInterval('P2D'))->format('Y-m-d');
        }else if(date('D') === 'Sun'){ 
            // echo "It is Sunday today\n";
            $start_monday = (new DateTime($expirydate))->sub(new DateInterval('P6D'))->format('Y-m-d');
            $end_monday = (new DateTime($expirydate))->add(new DateInterval('P1D'))->format('Y-m-d');
        }
        
        $this->start_monday = $start_monday;
        $this->end_monday = $end_monday;
    }
    
    function set_dayy($time_stamp) {
        $timestamp = strtotime($time_stamp);
        
        if(date('D',$timestamp) === 'Mon'){ 
            // echo "It is Monday today\n";
            $start_monday = (new DateTime($time_stamp))->format('Y-m-d');
            $end_monday = (new DateTime($time_stamp))->add(new DateInterval('P7D'))->format('Y-m-d');
        }else if(date('D',$timestamp) === 'Tue'){ 
            // echo "It is Tuesday today\n";
            $start_monday = (new DateTime($time_stamp))->sub(new DateInterval('P1D'))->format('Y-m-d');
            $end_monday = (new DateTime($time_stamp))->add(new DateInterval('P6D'))->format('Y-m-d');
        }else if(date('D',$timestamp) === 'Wed'){ 
            // echo "It is Wednesday today\n";
            $start_monday = (new DateTime($time_stamp))->sub(new DateInterval('P2D'))->format('Y-m-d');
            $end_monday = (new DateTime($time_stamp))->add(new DateInterval('P5D'))->format('Y-m-d');
        }else if(date('D',$timestamp) === 'Thu'){ 
            // echo "It is Thuseday today\n";
            $start_monday = (new DateTime($time_stamp))->sub(new DateInterval('P3D'))->format('Y-m-d');
            $end_monday = (new DateTime($time_stamp))->add(new DateInterval('P4D'))->format('Y-m-d');
        }else if(date('D',$timestamp) === 'Fri'){ 
            // echo "It is Friday today\n";
            $start_monday = (new DateTime($time_stamp))->sub(new DateInterval('P4D'))->format('Y-m-d');
            $end_monday = (new DateTime($time_stamp))->add(new DateInterval('P3D'))->format('Y-m-d');
        }else if(date('D',$timestamp) === 'Sat'){ 
            // echo "It is Saturday today\n";
            $start_monday = (new DateTime($time_stamp))->sub(new DateInterval('P5D'))->format('Y-m-d');
            $end_monday = (new DateTime($time_stamp))->add(new DateInterval('P2D'))->format('Y-m-d');
        }else if(date('D',$timestamp) === 'Sun'){ 
            // echo "It is Sunday today\n";
            $start_monday = (new DateTime($time_stamp))->sub(new DateInterval('P6D'))->format('Y-m-d');
            $end_monday = (new DateTime($time_stamp))->add(new DateInterval('P1D'))->format('Y-m-d');
        }
        
        $this->start_monday = $start_monday;
        $this->end_monday = $end_monday;
        
    }
    function get_start() {
        return $this->start_monday;
    }
    function get_end() {
        return $this->end_monday;
    }
}

// date_default_timezone_set('Asia/Bangkok');
// $time_stamp = date("Y-m-d H:i:s");
// // echo date('l');

// $time = new week();
// $time->set_day($time_stamp);

// echo $time->get_start();
// echo $time->get_end();
?>
