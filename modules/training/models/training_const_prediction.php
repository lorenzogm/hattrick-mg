<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_const_prediction extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'trainer_skill' => array(
            'label' => 'Trainer Skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'assistants' => array(
            'label' => 'Assistants',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'training_level' => array(
            'label' => 'Training level',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'age' => array(
            'label' => 'Age',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'training_type' => array(
            'label' => 'Training type',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'skill_level' => array(
            'label' => 'Skill level',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'weeks' => array(
            'label' => 'Weeks',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}