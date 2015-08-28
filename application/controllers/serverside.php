<?php

class Serverside extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    // A method that controlls serverside operations with datatables plugin:
    public function datatables()
    {
        $draw = htmlspecialchars($_GET['draw']);
        $length = htmlspecialchars($_GET['length']);
        $start = htmlspecialchars($_GET['start']);
        $order = htmlspecialchars($_GET['order']['0']['dir']);
        $column = htmlspecialchars($_GET['order']['0']['column']);
        $search = htmlspecialchars($_GET['search']['value']);
        
        // Number of all rows in the table:
        $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS");
        $rows = $query->num_rows;
        
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
        
        // UI search is active:
        if ( ! empty ($search))
        {
            // SQL query to search number:
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS 
                                       WHERE RECORD_ID LIKE '%$search%' 
                                       OR RECORD_EVENT_ID LIKE '%$search%' 
                                       OR RECORD_DATE LIKE '%$search%' 
                                       OR CALLER LIKE '%$search%' 
                                       OR RECIEVER LIKE '%$search%'");
        } else {
            // SQL query to set order:
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS 
                                       ORDER BY $column $order 
                                       LIMIT $start,$length");
        }
        
        $limit = $query->num_rows;
        
        // If DB contains some strings:
        if ($limit > 0)
        {
            foreach ($query->result() as $row)
            {
                $caller         = $row->CALLER;
                $event_id       = $row->RECORD_EVENT_ID;
                $reciever       = $row->RECIEVER;
                $record_date    = $row->RECORD_DATE;
                
                $data[] = array($caller, $event_id, $reciever, $record_date);
            }
            
            $json = array(
                "draw" => $draw,
                "recordsTotal" => $rows,
                "recordsFiltered" => $rows,
                "data" => $data
            );
            
            echo json_encode($json);
            exit;
            
        // If DB is empty:    
        } else {
            
            $json = array(
                "draw" => $draw,
                "recordsTotal" => $rows,
                "recordsFiltered" => $rows,
                "data" => false
            );
            
            // Send empty JSON response:
            echo json_encode($json);
            exit;
        }
    }
    
    // A method that response for modal window operations:
    public function modal()
    {
        // Retrieve caller and reciever numbers:
        $table_caller = htmlspecialchars($_GET['caller']);
        $table_reciever = htmlspecialchars($_GET['reciever']);
        
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
            
            // Create data and send json object:
            $this->sendJSON($data);
            
        } else {
            
            // Otherwise get info about the call:
            $sql = "SELECT CALLER, RECORD_EVENT_ID, RECIEVER, RECORD_DATE 
                    FROM T_PHONE_RECORDS 
                    WHERE CALLER = ? AND RECIEVER = ?";
        
            $query = $this->db->query($sql, array($table_caller, $table_reciever));
            
            // Define call resolution for modal window:
            $call_num = $query->num_rows;
            
            $title = $this->callResolution($call_num, $table_caller);
            
            // Collect call data:
            $row = $query->row();
            
            $data[] = array($title, $row->CALLER, $row->RECORD_EVENT_ID, $row->RECIEVER, $row->RECORD_DATE);
            
            // Send json object:
            $this->sendJSON($data);
        }
    }
    
    // Get call data function:
    private function getCallData($table_caller, $table_reciever)
    {
        $sql = "SELECT CALLER, RECORD_EVENT_ID, RECIEVER, RECORD_DATE 
                FROM T_PHONE_RECORDS 
                WHERE CALLER = ? AND RECIEVER = ?";
        
        $query = $this->db->query($sql, array($table_caller, $table_reciever));
        $call_num = $query->num_rows;
        
        // Set call resolution for every call:
        $title = $this->callResolution($call_num, $table_caller);
        
        $result = array($title, $query);
        return $result;
    }
    
    // Call resolution function:
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
    
    // Send JSON object function:
    private function sendJSON($data)
    {
        $json = array("data" => $data);
        echo json_encode($json);
        exit;
    }
}
