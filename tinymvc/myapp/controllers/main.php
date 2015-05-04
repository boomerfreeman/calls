<?php

class Main_Controller extends TinyMVC_Controller
{
    function index()
    {
        // Load model:
        $this->load->model('Data', 'page');
        
        // Generate data:
        $this->page->generateData();
        $data = $this->page->getData();
        
        // Load view:
        $this->view->assign('data', $data);
        
        $lang = array ( 'title' => 'Test',
                        'caller' => 'Caller',
                        'event' => 'Event',
                        'reciever' => 'Reciever',
                        'time' => 'Timestamp' );
        
        $this->view->assign($lang);
        
        // Display template:
        $this->view->display('log');
    }
}
