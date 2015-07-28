<?php

class Log extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->library('Table');
        $this->load->helper('url');
        $this->load->model('Data');
    }
    
    public function index()
    {
        // Check language settings:
        if ( ! isset ($_COOKIE['lang'])) {
            setcookie('lang', 'en', time() + (86400 * 30), "/");
            $lang = 'en';
        } else {
            $lang = $_COOKIE['lang'];
        }
        
        // Load language file:
        $this->lang->load('text', $lang);
        
        // Generate database data:
        //$this->Data->dbGen();
        
        // Create table template:        
        $data['title'] = $this->lang->line('title');
        $data['heading'] = $this->lang->line('heading');
        
        $table_id           =   $this->lang->line('table_id');
        $table_event        =   $this->lang->line('table_event');
        $table_timestamp    =   $this->lang->line('table_timestamp');
        $table_caller       =   $this->lang->line('table_caller');
        $table_reciever     =   $this->lang->line('table_reciever');
        
        // Set table data:
        $this->table->set_heading($table_id, $table_event, $table_timestamp, $table_caller, $table_reciever);
        $this->table->set_template(array(
            'table_open'    =>      '<table class="table table-striped table-hover" id="log">',
            'row_start'     =>      '<tr id="call">',
            'row_alt_start' =>      '<tr id="call">'));
        
        // Generate table:
        $query = $this->db->query("SELECT * FROM T_PHONE_RECORDS");
        $data['table'] = $this->table->generate($query);
        
        // Load view with $data array:
        $this->load->view('view', $data);
    }
    
    // Set russian language:
    public function ru()
    {
        setcookie('lang', 'ru', time() + (86400 * 30), "/");
        $this->lang->load('text', 'ru');
        redirect(base_url());
    }
    
    // Set english language:
    public function en()
    {
        setcookie('lang', 'en', time() + (86400 * 30), "/");
        $this->lang->load('text', 'en');
        redirect(base_url());
    }
}
