<?php

class Data extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // Row number collection method:
    public function getRowsCount()
    {
        $query = $this->db->select('COUNT(*) as Num')->from('T_PHONE_RECORDS')->get();
        
        $count = $query->row()->Num;
        
        return $count;
    }
    
    // First 10 records collection method:
    public function getRecords()
    {
        $query = $this->db->limit(10)
                ->select('CALLER, RECORD_EVENT_ID, RECORD_DATE, RECIEVER')
                ->from('T_PHONE_RECORDS')
                ->get();
        
        return $query;
    }
    
    // Data searching method:
    public function getSearchValue($search)
    {
        $query = $this->db->select('e.EVENT_NAME, r.RECORD_DATE, r.CALLER, r.RECIEVER')
                ->from('T_PHONE_RECORDS r')
                ->join('T_EVENT_TYPE e', 'r.RECORD_EVENT_ID = e.EVENT_ID', 'inner')
                ->like('r.RECORD_ID', $search)
                ->or_like('e.EVENT_NAME', $search)
                ->or_like('r.RECORD_DATE', $search)
                ->or_like('r.CALLER', $search)
                ->or_like('r.RECIEVER', $search)
                ->get();
        
        return $query;
    }
    
    // Column order setting method:
    public function setColumnOrder($column, $order, $length, $start)
    {
        $query = $this->db->select('e.EVENT_NAME, r.RECORD_DATE, r.CALLER, r.RECIEVER')
                ->from('T_PHONE_RECORDS r')
                ->join('T_EVENT_TYPE e', 'r.RECORD_EVENT_ID = e.EVENT_ID', 'inner')
                ->order_by($column, $order)
                ->limit($length, $start)
                ->get();                    
        
        return $query;
    }
    
    // Call data collection method:
    public function getCallData($caller, $reciever)
    {
        $query = $this->db->select('e.EVENT_NAME, r.RECORD_DATE, r.CALLER, r.RECIEVER')
                ->from('T_PHONE_RECORDS r')
                ->join('T_EVENT_TYPE e', 'r.RECORD_EVENT_ID = e.EVENT_ID', 'inner')
                ->where('r.CALLER', $caller)
                ->where('r.RECIEVER', $reciever)
                ->order_by('r.RECORD_DATE')
                ->get();
        
        return $query;
    }
    
    // Extended call log collection method:
    public function getExtendedCallData($caller)
    {
        $query = $this->db->select('RECORD_EVENT_ID, RECORD_DATE, CALLER, RECIEVER')
                ->from('T_PHONE_RECORDS')
                ->where('CALLER', $caller)
                ->where('RECORD_EVENT_ID', 'EVENT_PICK_UP')
                ->get();
        
        return $query;
    }
    
    // Talk duration counting method:
    public function getTalkDuration($caller, $reciever)
    {
        $events = array('EVENT_PICK_UP', 'EVENT_HANG_UP');
        
        $query = $this->db->select('RECORD_DATE')
                ->from('T_PHONE_RECORDS')
                ->where('CALLER', $caller)
                ->where('RECIEVER', $reciever)
                ->where_in('RECORD_EVENT_ID', $events)->get();
        
        $call_start = $query->first_row()->RECORD_DATE;
        $call_end = $query->last_row()->RECORD_DATE;
        
        // Count talk duration in minutes:
        $count = gmdate('H:i:s', strtotime($call_end) - strtotime($call_start));
        
        return $count;
    }
    
    // Generate new data for the table:
    public function genTable($quantity)
    {
        // Delete old table:
        $this->db->query('DROP TABLE T_PHONE_RECORDS');
        
        // Create new table:
        $this->db->query(
                'CREATE TABLE IF NOT EXISTS T_PHONE_RECORDS (
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
            $caller = '5' . rand(1000000,9999999);
            $reciever = '5' . rand(1000000,9999999);
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
    private function tableInsert($event, $date, $caller, $reciever)
    {
        $data = array(
            'RECORD_EVENT_ID' => $event,
            'RECORD_DATE' => $date,
            'CALLER' => $caller,
            'RECIEVER' => $reciever
        );
        
        $this->db->insert('T_PHONE_RECORDS', $data);
    }
    
    // Time addition method:
    private function genDateTime($date, $addition)
    {
        $date->add(new DateInterval($addition));
        return $date->format('Y-m-d H:i:s');
    }
}
