<?php

class Data extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // Generate new data for the table:
    public function tableGenerate()
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
        
        $boolDial = array (true, false);
        $boolAnswer = array (true, false);
        
        // Generate data for 5 different numbers:
        for ($i=0; $i < 50; $i++) {
            $caller = '555555' . rand(10, 99);
            $reciever = '555555' . rand(10, 99);
            
            // Caller picks up the phone:
            $this->tableInsert('EVENT_PICK_UP', date('Y-m-d H:i:s'), $caller, $reciever);
            
            $dial = $boolDial[rand(0, 1)];
            
            if ($dial === false) {
                $this->tableInsert('EVENT_HANG_UP', date('Y-m-d H:i:s', strtotime('+3 seconds')), $caller, '');
            } else {
                $this->tableInsert('EVENT_DIAL', date('Y-m-d H:i:s', strtotime('+10 seconds')), $caller, $reciever);
                
                $answer = $boolAnswer[rand(0, 1)];
                
                if ($answer === false) {
                    $this->tableInsert('EVENT_CALL_END', date('Y-m-d H:i:s', strtotime('+2 minutes')), $caller, $reciever);
                } else {
                    $this->tableInsert('EVENT_CALL_ESTABLISHED', date('Y-m-d H:i:s', strtotime('+1 minutes')), $caller, $reciever);
                    $this->tableInsert('EVENT_CALL_END', date('Y-m-d H:i:s', strtotime('+15 minutes')), $caller, $reciever);
                }
                $this->tableInsert('EVENT_HANG_UP', date('Y-m-d H:i:s', strtotime('+15 minutes +3 seconds')), $caller, $reciever);
            }
        }
    }
    
    private function tableInsert($event, $time, $caller, $reciever)
    {
        $sql = "INSERT INTO T_PHONE_RECORDS (RECORD_EVENT_ID, RECORD_DATE, CALLER, RECIEVER) VALUES (?, ?, ?, ?)";
        $this->db->query($sql, array($event, $time, $caller, $reciever));
    }
}
