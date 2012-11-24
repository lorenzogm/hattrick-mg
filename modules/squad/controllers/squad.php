<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Squad extends Public_Controller {

    public $mg_profile;

    function __construct() {
        parent::__construct();

        if(!isset($this->current_user->id)) {
            redirect('users/login');
        }

        $this->load->model('manager/profile');
        $this->mg_profile = new Profile();
        $this->mg_profile->where('user_id', $this->current_user->id)->get();
        if(!$this->mg_profile->valid_token)
            redirect('sync');

        $this->load->helper('squad');
        $this->load->language('squad');
    }

    public function index() {

        // Si spanish la primera vez que ejecutamos la página o hay error
        if ($this->input->post('form') == NULL)
            $this->build_table ();
        // Si se ha rellenado el formulario sin errores
        else {
            // Obtenemos las variables POST
            $post = $this->input->post();
            unset($post['submit']);
            unset($post['form']);

            // Asignamos los datos
            $squad_player = new Squad_player();
            $where = array (
                'team_id' => $this->mg_profile->team_id,
                'sold' => 0
            );
            $squad_player->where($where)->get();
            foreach ($squad_player as $player) {
                foreach ($post as $key => $value)
                    if($key == $player->player_id) {
                        $player->role_id = $value;
                        $this->load->library('sync_squad');

                        $score = $this->sync_squad->get_player_score($player);

                        $player->score = $score['score'];
                        $player->individual_order = $score['individual_order'];
                        $where = array(
                            'team_id' => $this->mg_profile->team_id,
                            'player_id' => $key
                        );
                        $update = array(
                            'role_id' => $value,
                            'score' => $score['score'],
                            'individual_order' => $score['individual_order']
                        );

                        if(!$player->where($where)->update($update))
                            DM_log($player);
                    }
            }

            $this->session->set_flashdata('success', lang('label_changes_saved'));
            // Redireccinamos a la página principal
            redirect('squad');
        }
    }

    public function order($table_col, $inv = NULL) {
        // Si spanish la primera vez que ejecutamos la página o hay error
        if ($this->input->post('form') == NULL)
            $this->build_table ($table_col, $inv);
        // Si se ha rellenado el formulario sin errores
        else {
            // Obtenemos las variables POST
            $post = $this->input->post();
            unset($post['submit']);
            unset($post['form']);

            // Asignamos los datos
            $players = new Squad_player();
            foreach ($post as $key => $value)
                $players->where('player_id', $key)->update('role_id', $value);

            // Redireccinamos a la página principal
            redirect('squad/order/'.$table_col);
        }
    }

    public function build_table($order = NULL, $inv = NULL) {

        $data['mg_profile'] = $this->mg_profile;
        // Tables colums
        $table_cols = new Squad_const_table_col();
        $data['table_cols'] = $table_cols->get();

        // Colums checked
        $preferences_table_cols = new Squad_preferences_table_col();
        $data['preferences_table_cols'] = $preferences_table_cols->get();

        // Players
        $players = new Squad_player();
        if(is_null($order))
            $data['players'] = $players->where('team_id', $this->mg_profile->team_id)->order_by('role_id', 'ASC')->order_by('score', 'DESC')->get();
        else {
            if(is_null($inv))
                $direction = 'DESC';
            else
                $direction = 'ASC';
            if($order == 'state')
                $data['players'] = $players->where('team_id', $this->mg_profile->team_id)->order_by('transfer_listed', $direction)->order_by('injury_level', $direction)->order_by('cards', $direction)->order_by('score', 'DESC')->get();
            else {
                if($order == 'role')
                    $order = 'role_id';

                $data['players'] = $players->where('team_id', $this->mg_profile->team_id)->order_by($order, $direction)->order_by('score', 'DESC')->get();
            }
        }
        $players_previous_week = new Squad_player_previous_week();
        $data['players_previous_week'] = $players_previous_week->where('team_id', $this->mg_profile->team_id)->get();

        // Roles
        $roles = new Squad_preferences_role();
        $roles->where('team_id', $this->mg_profile->team_id)->get();
        foreach ($roles as $role) {
            if(isset($role->custom_role_label))
                $form_roles[$role->position_id] = $role->custom_role_label;
            else
                $form_roles[$role->position_id] = lang('position_'.$role->position_id);
        }

        $data['roles'] = $form_roles;

        $data['player_role'] = new Squad_preferences_role();

        // Load the view
        $this->template
            ->title($this->module_details['name'])
            ->append_css('module::squad.css')
            ->build('squad', $data);
    }

    public function preferences($form = NULL) {

        if ($form == 'cols') {
            // Obtenemos las variables POST
            $post = $this->input->post();
            unset($post['submit']);

            // Asignamos los datos
            $preferences_table_cols = new Squad_preferences_table_col();
            if(!$preferences_table_cols->where('team_id', $this->mg_profile->team_id)->update('is_checked', FALSE))
                DM_log($preferences_table_cols);

            foreach ($post as $key => $value) {
                $where = array(
                    'col_id' => $key,
                    'team_id' => $this->mg_profile->team_id
                );
                if(!$preferences_table_cols->where($where)->update('is_checked', TRUE))
                    DM_log($preferences_table_cols);
            }
            $this->session->set_flashdata('success', lang('label_changes_saved'));
            redirect('squad/preferences');

        } else if($form == 'roles') {
            // Obtenemos las variables POST
            $post = $this->input->post();
            unset($post['submit']);

            // Asignamos los datos
            $data = array();
            foreach ($post as $key => $value) {
                if(!is_int($key)) {
                    $name = explode("color_", $key);
                    $data[$name[1]]['id'] = $name[1];
                    $data[$name[1]]['custom_bg_color'] = $value;
                } else {
                    $data[$key]['id'] = $key;
                    $data[$key]['custom_role_label'] = $value;
                }
            }

            $preferences_roles = new Squad_preferences_role();
            foreach ($data as $row) {
                $update_data = $row;
                unset($update_data['id']);

                $where = array(
                    'id' => $row['id'],
                    'team_id' => $this->mg_profile->team_id
                );
                if(!$preferences_roles->where($where)->update($update_data))
                    DM_log($preferences_roles);
            }

            // Redireccinamos a la página principal
            $this->session->set_flashdata('success', lang('label_changes_saved'));
            redirect('squad/preferences');
        } else {

            // Tables colums
            $table_cols = new Squad_const_table_col();
            $data['table_cols'] = $table_cols->order_by('id', 'ASC')->get();

            // Colums checked
            $preferences_table_cols = new Squad_preferences_table_col();
            $data['preferences_table_cols'] = $preferences_table_cols->where('team_id', $this->mg_profile->team_id)->get();

            $roles = new Squad_preferences_role();
            $data['roles'] = $roles->where('team_id', $this->mg_profile->team_id)->get();

            // Mostramos el formulario
            $this->template
                ->title($this->module_details['name'])
                ->build('squad/preferences_form', $data);

        }
    }
    /*
        function getPlayerScore($dataPlayer) {

            $playerContributions = $this->getPlayersContributions($dataPlayer->RolID);

            foreach ($playerContributions as $playerContribution) {

                $fields = array(
                    '1' => 'CenterBack-KeeperSkill',
                    '2' => 'WingBack-KeeperSkill',
                    '3' => 'CenterBack-DefenderSkill',
                    '4' => 'WingBack-DefenderSkill',
                    '5' => 'Midfield-PlaymakerSkill',
                    '6' => 'LateralAttack-WingerSkill',
                    '7' => 'LateralAttack-PassingSkill',
                    '8' => 'CentralAttack-PassingSkill',
                    '9' => 'LateralAttack-ScorerSkill',
                    '10' => 'CentralAttack-ScorerSkill'
                );

                $keeperSkill = $dataPlayer->KeeperSkill * ($playerContribution->$fields[1] + $playerContribution->$fields[2]);
                $defenderSkill = $dataPlayer->DefenderSkill * ($playerContribution->$fields[3] + $playerContribution->$fields[4]);
                $playMakerSkill = $dataPlayer->PlaymakerSkill * ($playerContribution->$fields[5]);
                $wingerSkill = $dataPlayer->WingerSkill * ($playerContribution->$fields[6]);
                $passingSkill = $dataPlayer->PassingSkill * ($playerContribution->$fields[7] + $playerContribution->$fields[8]);
                $scorerSkill = $dataPlayer->ScorerSkill * ($playerContribution->$fields[9] + $playerContribution->$fields[10]);

                $contributionsByPosition[$playerContribution->ID]  = $keeperSkill + $defenderSkill + $playMakerSkill + $wingerSkill + $passingSkill + $scorerSkill;

                if($dataPlayer->Specialty != 1)
                    $contributionsByPosition[34] = 0;
            }

            $max = max($contributionsByPosition);
            $data['Score'] = $max;

            foreach ($contributionsByPosition as $key => $value)
                if($max == $value) {
                    $data['IndividualOrder'] = $this->getPlayerPosition($key);
                }
            return $data;
        }
    */
}

/* End of file home.php */
/* Location: ./application/modules/home/controllers/home.php */