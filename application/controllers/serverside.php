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
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS WHERE RECORD_ID LIKE '%$search%' OR RECORD_EVENT_ID LIKE '%$search%' OR RECORD_DATE LIKE '%$search%' OR CALLER LIKE '%$search%' OR RECIEVER LIKE '%$search%'");
        } else {
            // SQL query to set order:
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS ORDER BY $column $order LIMIT $start,$length");
        }
        
        $limit = $query->num_rows;
        
        // If DB contains some strings:
        if ($limit > 0)
        {
            foreach ($query->result() as $row)
            {
                $record_id   = $row->RECORD_ID;
                $event_id    = $row->RECORD_EVENT_ID;
                $record_date = $row->RECORD_DATE;
                $caller      = $row->CALLER;
                $reciever    = $row->RECIEVER;
                
                $data[] = array($record_id, $event_id, $record_date, $caller, $reciever);
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
        // Recieve caller number:
        $caller = htmlspecialchars($_GET['caller']);
        
        $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS WHERE CALLER = $caller");
        
        foreach ($query->result() as $row)
        {
            $record_id   = $row->RECORD_ID;
            $event_id    = $row->RECORD_EVENT_ID;
            $record_date = $row->RECORD_DATE;
            $caller      = $row->CALLER;
            $reciever    = $row->RECIEVER;
            
            $data[] = array($record_id, $event_id, $record_date, $caller, $reciever);
        }
        
        // Create json object:
        $json = array("data" => $data);
        
        echo json_encode($json);
        exit;
    }
}
