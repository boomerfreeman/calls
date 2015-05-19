<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of lang_ru
 *
 * @author PB
 */
class Lang_ru extends CI_Model
{
    public function index()
    {
        $data['title'] = 'Лог звонков';
        $data['caller'] = 'Звонящий';
        $data['event'] = 'Событие';
        $data['reciever'] = 'Принимающий';
        $data['time'] = 'Время';
    }
}
