<?php

class Serverside extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Data');
    }
    
    // Main log processing method:
    public function mainLog()
    {
        $draw = (int)htmlspecialchars($_GET['draw']);
        $length = (int)htmlspecialchars($_GET['length']);
        $start = (int)htmlspecialchars($_GET['start']);
        $order = (string)htmlspecialchars($_GET['order']['0']['dir']);
        $column = (int)htmlspecialchars($_GET['order']['0']['column']);
        $search = (string)htmlspecialchars($_GET['search']['value']);
        
        // Get number of all rows in the table:
        $rows = $this->Data->getRowsCount();
        
        // If search form is active:
        if ( ! empty ($search)) {
            
            // Search data in the table:
            $query = $this->Data->getSearchValue($search);
            
        } else {
            
            // Define which column is set to change order:
            switch ($column) {
                case '0':
                    $column = 'CALLER'; break;
                case '1':
                    $column = 'EVENT_NAME'; break;
                case '2':
                    $column = 'RECIEVER'; break;
                case '3':
                    $column = 'RECORD_DATE'; break;
            }
            
            // Otherwise send query to set order:
            $query = $this->Data->setColumnOrder($column, $order, $length, $start);
        }
        
        $limit = $query->num_rows;
        
        // If Database is not empty:
        if ($limit > 0) {
            
            // Retrieve and send call log:
            foreach ($query->result() as $row) {
                
                $caller = $row->CALLER;
                $event = $row->EVENT_NAME;
                $reciever = $row->RECIEVER;
                $date = date('d/m/Y H:i:s', strtotime($row->RECORD_DATE));
                
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
        // If call log does not include reciever number:
        if ($table_reciever == 'null') {
            $table_reciever = false;
        }
        
        // Get call log for the caller and check how much calls were made:
        $query = $this->Data->getCallData($table_caller, $table_reciever);
        
        // Get data for every call:
        foreach ($query->result() as $row) {
            
            $caller = $row->CALLER;
            $event = $row->EVENT_NAME;
            $reciever = $row->RECIEVER;
            $date = date('d/m/Y H:i:s', strtotime($row->RECORD_DATE));
            
            $data[] = array($caller, $event, $reciever, $date);
        }
        
        // Send json object:
        $this->sendModalJSON($data);
    }
    
    // Extended modal window log processing method:
    public function extendLog($modal_caller)
    {
        // Get call log for the caller and check how much calls were made:
        $query = $this->Data->getExtendedCallData($modal_caller);
        $rows = $query->num_rows;
        
        // If caller made more than one call:
        if ($rows > 1) {
            
            // Get data for every call:
            foreach ($query->result() as $row) {
                
                $reciever = $row->RECIEVER;
                $query = $this->Data->getCallData($modal_caller, $reciever);
                
                $date = date('d/m/Y H:i:s', strtotime($row->RECORD_DATE));
                $duration = $this->Data->getTalkDuration($row->CALLER, $row->RECIEVER);
                $title = $this->getCallTitle($query->num_rows);
                
                $data[] = array($date, $duration, $reciever, $title);
            }
            
        } else {
            
            // Otherwise set call data:
            $row = $query->row();
            
            $date = date('d/m/Y H:i:s', strtotime($row->RECORD_DATE));
            $duration = $this->Data->getTalkDuration($row->CALLER, $row->RECIEVER);
            $reciever = $row->RECIEVER;
            $title = $this->getCallTitle($query->num_rows);
            
            $data[] = array($date, $duration, $reciever, $title);
        }
        
        // Send json object:
        $this->sendModalJSON($data);
    }
    
    // Call title setting method:
    private function getCallTitle($num)
    {
        switch ($num) {
            case '1':
                $title = "Cancelled call"; break;
            case '4':
                $title = "Non-dialled call"; break;
            case '5':
                $title = "Regular call"; break;
            default:
                $title = "Cancelled call";
        }        
        return $title;
    }
    
    // Send JSON object method (for main table):
    private function sendServerJSON($draw, $rows, $data)
    {
        $json = array('draw' => $draw,
                      'recordsTotal' => $rows,
                      'recordsFiltered' => $rows,
                      'data' => $data);
        
        echo json_encode($json);
        exit;
    }
    
    // Send JSON object method (for modal window):
    private function sendModalJSON($data)
    {
        $json = array('data' => $data);
        echo json_encode($json);
        exit;
    }
}
