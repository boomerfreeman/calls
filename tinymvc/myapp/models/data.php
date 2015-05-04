<?php

date_default_timezone_set("Europe/Tallinn");

class Data extends TinyMVC_Model
{
    function generateData()
    {
        // Delete old table
        $this->db->query('DROP TABLE T_PHONE_RECORDS');
        
        // Create new table
        $this->db->query('CREATE TABLE T_PHONE_RECORDS (
                            RECORD_ID int(11) NOT NULL AUTO_INCREMENT,
                            RECORD_EVENT_ID varchar(22) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                            RECORD_DATE timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            CALLER int(11) NOT NULL,
                            RECIEVER int(11) DEFAULT NULL,
                            PRIMARY KEY (RECORD_ID),
                            FOREIGN KEY (RECORD_EVENT_ID) REFERENCES T_EVENT_TYPE (EVENT_ID) ON DELETE CASCADE ON UPDATE CASCADE
                        ) ENGINE=InnoDB;');
        
        // Generate new table data:
        for ($i=1; $i < 11; $i++) {
            $events = array ('EVENT_CALL_END', 'EVENT_CALL_ESTABLISHED', 'EVENT_DIAL', 'EVENT_HANG_UP', 'EVENT_PICK_UP');
            //$caller = '5' . rand(1000000, 9999999);
            //$reciever = '5' . rand(1000000, 9999999);
            $caller = '5855660' . rand(0, 9);
            $reciever = '5855660' . rand(0, 9);
            $event = $events[rand(0, 4)];
            $time = date("Y-m-d H:i:s");
            
            $this->db->query("INSERT INTO T_PHONE_RECORDS (RECORD_ID, RECORD_EVENT_ID, RECORD_DATE, CALLER, RECIEVER) VALUES ($i, '$event', '$time', '$caller', '$reciever')");
        }
    }
    
    function getData()
    {
        return $this->db->query_all('SELECT * FROM T_PHONE_RECORDS');
    }
}