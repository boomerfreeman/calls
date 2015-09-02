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
            $caller = '555555' . rand(10,99);
            $reciever = '555555' . rand(10,99);
            $date = new DateTime('2015-01-' . rand(1,30) . ' ' . rand(0,24) . ':' . rand(0,59) . ':' . rand(0,59));
            
            // Caller picks up the phone:
            $this->tableInsert('EVENT_PICK_UP', $date->format('Y-m-d H:i:s'), $caller, $reciever);
            
            // Randomize dial event:
            $dial = $boolDial[rand(0,1)];
            
            // Caller tries to dial with reciever:
            if ($dial === false) {
                
                // If call is not dialled, send proper event without reciever number:
                $this->tableInsert('EVENT_HANG_UP', $this->genDateTime($date, 'PT' . rand(10,20) . 'S'), $caller, '');
                
            } else {
                
                // Otherwise dialling is started:
                $this->tableInsert('EVENT_DIAL', $this->genDateTime($date, 'PT' . rand(10,20) . 'S'), $caller, $reciever);
                
                // Randomize answer event:
                $answer = $boolAnswer[rand(0, 1)];
                
                // Caller waits for the answer:
                if ($answer === false) {
                    
                    // If reciever did not answer, send proper event:
                    $this->tableInsert('EVENT_CALL_END', $this->genDateTime($date, 'PT' . rand(20,30) . 'S'), $caller, $reciever);
                    
                } else {
                    
                    // Otherwise randomize talking duration:
                    $this->tableInsert('EVENT_CALL_ESTABLISHED', $this->genDateTime($date, 'PT' . rand(20,59) . 'S'), $caller, $reciever);
                    $this->tableInsert('EVENT_CALL_END', $this->genDateTime($date, 'PT' . rand(19,20) . 'M'), $caller, $reciever);
                }
                
                // Caller hangs up the phone:
                $this->tableInsert('EVENT_HANG_UP', $this->genDateTime($date, 'PT' . rand(1,15) . 'S'), $caller, $reciever);
            }
        }
    }
    
    // Table fields filling method:
    private function tableInsert($event, $time, $caller, $reciever)
    {
        $sql = 'INSERT INTO T_PHONE_RECORDS (RECORD_EVENT_ID, RECORD_DATE, CALLER, RECIEVER) 
                VALUES (?, ?, ?, ?)';
        $this->db->query($sql, array($event, $time, $caller, $reciever));
    }
    
    // Time addition method:
    private function genDateTime($date, $addition)
    {
        $date->add(new DateInterval($addition));
        return $date->format('Y-m-d H:i:s');
    }
}
