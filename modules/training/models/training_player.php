<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_player extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'player_id' => array(
            'label' => 'Player ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'season' => array(
            'label' => 'Season',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'percentage_trained' => array(
            'label' => 'Percentage trained',
            'rules' => array('required', 'trim')
        ),
        'new_percentage_trained' => array(
            'label' => 'New percentage trained',
            'rules' => array('required', 'trim')
        ),
        'minutes_trained' => array(
            'label' => 'Minutes trained',
            'rules' => array('required', 'trim')
        ),
        'new_minutes_trained' => array(
            'label' => 'New minutes trained',
            'rules' => array('required', 'trim')
        ),
        'auto_set' => array(
            'label' => 'Auto set value',
            'rules' => array('required', 'trim')
        )
    );

}