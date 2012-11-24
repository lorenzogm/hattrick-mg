<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Economy extends CI_Controller {

    public $layout = 'private';

    function __construct() {
        parent::__construct();

        $this->load->language('economy');
        $this->load->model('economy_model');
    }

    public function index() {

        $data['teamID'] = get_team_id();
        $data['fields'] = array(
            'season',
            'income_spectators',
            'income_sponsors',
            'costs_arena',
            'costs_staff',
            'costs_youth',
            'constant_balance',
            'income_sold_players',
            'income_sold_players_commission',
            'income_temporary',
            'costs_players',
            'costs_bought_players',
            'costs_arenaBuilding',
            'costs_temporary',
            'costs_financial',
            'variable_balance',
            'balance'
            );
        $data['seasonal_economies'] = $this->economy_model->get_seasonal_economies(get_team_id());
        $data['message'] = $this->session->flashdata('message_season_added');

        $this->load->view('economy/economy', $data);
    }

    public function add_season ($season = NULL) {
        $data['fields'] = $this->economy_model->get_economy_seasonal_fields();

        if(is_null($season)) {
            foreach ($data['fields'] as $field)
                $values[$field] = 0;
        } else {
            $seasonData = $this->economy_model->get_seasonal_economies(get_team_id(), $season);
            foreach ($data['fields'] as $field)
                $values[$field] = $seasonData->$field;
        }

        $post = $this->input->post();

        if(!is_null($post['form'])) {
            unset($post['form']);
            if(!is_null($season)) {
                $team_id = array('team_id' => get_team_id());
                $post = $team_id + $post;

                $this->economy_model->update_season($post, get_team_id(), $season);
                set_flashdata(lang('message_season_added'));
                redirect('manager/economy');
            } else {
                if(!$this->economy_model->is_exists(get_team_id(), $post['season'])) {
                    $team_id = array('team_id' => get_team_id());
                    $post = $team_id + $post;

                    $this->economy_model->insert_season($post);
                    set_flashdata(lang('message_season_added'));
                    redirect('manager/economy');
                } else {
                    $data['values'] = $post;
                    $data['message'] = lang('message_season_exists');
                    $this->load->view('economy/add_season', $data);
                }
            }
        } else {
            $data['values'] = $values;
            $this->load->view('economy/add_season', $data);
        }
    }

    public function market($season = NULL) {

        if(is_null($season))
            $data['season'] = $this->economy_model->get_current_season(get_team_id());
        else
            $data['season'] = $season;

        $data['teamID'] = get_team_id();
        $data['seasons'] = $this->economy_model->get_seasons(get_team_id());

        $players_to_show = $this->economy_model->get_players_to_show(get_team_id());
        foreach ($players_to_show as $player)
            $array[] = $player->player_id;
        $data['players'] = $this->economy_model->get_economy_market_price(get_team_id(), $data['season'], $array);

        $this->load->view('economy/market', $data);
    }

}

/* End of file home.php */
/* Location: ./application/modules/home/controllers/home.php */