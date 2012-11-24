<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Squad_preferences_table_col extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'team_id' => array(
            'label' => 'Team ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'col_id' => array(
            'label' => 'Col ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'is_checked' => array(
            'label' => 'Checked',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}