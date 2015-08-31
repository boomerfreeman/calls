<?php

class Serverside extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // Main log processing method:
    public function mainLog()
    {
        $draw = htmlspecialchars($_GET['draw']);
        $length = htmlspecialchars($_GET['length']);
        $start = htmlspecialchars($_GET['start']);
        $order = htmlspecialchars($_GET['order']['0']['dir']);
        $column = htmlspecialchars($_GET['order']['0']['column']);
        $search = htmlspecialchars($_GET['search']['value']);
        
        // Get number of all rows in the table:
        $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS");
        $rows = $query->num_rows;
        
        // Define which column is set to change order:
        switch ($column) {
            case '0':
                $column = 'RECORD_ID'; break;
            case '1':
                $column = 'RECORD_EVENT_ID'; break;
            case '2':
                $column = 'RECORD_DATE'; break;
            case '3':
                $column = 'CALLER'; break;
            case '4':
                $column = 'RECIEVER'; break;
            default:
                $column = 'RECORD_ID';
        }
        
        // If search form is active:
        if ( ! empty ($search))
        {
            // Search data in the table:
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS 
                                       WHERE RECORD_ID LIKE '%$search%' 
                                       OR RECORD_EVENT_ID LIKE '%$search%' 
                                       OR RECORD_DATE LIKE '%$search%' 
                                       OR CALLER LIKE '%$search%' 
                                       OR RECIEVER LIKE '%$search%'");
        } else {
            
            // Otherwise send query to set order:
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS 
                                       ORDER BY $column $order 
                                       LIMIT $start,$length");
        }
        
        $limit = $query->num_rows;
        
        // If Database is not empty:
        if ($limit > 0)
        {
            // Retrieve and send call log:
            foreach ($query->result() as $row) {
                
                $caller = $row->CALLER;
                $event = $this->getCallEvent($row->RECORD_EVENT_ID);
                $reciever = $row->RECIEVER;
                $date = date("d.m.Y H:i:s", strtotime($row->RECORD_DATE));
                
                $data[] = array($caller, $event, $reciever, $date);
            }
            
            $this->sendServerJSON($draw, $rows, $data);
            
        } else {
            
            // Send empty response if database has no data:
            $this->sendServerJSON($draw, $rows, false);
        }
    }
    
    // Modal window log processing method:
    public function modalLog($table_caller, $table_reciever)
    {
        // Get call log for the caller and check how much calls were made:
        $call_data = $this->getCallData($table_caller, $table_reciever);
        
        // Get data for every call:
        foreach ($call_data[1]->result() as $row) {
            
            $title = $call_data[0];
            $caller = $row->CALLER;
            $event = $this->getCallEvent($row->RECORD_EVENT_ID);
            $reciever = $row->RECIEVER;
            $date = date("d.m.Y H:i:s", strtotime($row->RECORD_DATE));
            
            $data[] = array($title, $caller, $event, $reciever, $date);
        }
        
        // Send json object:
        $this->sendModalJSON($data);
    }
    
    // Extended modal window log processing method:
    public function extendLog($modal_caller, $modal_reciever)
    {
        // Get call log for the caller and check how much calls were made:
        $call_data = $this->getExtendedCallData($modal_caller, $modal_reciever);
        
        // If caller made more than one call:
        if ($call_data[2] > 1) {
            
            // Get data for every call:
            foreach ($call_data[1]->result() as $row) {
                
                $reciever = $row->RECIEVER;
                $call_data = $this->getCallData($modal_caller, $reciever);
                
                $date = date("d.m.Y H:i:s", strtotime($row->RECORD_DATE));
                $duration = '15:00';
                $title = $call_data[0];
                
                $data[] = array($date, $duration, $reciever, $title);
            }
            
        } else {
            
            // Otherwise set call data:
            $row = $call_data[1]->row();
            
            $date = date("d.m.Y H:i:s", strtotime($row->RECORD_DATE));
            $duration = '10:00';
            $reciever = $row->RECIEVER;
            $title = $call_data[0];
            
            $data[] = array($date, $duration, $reciever, $title);
        }
        
        // Send json object:
        $this->sendModalJSON($data);
    }
    
    // Call data collection method:
    private function getCallData($caller, $reciever)
    {
        // If call log does not include reciever number:
        if ($reciever == 'null') {
            $reciever = false;
        }
        
        $sql = "SELECT CALLER, RECORD_EVENT_ID, RECIEVER, RECORD_DATE 
                FROM T_PHONE_RECORDS 
                WHERE CALLER = ? AND RECIEVER = ?";
        
        $query = $this->db->query($sql, array($caller, $reciever));
        
        $call_num = $query->num_rows;
        
        // Set title for every call:
        $title = $this->getCallTitle($call_num, $caller);
        
        return array($title, $query);
    }
    
    // Extended call log collection method:
    private function getExtendedCallData($caller, $reciever)
    {
        $sql = "SELECT CALLER, RECORD_EVENT_ID, RECIEVER, RECORD_DATE 
                FROM T_PHONE_RECORDS 
                WHERE CALLER = ? AND RECORD_EVENT_ID = 'EVENT_PICK_UP'
                ORDER BY RECORD_DATE DESC";
        
        $query = $this->db->query($sql, array($caller, $reciever));
        $call_num = $query->num_rows;
        
        // Set call resolution for every call:
        $title = $this->getCallTitle($call_num, $caller);
        
        // Return title, data and number of calls:
        return array($title, $query, $call_num);
    }
    
    // Call resolution definition method:
    private function getCallTitle($num, $caller)
    {
        switch ($num) {
            case '2':
                $title = "$caller: Cancelled call"; break;
            case '4':
                $title = "$caller: Non-dialled call"; break;
            case '5':
                $title = "$caller: Regular call"; break;
            default:
                $title = "$caller: Cancelled call";
        }        
        return $title;
    }
    
    // Call event type definition method:
    private function getCallEvent($event)
    {
        switch ($event) {
            case 'EVENT_PICK_UP': $event = 'Pick-up'; break;
            case 'EVENT_DIAL': $event = 'Dialling'; break;
            case 'EVENT_CALL_ESTABLISHED': $event = 'Call Established'; break;
            case 'EVENT_CALL_END': $event = 'Call End'; break;
            case 'EVENT_HANG_UP': $event = 'Hang-up'; break;
            default: $event = 'Hang-up';
        }        
        return $event;
    }
    
    // Send JSON object method (for main table):
    private function sendServerJSON($draw, $rows, $data)
    {
        $json = array("draw" => $draw,
                      "recordsTotal" => $rows,
                      "recordsFiltered" => $rows,
                      "data" => $data);
        
        echo json_encode($json);
        exit;
    }
    
    // Send JSON object method (for modal window):
    private function sendModalJSON($data)
    {
        $json = array("data" => $data);
        echo json_encode($json);
        exit;
    }
}
