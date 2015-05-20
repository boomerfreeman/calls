<?php

class Data extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function generate()
    {
        // Delete old table
        $this->db->query('DROP TABLE T_PHONE_RECORDS');
        
        // Create new table
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
        for ($i=0; $i < 5; $i++) {
            $caller = '5' . rand(1000000, 9999999);
            $reciever = '5' . rand(1000000, 9999999);
            $time = date("Y-m-d H:i:s");
            
            // Caller picks up the phone:
            $this->dbInsert('EVENT_PICK_UP', $time, $caller, $reciever);
            
            $dial = $boolDial[rand(0, 1)];
            
            if ($dial === false) {
                $this->dbInsert('EVENT_HANG_UP', $time, $caller, '');
            } else {
                $this->dbInsert('EVENT_DIAL', $time, $caller, $reciever);
                
                $answer = $boolAnswer[rand(0, 1)];
                
                if ($answer === false) {
                    $this->dbInsert('EVENT_CALL_END', $time, $caller, $reciever);
                } else {
                    $this->dbInsert('EVENT_CALL_ESTABLISHED', $time, $caller, $reciever);
                    $this->dbInsert('EVENT_CALL_END', $time, $caller, $reciever);
                }
                $this->dbInsert('EVENT_HANG_UP', $time, $caller, $reciever);
            }
        }
    }
    
    private function dbInsert($event, $time, $caller, $reciever)
    {
        $values = array (
            'RECORD_EVENT_ID' => $event,
            'RECORD_DATE' => $time,
            'CALLER' => $caller,
            'RECIEVER' => $reciever
        );
        
        $this->db->insert('T_PHONE_RECORDS', $values);
    }
    
    public function show()
    {
        $query = $this->db->get('T_PHONE_RECORDS');
        return $query->result();
    }
    
    public function sortNumber($number, $order)
    {
        $query = $this->db->from('T_PHONE_RECORDS')->order_by($number, $order);
        $query = $this->db->get();
        
        return $query->result();
    }
}
