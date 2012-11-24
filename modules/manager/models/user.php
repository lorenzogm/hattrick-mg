<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User extends DataMapper {

    var $prefix = "";

    var $validation = array(
        'email' => array(
            'label' => 'Email',
            'rules' => array('required', 'trim', 'max_length' => 60)
        ),
        'password' => array(
            'label' => 'Password',
            'rules' => array('required', 'trim', 'max_length' => 100)
        ),
        'sañt' => array(
            'label' => 'Salt',
            'rules' => array('required', 'trim', 'max_length' => 6)
        ),
        'username' => array(
            'label' => 'Username',
            'rules' => array('required', 'trim', 'max_length' => 50)
        ),
        'group_id' => array(
            'label' => 'Group ID',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'ip_address' => array(
            'label' => 'IP address',
            'rules' => array('required', 'trim', 'max_length' => 50)
        ),
        'active' => array(
            'label' => 'Active',
            'rules' => array('required', 'trim', 'numeric')
        ),
        'activation_code' => array(
            'label' => 'Activation code',
            'rules' => array('required', 'trim', 'max_length' => 40)
        ),
        'new_password_requested' => array(
            'label' => 'New password requested',
            'rules' => array('required', 'trim', 'valid_date')
        ),
        'new_email' => array(
            'label' => 'New email',
            'rules' => array('required', 'trim', 'max_length' => 100)
        ),
        'new_email_key' => array(
            'label' => 'New email key',
            'rules' => array('required', 'trim', 'max_length' => 50)
        ),
        'last_ip' => array(
            'label' => 'Last IP',
            'rules' => array('required', 'trim', 'valid_ip', 'max_length' => 40)
        ),
        'last_login' => array(
            'label' => 'Last login',
            'rules' => array('required', 'trim', 'valid_date')
        ),
        'created' => array(
            'label' => 'Created',
            'rules' => array('required', 'trim', 'valid_date')
        ),
        'modified' => array(
            'label' => 'Modified',
            'rules' => array('required', 'trim')
        )
    );

}