<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_event extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'player_id' => array(
            'label' => 'Player ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'skill_id' => array(
            'label' => 'Skill ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'old_level' => array(
            'label' => 'Old level',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'new_level' => array(
            'label' => 'New level',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'season' => array(
            'label' => 'Season',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'match_round' => array(
            'label' => 'Match round',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'day_number' => array(
            'label' => 'Day number',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}