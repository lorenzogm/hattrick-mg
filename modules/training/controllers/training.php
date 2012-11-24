<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Training extends Public_Controller {

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

        $this->load->language('training');
        $this->load->helper('training');
    }

    public function _remap($method) {
        $param_offset = 2;

        // Default to index
        if ( ! method_exists($this, $method)) {
            // We need one more param
            $param_offset = 1;
            $method = 'index';
        }

        // Since all we get is $method, load up everything else in the URI
        $params = array_slice($this->uri->rsegment_array(), $param_offset);

        // Call the determined method with all params
        call_user_func_array(array($this, $method), $params);
    }

    function index($season = NULL, $edit_view = NULL) {

        if($this->input->post('submit') != NULL) {
            $post = $this->input->post();
            unset($post->submit);
            $update_players_prediction = array();
            foreach ($post as $key => $value) {
                if($value != 0) {
                    $filter = explode('-', $key);

                    if($filter[0] == 'training_type' OR $filter[0] == 'stamina_training_part') {
                        $training_team = new Training_team();
                        $where = array(
                            'team_id' => $this->mg_profile->team_id,
                            'season' => $season
                        );
                        $training_team->where($where)->get();

                        if($filter[0] == 'stamina_training_part' AND $value > '100')
                            $value = 100;

                        $var = explode('-', $training_team->$filter[0]);
                        $var[$filter[1]] = $value;
                        $training_team->$filter[0] = implode('-', $var);

                        if($training_team->save())
                            DM_log($training_team);

                    } elseif($filter[0] == 'percentage_trained' OR $filter[0] == 'minutes_trained') {
                        $training_player = new Training_player();
                        $where = array(
                            'team_id' => $this->mg_profile->team_id,
                            'player_id' => $filter[1],
                            'season' => $season
                        );

                        $training_player->where($where)->get();
                        if($training_player->result_count() == 0) {

                            for($match_round = 0; $match_round <= 15; ++$match_round) {
                                $percentage_trained[$match_round] = 0;
                                $new_percentage_trained[$match_round] = 0;
                                $minutes_trained[$match_round] = 0;
                                $new_minutes_trained[$match_round] = 0;
                                $auto_set[$match_round] = 1;
                            }

                            $training_player->team_id = $this->mg_profile->team_id;
                            $training_player->player_id = $filter[1];
                            $training_player->season = $season;
                            $training_player->percentage_trained = implode('-', $percentage_trained);
                            $training_player->new_percentage_trained = implode('-', $new_percentage_trained);
                            $training_player->minutes_trained = implode('-', $minutes_trained);
                            $training_player->new_minutes_trained = implode('-', $new_minutes_trained);
                            $training_player->auto_set = implode('-', $auto_set);

                            if($training_player->save_as_new())
                                DM_log($training_player);
                        }

                        $training_player->where($where)->get();

                        if($filter[0] == 'percentage_trained' AND $value > '100')
                            $value = 100;
                        elseif($filter[0] == 'minutes_trained' AND $value > '90')
                            $value = 90;

                        $var = explode('-', $training_player->$filter[0]);
                        if($var[$filter[2]] != $value) {
                            $var[$filter[2]] = $value;
                            $training_player->$filter[0] = implode('-', $var);

                            if($training_player->save())
                                DM_log($training_player);

                            if(!in_array($filter[1], $update_players_prediction))
                                $update_players_prediction[] = $filter[1];
                        }
                    }
                }
            }
            $this->training_predictor($update_players_prediction);
            $this->session->set_flashdata('success', lang('message_training_modified'));
            redirect('training/'.$season);
        }

        // Team ID
        $team_id = array('team_id' => $this->mg_profile->team_id);
        $data['team_id'] = $this->mg_profile->team_id;

        // Season to show
        if(is_null($season))
            $data['season'] = $this->mg_profile->season;
        else
            $data['season'] = $season;

        // Seasons with training data
        $seasons = new Training_season();
        $data['seasons'] = $seasons->where($team_id)->get();

        // Whether is edit view
        $data['edit_view'] = $edit_view;

        // Roles to show
        $this->load->model('squad/squad_preferences_role');
        $preferences_roles = new Squad_preferences_role();
        $data['preferences_roles'] = $preferences_roles;
        $where = array(
            'team_id' => $this->mg_profile->team_id,
            'show_on_training' => TRUE
        );
        $preferences_roles->where($where)->order_by('position_id', 'ASC')->get();

        // Players to show
        $this->load->model('squad/squad_player');
        $players = new Squad_player();
        $where = array(
            'team_id' => $this->mg_profile->team_id,
            'sold' => 0
        );
        $players->where($where)->get();

        $data['players'] = $players;
        foreach ($preferences_roles as $preferences_role) {
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'sold' => 0,
                'role_id' => $preferences_role->id
            );
            $players->where($where)->order_by('score', 'DESC')->get();

            foreach ($players as $player)
                $players_to_show[] = $player->player_id;
        }
        $data['players_to_show'] = $players_to_show;

        // Predictions
        $predictions = new Training_prediction();
        $where = array(
            'team_id' => $this->mg_profile->team_id,
            'season' => $data['season']
        );
        $data['predictions'] = $predictions->where($where)->get();

        // Training type
        $training_types = new Training_const_type();
        $data['training_types'] = $training_types->get();

        $data['mg_profile'] = $this->mg_profile;

        $this->template
            ->title($this->module_details['name'])
            ->append_css('module::training.css')
            ->append_js('module::jquery-1.8.2.min.js')
            ->append_js('module::jquery-ui-git.js')
            ->append_css('module::jquery-ui-git.css')
            ->append_js('module::training.js')
            ->build('training', $data);
    }

    function preferences($form = NULL) {

        $this->load->model('squad/squad_preferences_role');
        $squad_preferences_roles = new Squad_preferences_role();
        $data['squad_preferences_roles'] = $squad_preferences_roles;
        $squad_preferences_roles->where('team_id', $this->mg_profile->team_id)->get();

        if ($form == 'roles') {

            // Obtenemos las variables POST
            $post = $this->input->post();

            // Asignamos los datos
            $squad_preferences_roles->where('team_id', $this->mg_profile->team_id)->update('show_on_training', FALSE);
            foreach ($post as $key => $value)
                $squad_preferences_roles->where('id', $key)->update('show_on_training', TRUE);

            $this->session->set_flashdata('success', lang('message_changes_saved'));
            redirect('training/preferences');
        }

        $data['season'] = $this->mg_profile->season;
        // Mostramos el formulario
        $this->template
            ->title($this->module_details['name'])
            ->build('preferences_form', $data);

    }

    public function add_season () {

        $this->db->where('team_id', $this->mg_profile->team_id);
        $this->db->select_max('season');
        $query = $this->db->get('mg_training_seasons');
        $last_season = $query->row('season');

        $new_season = $last_season + 1;
        $flag_season = $this->mg_profile->season + 3;

        if($new_season <= $flag_season) {
            $training_season = new Training_season();
            $training_season->team_id = $this->mg_profile->team_id;
            $training_season->season = $new_season;

            if(!$training_season->save_as_new())
                DM_log($training_season);

            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'season' => $last_season
            );

            $training_team = new Training_team();
            $training_team->where($where)->get();

            $training_type = explode('-', $training_team->training_type);
            $training_level = explode('-', $training_team->training_level);
            $stamina_training_part = explode('-', $training_team->stamina_training_part);

            for($match_round = 0; $match_round <= 15; ++$match_round) {
                $training_type[$match_round] = $training_type[$this->mg_profile->match_round];
                $training_level[$match_round] = $training_level[$this->mg_profile->match_round];
                $stamina_training_part[$match_round] = $stamina_training_part[$this->mg_profile->match_round];
            }

            unset($training_team->id);
            $training_team->team_id = $this->mg_profile->team_id;
            $training_team->season = $new_season;
            $training_team->training_type = implode('-', $training_type);
            $training_team->training_level = implode('-', $training_level);
            $training_team->stamina_training_part = implode('-', $stamina_training_part);

            if(!$training_team->save_as_new())
                DM_log($training_team);

            $squad_player = new Squad_player();
            $squad_player->where('team_id', $this->mg_profile->team_id)->get();

            foreach($squad_player->all as $player) {
                for($match_round = 0; $match_round <= 15; ++$match_round) {
                    $percentage_trained[$match_round] = 0;
                    $new_percentage_trained[$match_round] = 0;
                    $minutes_trained[$match_round] = 0;
                    $new_minutes_trained[$match_round] = 0;
                    $auto_set[$match_round] = 1;
                }

                $training_player = new Training_player();

                $training_player->team_id = $this->mg_profile->team_id;
                $training_player->player_id = $player->player_id;
                $training_player->season = $new_season;
                $training_player->percentage_trained = implode('-', $percentage_trained);
                $training_player->new_percentage_trained = implode('-', $new_percentage_trained);
                $training_player->minutes_trained = implode('-', $minutes_trained);
                $training_player->new_minutes_trained = implode('-', $new_minutes_trained);
                $training_player->auto_set = implode('-', $auto_set);

                if($training_player->save_as_new())
                    DM_log($training_player);
            }

            $this->session->set_flashdata('success', lang('mesage_changes_saved'));
        } else
            $this->session->set_flashdata('error', lang('message_season_limit'));

        redirect('training');
    }

    function training_predictor ($players_id) {

        $training_prediction = new Training_prediction();
        $where = array ('team_id' => $this->mg_profile->team_id);
        $training_prediction->where($where)->get();
        if(!$training_prediction->delete())
            DM_log($training_prediction);

        $this->load->model('squad/squad_player');
        $squad_player = new Squad_player();
        foreach ($players_id as $player_id) {
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'player_id' => $player_id
            );
            $squad_player->where($where)->get();

            $training_event = new Training_event();
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'player_id' => $squad_player->player_id
            );

            $training_event->where($where)->order_by('season', 'match_round')->get();
            if($training_event->result_count() != 0) {
                $training_event->where($where)->group_by('skill_id')->order_by('season', 'match_round')->get();

                foreach ($training_event->all as $last) {
                    if($last->old_level < $last->new_level AND $last->skill_id != 2) {
                        --$last->match_round;

                        $training_team = new Training_team();
                        $where = array (
                            'team_id' => $this->mg_profile->team_id,
                            'season' => $last->season
                        );
                        $training_team->where($where)->get();
                        $training_type = explode('-', $training_team->training_type);

                        $where = array(
                            'trainer_skill' => 7,
                            'assistants' => 10,
                            'training_level' => 100,
                            'age' => $squad_player->age,
                            'training_type' => $training_type[$last->match_round],
                            'skill_level' => $last->new_level,
                        );

                        $weeks = NULL;
                        $const_training_prediction = new Training_const_prediction();
                        while($weeks == NULL) {
                            $const_training_prediction->where($where)->get();
                            $weeks = $const_training_prediction->weeks;

                            --$where['age'];
                            if($where['age'] < 17) {
                                $weeks = 100;
                            }
                        }

                        $prediction[$squad_player->player_id][$last->skill_id]['skill_id'] = $last->skill_id;
                        $prediction[$squad_player->player_id][$last->skill_id]['last_training_event_season'] = $last->season;
                        $prediction[$squad_player->player_id][$last->skill_id]['last_training_event_match_round'] = $last->match_round;
                        $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_season'] = $last->season;
                        $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'] = $last->match_round + $weeks;

                        while($prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'] > 16) {
                            $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'] = $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'] - 16;
                            ++$prediction[$squad_player->player_id][$last->skill_id]['next_training_event_season'];
                        }

                        $this->db->where('team_id', $this->mg_profile->team_id);
                        $this->db->select_max('season');
                        $query = $this->db->get('mg_training_seasons');
                        $last_season = $query->row('season');

                        $flag_done = FALSE;
                        while(!$flag_done) {
                            $flag_done = FALSE;

                            for ($season = (int) $last->season; $season <= $last_season; ++$season) {
                                if($season == $last->season)
                                    $initial_match_round = $last->match_round + 1;
                                else
                                    $initial_match_round = 0;

                                for($match_round = $initial_match_round; $match_round <=15; ++$match_round) {
                                    $where = array(
                                        'team_id' => $this->mg_profile->team_id,
                                        'season' => $season
                                    );
                                    $training_team->where($where)->get();
                                    $season_training_trained = explode('-', $training_team->training_type);
                                    $training_trained = $season_training_trained[$match_round];

                                    $training_type = new Training_const_type();
                                    $training_type->where('id', $training_trained)->get();
                                    $skill_id = $training_type->skill_id;

                                    if($skill_id != $last->skill_id) {
                                        ++$prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'];
                                    } else {
                                        $training_player = new Training_player();
                                        $where = array (
                                            'team_id' => $this->mg_profile->team_id,
                                            'player_id' => $squad_player->player_id,
                                            'season' => $season
                                        );
                                        $training_player->where($where)->get();
                                        $percentage_trained = explode('-', $training_player->percentage_trained);
                                        $minutes_trained = explode('-', $training_player->minutes_trained);

                                        if(isset($percentage_trained[$match_round]) AND isset($minutes_trained[$match_round]))
                                            $percentage = $percentage_trained[$match_round] * $minutes_trained[$match_round] /90;
                                        else
                                            $percentage = 0;

                                        ++$prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'];
                                        $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'] -= $percentage/100;
                                    }

                                    if($prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round']>16) {
                                        $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round'] = 1;
                                        ++$prediction[$squad_player->player_id][$last->skill_id]['next_training_event_season'];
                                    }

                                    // Prediction succeeded
                                    if($season == $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_season'] AND $match_round >= $prediction[$squad_player->player_id][$last->skill_id]['next_training_event_match_round']) {
                                        $flag_done = TRUE;
                                    }
                                    // Season exceed
                                    if($prediction[$squad_player->player_id][$last->skill_id]['next_training_event_season'] > $last_season) {
                                        $flag_done = TRUE;
                                    }
                                    if($flag_done) {
                                        break;
                                    }
                                }
                                if($flag_done) {
                                    break;
                                }
                            }
                        }

                    }
                }
                if(isset($prediction[$squad_player->player_id])) {
                    foreach ($prediction[$squad_player->player_id] as $row) {
                        if($row['next_training_event_season'] <= $last_season) {

                            $training_prediction = new Training_prediction();
                            $training_prediction->team_id = $this->mg_profile->team_id;
                            $training_prediction->player_id = $squad_player->player_id;
                            $training_prediction->skill_id = $row['skill_id'];
                            $training_prediction->season = $row['next_training_event_season'];
                            $training_prediction->match_round = $row['next_training_event_match_round'];

                            if(!$training_prediction->save_as_new())
                                DM_log($training_prediction);
                        }
                    }

                }
            }
        }
    }

    function minutes_trained() {

        if($_POST['response'] == 'no') {
            $training_player = new Training_player();
            $where = array(
                'team_id' => $_POST['team_id'],
                'player_id' => $_POST['player_id'],
                'season' => $_POST['season']
            );
            $training_player->where($where)->get();
            $new_percentage_trained = explode('-', $training_player->new_percentage_trained);
            $new_minutes_trained = explode('-', $training_player->new_minutes_trained);

            $json['new_percentage_trained'] = $new_percentage_trained[$_POST['match_round']];
            $json['new_minutes_trained'] = $new_minutes_trained[$_POST['match_round']];
            echo json_encode($json);
        } else {
            $training_player = new Training_player();
            $data = $_POST['data'];
            $where = array(
                'team_id' => $data[0],
                'player_id' => $data[1],
                'season' => $data[2]
            );
            $training_player->where($where)->get();
            $match_round = $data[3];

            $new_percentage_trained = explode('-', $training_player->new_percentage_trained);
            $new_minutes_trained = explode('-', $training_player->new_minutes_trained);
            $auto_set = explode('-', $training_player->auto_set);
            if($_POST['response']) {

                $percentage_trained = explode('-', $training_player->percentage_trained);
                $minutes_trained = explode('-', $training_player->minutes_trained);

                $percentage_trained[$match_round] = $new_percentage_trained[$match_round];
                $minutes_trained[$match_round] = $new_minutes_trained[$match_round];

                $new_percentage_trained[$match_round] = 0;
                $new_minutes_trained[$match_round] = 0;
                $auto_set[$match_round] = 1;

                $training_player->percentage_trained = implode('-', $percentage_trained);
                $training_player->minutes_trained = implode('-', $minutes_trained);
            }
            elseif(!$_POST['response']) {
                $new_percentage_trained[$match_round] = 0;
                $new_minutes_trained[$match_round] = 0;
                $auto_set[$match_round] = 2;
            }

            $training_player->new_percentage_trained = implode('-', $new_percentage_trained);
            $training_player->new_minutes_trained = implode('-', $new_minutes_trained);
            $training_player->auto_set = implode('-', $auto_set);

            if($training_player->save())
                DM_log($training_player);
        }
    }
}

/* End of file home.php */
/* Location: ./application/modules/home/controllers/home.php */