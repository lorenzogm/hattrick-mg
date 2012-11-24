<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_team extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'season' => array(
            'label' => 'Season',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'training_type' => array(
            'label' => 'Training type',
            'rules' => array('required', 'trim', 'alpha_dash', 'max_length' => 255)
        ),
        'training_level' => array(
            'label' => 'Training level',
            'rules' => array('required', 'trim', 'alpha_dash', 'max_length' => 255)
        ),
        'stamina_training_part' => array(
            'label' => 'Stamina training part',
            'rules' => array('required', 'trim', 'alpha_dash', 'max_length' => 255)
        )
    );

}