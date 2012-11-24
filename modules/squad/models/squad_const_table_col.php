<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Squad_const_table_col extends DataMapper {

    var $prefix = "mg_";

    var $validation = array(
        'col_label' => array(
            'label' => 'Column label',
            'rules' => array('required', 'trim', 'alpha_dash')
        )
    );

}