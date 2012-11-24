<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Synchronization extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'user_id' => array(
            'label' => 'User ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'start_date' => array(
            'label' => 'Start date',
            'rules' => array('required', 'trim', 'valid_date')
        ),
        'end_date' => array(
            'label' => 'End date',
            'rules' => array('required', 'trim', 'valid_date')
        ),
        'position_queue' => array(
            'label' => 'Position in queue',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}