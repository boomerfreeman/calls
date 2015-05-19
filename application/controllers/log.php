<?php

class Log extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Data');
    }
    
    public function index()
    {
        // Start session:
        session_start();
//        unset($_SESSION);
//        session_destroy();
//        session_write_close();
        
        if (! isset($_SESSION)) {
            $this->Data->generate();
        }
        
        echo session_id();
        
        $data['genData'] = $this->Data->show();
        //$data['showData'] = array('Caller', 'Event', 'Reciever', 'Timestamp');
        $data['dbData'] = array('CALLER', 'RECORD_EVENT_ID', 'RECIEVER', 'RECORD_DATE');
        
        $data['title'] = 'Logs';
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/view', $data);
        $this->load->view('templates/footer');
    }
    
    public function sort()
    {
        session_start();
        
        if (! isset($_SESSION['order'])) {
            $_SESSION['order'] = 'ASC';
        } elseif ($_SESSION['order'] == 'DESC') {
            $_SESSION['order'] = 'ASC';
        } else {
            $_SESSION['order'] = 'DESC';
        }
        
        $order = $_SESSION['order'];
        
        $data['genData'] = $this->Data->sortCaller($order);
        $data['dbData'] = array('CALLER', 'RECORD_EVENT_ID', 'RECIEVER', 'RECORD_DATE');
        
        $data['title'] = 'Logs';
        
        $this->load->view('templates/header', $data);
        $this->load->view('pages/view', $data);
        $this->load->view('templates/footer');
    }
}
