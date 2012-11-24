<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Profile extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'user_id' => array(
            'label' => 'User ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'username' => array(
            'label' => 'Username',
            'rules' => array('required', 'trim')
        ),
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('trim', 'numeric')
        ),
        'team_name' => array(
            'label' => 'Team name',
            'rules' => array('trim', 'max_length' => 100)
        ),
        'user_token' => array(
            'label' => 'User token',
            'rules' => array('required', 'trim', 'alpha_numeric', 'max_length' => 100)
        ),
        'user_token_secret' => array(
            'label' => 'User token secret',
            'rules' => array('required', 'trim', 'alpha_numeric', 'max_length' => 100)
        ),
        'valid_token' => array(
            'label' => 'League ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'league_id' => array(
            'label' => 'League ID',
            'rules' => array('trim', 'numeric')
        ),
        'season' => array(
            'label' => 'Season',
            'rules' => array('trim', 'numeric')
        ),
        'match_round' => array(
            'label' => 'Match round',
            'rules' => array('trim', 'numeric')
        ),
        'training_date' => array(
            'label' => 'Training date',
            'rules' => array('trim', 'valid_date')
        ),
        'economy_date' => array(
            'label' => 'Economy date',
            'rules' => array('trim', 'valid_date')
        ),
        'cup_match_date' => array(
            'label' => 'Cup match date',
            'rules' => array('trim', 'valid_date')
        ),
        'series_match_date' => array(
            'label' => 'Series match date',
            'rules' => array('trim', 'valid_date')
        ),
        'currency_name' => array(
            'label' => 'Currency name',
            'rules' => array('trim')
        ),
        'currency_rate' => array(
            'label' => 'Currency rate',
            'rules' => array('trim')
        )
    );

}