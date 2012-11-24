<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_const_percentage extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'training_type' => array(
            'label' => 'Training type',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'match_role_id' => array(
            'label' => 'Match role ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'percentage' => array(
            'label' => 'Percentage',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}