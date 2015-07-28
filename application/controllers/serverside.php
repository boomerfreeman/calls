<?php

class Serverside extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function index()
    {
        $draw = htmlspecialchars($_GET['draw']);
        $length = htmlspecialchars($_GET['length']);
        $order = htmlspecialchars($_GET['order']['0']['dir']);
        $column = htmlspecialchars($_GET['order']['0']['column']);
        $search = htmlspecialchars($_GET['search']['value']);
        
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
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS WHERE CALLER LIKE '$search%' OR RECIEVER LIKE '$search%'");
        } else {
            $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS ORDER BY $column $order LIMIT $length OFFSET 0");
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
            
            $jsonData = array(
                "draw" => $draw,
                "recordsTotal" => $limit,
                "recordsFiltered" => $length,
                "data" => $data
            );
            
            echo json_encode($jsonData);
            
        // If DB is empty:    
        } else {
            
            $jsonData = array(
                "draw" => $draw,
                "recordsTotal" => $limit,
                "recordsFiltered" => $length,
                "data" => false
            );
            
            // Send empty JSON response:
            echo json_encode($jsonData);
        }
    }
}
