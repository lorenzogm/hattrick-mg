<?php

/**
 * sync
 *
 * Esta librería se encarga de todo el proceso de sincronización con hattrick para obtener datos.
 *
 * @package
 *
 * @author ImHosb
 * @author www.Imhosb.com
 *
 * @access public
 */
class Sync_training extends Sync_sync {

    /**
     * sync::__construct()
     *
     * @return sync
     */
    function __construct($user_id){
        parent::__construct($user_id);
    }

    function update_last_training() {
        dump('update_last_training');

        $league = $this->PHT->getWorldDetailsByLeagueId($this->mg_profile->league_id)->xmlText;
        $xml = simplexml_load_string($league);
        $season = (int) $xml->LeagueList->League->Season;
        $match_round = (int) $xml->LeagueList->League->MatchRound;
        $training_date = (string) $xml->LeagueList->League->TrainingDate;
        $series_match_date = (string) $xml->LeagueList->League->SeriesMatchDate;
        $last_training_date = $this->mg_profile->training_date;
        $last_series_match_date = $this->mg_profile->series_match_date;

        if(isset($last_training_date) AND $training_date != $last_training_date) {
            if($series_match_date >= $last_series_match_date) {
                --$match_round;
                if($match_round <= 0) {
                    $match_round += 16;
                    --$season;
                }
            }

            // Last training data
            $training = $this->PHT->getTraining()->xmlText;
            $xml = simplexml_load_string($training);

            $this->CI->load->model('training/training_team');
            $training_team = new Training_team();
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'season' => $season
            );

            $training_team->where($where)->get();
            $training_type = explode('-', $training_team->training_type);
            $training_type[$match_round] = (int) $xml->Team->LastTrainingTrainingType;
            $training_level = explode('-', $training_team->training_level);
            $training_level[$match_round] = (int) $xml->Team->LastTrainingTrainingLevel;
            $stamina_training_part = explode('-', $training_team->stamina_training_part);
            $stamina_training_part[$match_round] = (int) $xml->Team->LastTrainingStaminaTrainingPart;

            $training_team->team_id = $this->mg_profile->team_id;
            $training_team->season = $season;
            $training_team->training_type = implode('-', $training_type);
            $training_team->training_level = implode('-', $training_level);
            $training_team->stamina_training_part = implode('-', $stamina_training_part);

            if(!$training_team->save())
                DM_log($training_team);

            $this->CI->load->model('squad/squad_player');
            $squad_player = new Squad_player();
            $this->CI->load->model('squad/squad_player_previous_week');

            $squad_player->where('team_id', $this->mg_profile->team_id)->get();
            foreach($squad_player as $row) {
                $squad_player_previous_week = new Squad_player_previous_week();

                foreach ($row->fields as $field)
                    $squad_player_previous_week->$field = $row->$field;

                $insert_where = array (
                    'team_id' => $this->mg_profile->team_id,
                    'player_id' => $row->player_id
                );
                $squad_player_previous_week->where($insert_where)->get();
                if($squad_player_previous_week->result_count() == 0)
                    if(!$squad_player_previous_week->save_as_new())
                        DM_log($squad_player_previous_week);
                else
                    if($squad_player_previous_week->save())
                        DM_log($squad_player_previous_week);
            }

            $this->mg_profile->training_date = $training_date;
            $this->mg_profile->series_match_date = $series_match_date;

            if(!$this->mg_profile->save())
                DM_log($this->mg_profile);
        }
    }

    /**
     * sync::get_trainingevents()
     *
     * Obtenemos los datos de los eventos de entrenamiento del jugador y los guardamos en la DB
     *
     * @param   CHPPConnection  $HT         Clase con la que realizamos las consultas a hattrick.org
     * @param   int             $player_id  ID del jugador
     * @return void
     */
    function get_training_events($player_id) {
        dump('training events');

        $training_events = $this->PHT->getTrainingEvents($player_id)->xmlText;
        $xml = simplexml_load_string($training_events);

        $xml_training_events = (array)$xml->Player->TrainingEvents;
        foreach ($xml_training_events as $key => $value) {
            if($key == 'TrainingEvent') {
                if(isset($value[1]))
                    foreach ($value as $xml_training_event)
                        $this->get_training_events_support($player_id, $xml_training_event);
                else
                    $this->get_training_events_support($player_id, $value);
            }
        }
    }

    function get_training_events_support($player_id, $xml) {
        $filter = array (
            'player_id' => $player_id,
            'team_id' => $this->mg_profile->team_id,
            'skill_id' => (int) $xml->SkillID,
            'old_level' => (int) $xml->OldLevel,
            'new_level' => (int) $xml->NewLevel,
            'season' => (int) $xml->Season,
            'match_round' => (int) $xml->MatchRound,
            'day_number' => (int) $xml->DayNumber
        );

        $this->CI->load->model('training/training_event');
        $training_event = new Training_event();
        $training_event->where($filter)->get();
        if($training_event->result_count() == 0) {
            $training_event->player_id = $player_id;
            $training_event->team_id = $this->mg_profile->team_id;
            $training_event->skill_id = (int) $xml->SkillID;
            $training_event->old_level = (int) $xml->OldLevel;
            $training_event->new_level = (int) $xml->NewLevel;
            $training_event->season = (int) $xml->Season;
            $training_event->match_round = (int) $xml->MatchRound;
            $training_event->day_number = (int) $xml->DayNumber;

            if(!$training_event->save_as_new())
                DM_log($training_event);
        }
    }

    function build_seasons() {
        dump('training/build_seasons');

        $this->CI->load->model('training/training_event');
        $training_event = new Training_event();
        $training_event->where('team_id', $this->mg_profile->team_id)->group_by('season')->distinct()->get();

        $this->CI->load->model('training/training_season');
        $current_season_added = FALSE;
        foreach($training_event as $row) {
            $training_season = new Training_season();
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'season' => $row->season
            );
            $training_season->where($where)->get();
            if($training_season->result_count() == 0) {
                $training_season->team_id = $this->mg_profile->team_id;
                $training_season->season = $row->season;
                if(!$training_season->save_as_new())
                    DM_log($training_season);
            }
            if($row->season == $this->mg_profile->season)
                $current_season_added = TRUE;
        }

        if(!$current_season_added) {
            $training_season = new Training_season();
            $training_season->team_id = $this->mg_profile->team_id;
            $training_season->season = $this->mg_profile->season;

            if(!$training_season->save_as_new())
                DM_log($training_season);
        }
    }

    function build_training_team() {
        dump('training/build_training_team');

        $this->CI->load->model('training/training_season');
        $training_season = new Training_season();
        $training_season->where('team_id', $this->mg_profile->team_id)->get();

        $this->CI->load->model('training/training_team');
        foreach($training_season as $season) {
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'season' => $season->season
            );

            $training_team = new Training_team();
            $training_team->where($where)->get();
            if($training_team->result_count() == 0) {
                $training = $this->PHT->getTraining()->xmlText;
                $xml = simplexml_load_string($training);

                for($match_round = 0; $match_round <= 15; ++$match_round) {
                    $training_type[$match_round] = $xml->Team->TrainingType;
                    $training_level[$match_round] = $xml->Team->TrainingLevel;
                    $stamina_training_part[$match_round] = $xml->Team->StaminaTrainingPart;
                }
                $training_team->team_id = $this->mg_profile->team_id;
                $training_team->season = $season->season;
                $training_team->training_type = implode('-', $training_type);
                $training_team->training_level = implode('-', $training_level);
                $training_team->stamina_training_part = implode('-', $stamina_training_part);

                if($training_team->save_as_new())
                    DM_log($training_team);
            }
        }

    }

    /**
     * sync::buildTrainingPlayer()
     *
     * Insertamos filas en la tabla 'manager_training_players' donde almacenar el % entrenado por el jugador cada semana.
     * Se inserta una nueva fila por cada semana de la temporada actual. 16 filas en total por las 16 semanas que tiene 'Hattrick'.
     *
     * @param   CHPPConnection  $HT         Clase con la que realizamos las consultas a hattrick.org
     * @param   int             $player_id  ID del jugador
     * @return void
     */
    function build_training_player($player_id) {
        dump('training/build_training_player');

        $this->CI->load->model('training/training_season');
        $training_season = new Training_season();
        $training_season->where('team_id', $this->mg_profile->team_id)->get();

        foreach ($training_season as $season) {
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'player_id' => $player_id,
                'season' => $season->season
            );

            $this->CI->load->model('training/training_player');
            $training_player = new Training_player();
            $training_player->where($where)->get();
            if($training_player->result_count() == 0) {
                die('joder');
            }

        }
    }

    function get_training_last_match($player_id) {
        dump('training/build_training_last_match');

        $players = $this->PHT->getTeamPlayers($this->mg_profile->team_id, TRUE)->xmlText;
        $xml = simplexml_load_string($players);

        // Let's find the season and the match round which correspond to the last match
        $i=0;

        $last_match = array(
            'Date' => '0000-00-00 00:00:00',
            'PositionCode' => '0',
            'MintuesPlayed' => '0'
        );
        $exists_last_match = FALSE;
        foreach ($xml->Team->PlayerList->Player as $row) {
            if((int) $row->PlayerID == $player_id AND $xml->Team->PlayerList->Player[$i]->LastMatch->Date != NULL) {
                $last_match = (array)$xml->Team->PlayerList->Player[$i]->LastMatch;
                $exists_last_match = TRUE;
                break;
            }
            ++$i;
        }

        if($exists_last_match) {

            $league = $this->PHT->getWorldDetailsByLeagueId($this->mg_profile->league_id)->xmlText;
            $xml = simplexml_load_string($league);
            $current_season = $this->mg_profile->season;
            $current_match_round = $this->mg_profile->match_round;

            $fetched_date = new DateTime((string) $xml->FetchedDate);
            $last_match_date = new DateTime($last_match['Date']);
            $series_match_date = new DateTime($this->mg_profile->series_match_date);

            $fetched_date_week = $fetched_date->format('W');
            $last_match_date_week = $last_match_date->format('W');

            $fetched_date_day = $fetched_date->format('D');
            if($fetched_date_day == 'Sat' OR $fetched_date_day=='Sun')
                ++$fetched_date_week;

            $last_match_date_day = $last_match_date->format('D');
            $series_match_date_day = $series_match_date->format('D');

            $week_difference = $fetched_date_week - $last_match_date_week;

            while(1) {
                if($week_difference < 0) {
                    $fetched_date_week += 52;
                    $week_difference = $fetched_date_week - $last_match_date_week;
                }
                if($week_difference >=0)
                    break;
            }

            $last_match_match_round = $current_match_round - $week_difference;
            $last_match_season = $current_season;

            while(1) {
                if($last_match_match_round < 1) {
                    $last_match_match_round += 16;
                    --$last_match_season;
                }
                if($last_match_match_round >=1)
                    break;
            }

            if($last_match_date_day == $series_match_date_day)
                ++$last_match_match_round;

            // Our match rounds are from 0 to 15, not from 1 to 16
            --$last_match_match_round;

            // Season and match round got, let's update the percentage trained in that match
            $this->CI->load->model('training/training_team');
            $training_team = new Training_team();
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'season' => $last_match_season
            );
            $training_team->where($where)->get();
            $team_training_type = explode('-', $training_team->training_type);

            $this->CI->load->model('training/training_const_percentage');
            $training_percentage = new Training_const_percentage();
            $where = array(
                'training_type' => $team_training_type[$last_match_match_round],
                'match_role_id' => $last_match['PositionCode']
            );

            $training_percentage->where($where)->get();
            $minutes_trained_value = $last_match['PlayedMinutes'];
            $percentage_trained_value = $training_percentage->percentage;
            if($percentage_trained_value == NULL OR $percentage_trained_value == 0) {
                $minutes_trained_value = 0;
                $percentage_trained_value = 0;
            }

            $this->CI->load->model('training/training_player');
            $training_player = new Training_player();
            $where = array(
                'team_id' => $this->mg_profile->team_id,
                'player_id' => $player_id,
                'season' => $last_match_season
            );

            if(round($minutes_trained_value != 0)) {

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
                    $training_player->player_id = $player_id;
                    $training_player->season = $last_match_season;
                    $training_player->percentage_trained = implode('-', $percentage_trained);
                    $training_player->new_percentage_trained = implode('-', $new_percentage_trained);
                    $training_player->minutes_trained = implode('-', $minutes_trained);
                    $training_player->new_minutes_trained = implode('-', $new_minutes_trained);
                    $training_player->auto_set = implode('-', $auto_set);

                    if($training_player->save_as_new())
                        DM_log($training_player);
                }

                $training_player->where($where)->get();
                $player_training_auto_set = explode('-', $training_player->auto_set);
                if($player_training_auto_set[$last_match_match_round] == 1) {

                    $player_training_minutes_trained = explode('-', $training_player->minutes_trained);
                    if($player_training_minutes_trained[$last_match_match_round] != round($minutes_trained_value, 2)) {

                        $player_training_new_percentage_trained = explode('-', $training_player->new_percentage_trained);
                        $player_training_new_percentage_trained[$last_match_match_round] = $percentage_trained_value;

                        $player_training_new_minutes_trained = explode('-', $training_player->new_minutes_trained);
                        $player_training_new_minutes_trained[$last_match_match_round] = round($minutes_trained_value, 2);

                        $player_training_auto_set[$last_match_match_round] = 0;

                        $training_player->team_id = $this->mg_profile->team_id;
                        $training_player->player_id = $player_id;
                        $training_player->season = $last_match_season;
                        $training_player->new_percentage_trained = implode('-', $player_training_new_percentage_trained);
                        $training_player->new_minutes_trained = implode('-', $player_training_new_minutes_trained);
                        $training_player->auto_set = implode('-', $player_training_auto_set);

                        if($training_player->save())
                            DM_log($training_player);
                    }
                }

            }

        }
    }

}

?>
