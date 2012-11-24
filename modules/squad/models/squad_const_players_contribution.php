<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Squad_const_players_contribution extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'position' => array(
            'label' => 'English',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'number_of_players' => array(
            'label' => 'Number of players',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'individual_order' => array(
            'label' => 'Individual order',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'center_back_keeper_skill' => array(
            'label' => 'Center back contribution by the keeper skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'center_back_defender_skill' => array(
            'label' => 'Center back contribution by the defender skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'wing_back_keeper_skill' => array(
            'label' => 'Wing back contribution by the keeper skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'wing_back_defender_skill' => array(
            'label' => 'Wing back contribution by the defender skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'midfield_playmaker_skill' => array(
            'label' => 'Midfield contribution by the playmaker skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'wing_attack_winger_skill' => array(
            'label' => 'Wing attack contribution by the winger skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'wing_attack_passing_skill' => array(
            'label' => 'Wing attack contribution by the passing skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'wing_attack_scorer_skill' => array(
            'label' => 'Wing attack contribution by the scorer skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'central_attack_scorer_skill' => array(
            'label' => 'Center attack contribution by the scorer skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'central_attack_passing_skill' => array(
            'label' => 'Center attack contribution by the passing skill',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}