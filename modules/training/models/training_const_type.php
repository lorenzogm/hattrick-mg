<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Training_const_type extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'skill_id' => array(
            'label' => 'Skill ID',
            'rules' => array('required', 'trim', 'numeric')
        )
    );

}