<?php

class Serverside extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // Serverside operations control method:
    public function datatables()
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
            foreach ($query->result() as $row)
            {
                $data[] = array($row->CALLER, $row->RECORD_EVENT_ID, $row->RECIEVER, $row->RECORD_DATE);
            }
            
            $this->sendServerJSON($draw, $rows, $data);
            
        } else {
            
            // Send empty response if database has no data:
            $this->sendServerJSON($draw, $rows, false);
        }
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
    
    // Modal window operations response method:
    public function modal()
    {
        // Retrieve caller and reciever numbers:
        $table_caller = htmlspecialchars($_GET['caller']);
        
        // Get call log for the caller and check how much calls were made:
        $sql = "SELECT CALLER, RECORD_EVENT_ID, RECIEVER, RECORD_DATE 
                FROM T_PHONE_RECORDS 
                WHERE CALLER = ? AND RECORD_EVENT_ID = 'EVENT_PICK_UP'";
        
        $query = $this->db->query($sql, $table_caller);
        $call_num = $query->num_rows;
        
        // If caller made more than one call:
        if ($call_num > 1) {
            
            // Get data for every call:
            foreach ($query->result() as $row)
            {
                $call_data = $this->getCallData($table_caller, $row->RECIEVER);
                
                $data[] = array($call_data[0], $row->CALLER, $row->RECORD_EVENT_ID, $row->RECIEVER, $row->RECORD_DATE);
            }
            
            // Send json object:
            $this->sendModalJSON($data);
            
        } else {
            
            // Otherwise get info about the call:
            $title = $this->callResolution($call_num, $table_caller);
            
            // Collect call data:
            $row = $query->row();
            
            $data[] = array($title, $row->CALLER, $row->RECORD_EVENT_ID, $row->RECIEVER, $row->RECORD_DATE);
            
            // Send json object:
            $this->sendModalJSON($data);
        }
    }
    
    // Call data collection method:
    private function getCallData($caller, $reciever)
    {
        $sql = "SELECT CALLER, RECORD_EVENT_ID, RECIEVER, RECORD_DATE 
                FROM T_PHONE_RECORDS 
                WHERE CALLER = ? AND RECIEVER = ?";
        
        $query = $this->db->query($sql, array($caller, $reciever));
        $call_num = $query->num_rows;
        
        // Set call resolution for every call:
        $title = $this->callResolution($call_num, $caller);
        
        $result = array($title, $query);
        return $result;
    }
    
    // Call resolution definition method:
    private function callResolution($num, $caller)
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
    
    // Send JSON object method (for modal window):
    private function sendModalJSON($data)
    {
        $json = array("data" => $data);
        echo json_encode($json);
        exit;
    }
}
