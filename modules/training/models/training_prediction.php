<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_prediction extends DataMapper {

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
        'skill_id' => array(
            'label' => 'Skill ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'season' => array(
            'label' => 'Season',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'match_round' => array(
            'label' => 'Match round',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}