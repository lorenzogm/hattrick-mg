<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Roles_bg_color extends DataMapper {

    var $prefix = "const_";

    var $validation = array(
        'bg_color' => array(
            'label' => 'BG Color',
            'rules' => array('required', 'trim')
        )
    );

}