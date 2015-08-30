<?php

class Data extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // Generate new data for the table:
    public function genTable($quantity)
    {
        // Delete old table:
        $this->db->query('DROP TABLE T_PHONE_RECORDS');
        
        // Create new table:
        $this->db->query(
                'CREATE TABLE T_PHONE_RECORDS (
                    RECORD_ID int(11) NOT NULL AUTO_INCREMENT,
                    RECORD_EVENT_ID varchar(22) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                    RECORD_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CALLER varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                    RECIEVER varchar(8) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                    PRIMARY KEY (RECORD_ID),
                    FOREIGN KEY (RECORD_EVENT_ID) REFERENCES T_EVENT_TYPE (EVENT_ID) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB'
        );
        
        // Generate logs:
        $this->genLogs($quantity);
    }
    
    // Main log generation method:
    private function genLogs($num)
    {
        // Arrays for randomization calling logs:
        $boolDial = array (true, false);
        $boolAnswer = array (true, false);
        
        // Generate data for 100 different numbers:
        for ($i=0; $i < $num; $i++) {
            
            // Randomize caller, reciever numbers and date:
            $caller = '555555' . rand(10, 99);
            $reciever = '555555' . rand(10, 99);
            $time = 'Y-m-' .rand(1, 30). ' ' .rand(1, 24). ':' .rand(0, 60). ':' . rand(0, 60);
            
            // Caller picks up the phone:
            $this->tableInsert('EVENT_PICK_UP', date($time), $caller, $reciever);
            
            // Randomize dial event:
            $dial = $boolDial[rand(0, 1)];
            
            // Caller tries to dial with reciever:
            if ($dial === false) {
                
                // If call is not dialled, send proper event without reciever number:
                $this->tableInsert('EVENT_HANG_UP', date($time, strtotime('+'.rand(10, 20).' seconds')), $caller, '');
                
            } else {
                
                // Otherwise dialling is started:
                $this->tableInsert('EVENT_DIAL', date($time, strtotime('+'.rand(10, 20).' seconds')), $caller, $reciever);
                
                // Randomize answer event:
                $answer = $boolAnswer[rand(0, 1)];
                
                // Caller waits for the answer:
                if ($answer === false) {
                    
                    // If reciever did not answer, send proper event:
                    $this->tableInsert('EVENT_CALL_END', date($time, strtotime('+'.rand(20, 60).' seconds')), $caller, $reciever);
                    
                } else {
                    
                    // Otherwise randomize talking duration:
                    $this->tableInsert('EVENT_CALL_ESTABLISHED', date($time, strtotime('+'.rand(20, 60).' seconds')), $caller, $reciever);
                    $this->tableInsert('EVENT_CALL_END', date($time, strtotime('+'.rand(1, 5).' minutes')), $caller, $reciever);
                }
                
                // Caller hangs up the phone:
                $this->tableInsert('EVENT_HANG_UP', date($time, strtotime('+5 minutes')), $caller, $reciever);
            }
        }
    }
    
    // Table fields filling method:
    private function tableInsert($event, $time, $caller, $reciever)
    {
        $sql = "INSERT INTO T_PHONE_RECORDS (RECORD_EVENT_ID, RECORD_DATE, CALLER, RECIEVER) 
                VALUES (?, ?, ?, ?)";
        $this->db->query($sql, array($event, $time, $caller, $reciever));
    }
}
