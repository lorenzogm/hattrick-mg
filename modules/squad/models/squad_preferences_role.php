<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Squad_preferences_role extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'is_default' => array(
            'label' => 'Role',
            'rules' => array('required', 'trim')
        ),
        'position_id' => array(
            'label' => 'Position ID',
            'rules' => array('required', 'trim')
        ),
        'custom_role_label' => array(
            'label' => 'BG color',
            'rules' => array('trim')
        ),
        'custom_bg_color' => array(
            'label' => 'BG color',
            'rules' => array('trim')
        ),
        'show_on_training' => array(
            'label' => 'Show on training',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}