<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Player extends CI_Controller {

    public $layout = 'private';
    var $maxlength = 100;
    var $size = 50;
    var $role;

    function __construct() {
        parent::__construct();

        //$this->load->helper('squad');
        //$this->load->helper('training');
        //$this->load->language('player');
        $this->load->model('player_model');
        //$this->load->model('squad_model');
        //$this->load->model('training_model');
    }

    public function _remap($method)
    {
        $param_offset = 2;

        // Default to index
        if ( ! method_exists($this, $method))
        {
            // We need one more param
            $param_offset = 1;
            $method = 'index';
        }

        // Since all we get is $method, load up everything else in the URI
        $params = array_slice($this->uri->rsegment_array(), $param_offset);

        // Call the determined method with all params
        call_user_func_array(array($this, $method), $params);
    }

    public function index($player_id) {

        $data = array();
        $data['team_id'] = get_team_id();
        $data['player'] = $this->player_model->get_player($player_id, get_team_id());
        $this->load->view('player/player', $data);
    }

}

/* End of file home.php */
/* Location: ./application/modules/home/controllers/home.php */