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
        
        // Generate table data for 100 different numbers:
        $this->Data->genTable(100);
        
        // Create table template:        
        $data['title'] = $this->lang->line('title');
        $data['heading'] = $this->lang->line('heading');
        
        $table_event        =   $this->lang->line('table_event');
        $table_timestamp    =   $this->lang->line('table_timestamp');
        $table_caller       =   $this->lang->line('table_caller');
        $table_reciever     =   $this->lang->line('table_reciever');
        $modal_title        =   $this->lang->line('modal_title');
        $modal_duration     =   $this->lang->line('modal_duration');
        
        // Pre-set main table configuration:
        $this->table->set_heading($table_caller, $table_event, $table_reciever, $table_timestamp);
        $this->table->set_template(array(
            'table_open'    =>      '<table class="table table-striped table-hover" id="log">'));
        
        // Generate table with first 10 rows:
        $query = $this->db->query('SELECT CALLER, RECORD_EVENT_ID, RECIEVER, RECORD_DATE 
                                   FROM T_PHONE_RECORDS LIMIT 0,10');
        $data['table'] = $this->table->generate($query);
        
        // Set modal window configuration and generate data:
        $this->table->set_heading($modal_title, $table_caller, $table_event, $table_reciever, $table_timestamp);
        $this->table->set_template(array(
            'table_open'    =>      '<table class="table table-striped table-hover" id="modal">'));
        $data['modal'] = $this->table->generate();
        
        // Set extended modal window configuration and generate data:
        $this->table->set_heading($table_timestamp, $modal_duration, $table_reciever, $modal_title);
        $this->table->set_template(array(
            'table_open'    =>      '<table class="table table-striped table-hover" id="extend">'));
        $data['extend'] = $this->table->generate();
        
        // Load view:
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
