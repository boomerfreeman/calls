<?php

class Language extends CI_Controller
{
    public function setLang($lang = 'en')
    {
        switch ($lang) {
            case 'en':
                $data['title'] = 'Call log';
                $data['caller'] = 'Caller';
                $data['event'] = 'Event';
                $data['reciever'] = 'Reciever';
                $data['time'] = 'Timestamp';
            
            case 'ru':
                $this->load->model('lang_ru');
        }
    }
}
