<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_season extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'season' => array(
            'label' => 'Season',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}