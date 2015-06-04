<?php

class Log extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->library('Datatables');
        $this->load->library('Table');
        $this->load->helper('url');
        $this->load->model('Data');
    }
    
    function index()
    {
        $this->Data->dbGen();
        
        // Create table template:
        $tmpl = array (
            'table_open'            => '<table class="table table-striped table-hover" id="big_table">',
            'heading_row_start'     => '<tr>',
            'heading_row_end'       => '</tr>',
            'heading_cell_start'    => '<th>',
            'heading_cell_end'      => '</th>',
            
            'row_start'             => '<tr>',
            'row_end'               => '</tr>',
            'cell_start'            => '<td>',
            'cell_end'              => '</td>',
            
            'table_close'           => '</table>'
        );
        
        // Set table data:
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('ID', 'Event', 'Timestamp', 'Caller', 'Reciever');
        
        $data['title'] = 'Calls log';
        // Generate table:
        $data['table'] = $this->table->generate();
        
        // Load view with $data array:
        $this->load->view('view', $data);
    }
    
    //function to handle callbacks
    function datatable()
    {
        $this->datatables->select('RECORD_ID, RECORD_EVENT_ID, RECORD_DATE, CALLER, RECIEVER')->from('T_PHONE_RECORDS');
        
        echo $this->datatables->generate();
    }
}
