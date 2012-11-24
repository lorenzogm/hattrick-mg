<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Squad_player extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'team_name' => array(
            'label' => 'Team name',
            'rules' => array('required', 'trim', 'max_lenght' => 100)
        ),
        'player_id' => array(
            'label' => 'Player ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'role_id' => array(
            'label' => 'Role ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'first_name' => array(
            'label' => 'First name',
            'rules' => array('required', 'trim')
        ),
        'last_name' => array(
            'label' => 'Last name',
            'rules' => array('required', 'trim')
        ),
        'age' => array(
            'label' => 'Age',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'age_days' => array(
            'label' => 'Age days',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'tsi' => array(
            'label' => 'TSI',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'player_form' => array(
            'label' => 'Player form',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'experience' => array(
            'label' => 'Experience',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'loyalty' => array(
            'label' => 'Loyalty',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'mother_club_bonus' => array(
            'label' => 'Mother club bonus',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'leadership' => array(
            'label' => 'Leadership',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'salary' => array(
            'label' => 'Salary',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'country_id' => array(
            'label' => 'Country ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'is_abroad' => array(
            'label' => 'Is abroad',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'specialty' => array(
            'label' => 'Specialty',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'transfer_listed' => array(
            'label' => 'Tranfer listed',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'sold' => array(
            'label' => 'Sold',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'cards' => array(
            'label' => 'Cards',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'injury_level' => array(
            'label' => 'Injury level',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'stamina_skill' => array(
            'label' => 'Stamina skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'keeper_skill' => array(
            'label' => 'Keeper skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'playmaker_skill' => array(
            'label' => 'Playmaker skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'scorer_skill' => array(
            'label' => 'Scorer skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'passing_skill' => array(
            'label' => 'Passing skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'winger_skill' => array(
            'label' => 'Winger skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'defender_skill' => array(
            'label' => 'Defender skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'set_pieces_skill' => array(
            'label' => 'Set pieces skill',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'score' => array(
            'label' => 'Score',
            'rules' => array('required', 'trim')
        ),
        'individual_order' => array(
            'label' => 'Individual order',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}