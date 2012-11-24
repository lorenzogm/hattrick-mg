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

require_once ADDONPATH.'modules/sync/libraries/sync_sync' . EXT;

class Sync_squad extends Sync_sync {

    function __construct($user_id) {
        parent::__construct($user_id);
    }

    function build_roles() {

        $this->CI->load->model('squad/squad_preferences_role');
        $squad_preferences_roles = new Squad_preferences_role();
        $squad_preferences_roles->where('team_id', $this->mg_profile->team_id)->get();
        if($squad_preferences_roles->result_count() == 0) {

            for($i=1; $i<=10; ++$i) {
                $squad_preferences_roles->team_id = $this->mg_profile->team_id;
                $squad_preferences_roles->is_default = TRUE;
                $squad_preferences_roles->position_id = $i;
                $squad_preferences_roles->custom_bg_color = NULL;
                $squad_preferences_roles->show_on_training = TRUE;

                switch($i) {
                    case 7:
                        $squad_preferences_roles->show_on_training = 0;
                        break;
                    case 8:
                        $squad_preferences_roles->is_default = 0;
                        $squad_preferences_roles->show_on_training = 0;
                        break;
                    case 9:
                        $squad_preferences_roles->is_default = 0;
                        $squad_preferences_roles->show_on_training = 0;
                        break;
                    case 10:
                        $squad_preferences_roles->is_default = 0;
                        $squad_preferences_roles->show_on_training = 0;
                        break;
                }

                if(!$squad_preferences_roles->save_as_new())
                    DM_log($squad_preferences_roles);
            }
        }
    }

    function get_players() {
        $training = $this->PHT->getTraining()->xmlText;
        $xml = simplexml_load_string($training);
        $trainer_id = (int)$xml->Team->Trainer->TrainerID;
        // Obtenemos los datos del xml
        $players = $this->PHT->getTeamPlayers($this->mg_profile->team_id, TRUE)->xmlText;
        // Obtenemos los datos de nuevo sin formato xmlText para sacar el n�mero de jugadores de la plantilla
        $teamplayers = $this->PHT->getTeamPlayers();

        $xml = simplexml_load_string($players);

        $this->CI->load->model('squad/squad_player');
        $player = new Squad_player();
        if(!$player->where('team_id', $this->mg_profile->team_id)->update('sold',1))
            DM_log($player);

        // Haremos tantas iteraciones como jugadores haya en el equipo
        for ($i = 0; $i < $teamplayers->getNumberPlayers(); ++$i) {
            $where = array(
                'team_id' => (int) $xml->Team->TeamID,
                'player_id' => $xml->Team->PlayerList->Player[$i]->PlayerID
            );
            $player->where($where)->get();

            $player->team_id = (int) $xml->Team->TeamID;
            $player->team_name = (string) $xml->Team->TeamName;
            $player->player_id = (int) $xml->Team->PlayerList->Player[$i]->PlayerID;
            $player->first_name = (string) $xml->Team->PlayerList->Player[$i]->FirstName;
            $player->last_name = (string) $xml->Team->PlayerList->Player[$i]->LastName;
            $player->age = (int) $xml->Team->PlayerList->Player[$i]->Age;
            $player->age_days = (int) $xml->Team->PlayerList->Player[$i]->AgeDays;
            $player->tsi = (int) $xml->Team->PlayerList->Player[$i]->TSI;
            $player->player_form = (int) $xml->Team->PlayerList->Player[$i]->PlayerForm;
            $player->experience = (int) $xml->Team->PlayerList->Player[$i]->Experience;
            $player->loyalty = (int) $xml->Team->PlayerList->Player[$i]->Loyalty;
            $player->mother_club_bonus = (int) $xml->Team->PlayerList->Player[$i]->MotherClubBonus;
            $player->leadership = (int) $xml->Team->PlayerList->Player[$i]->Leadership;
            $player->salary = (int) $xml->Team->PlayerList->Player[$i]->Salary;
            $player->country_id = (int) $xml->Team->PlayerList->Player[$i]->CountryID;
            $player->is_abroad = (int) $xml->Team->PlayerList->Player[$i]->IsAbroad;
            $player->specialty = (int) $xml->Team->PlayerList->Player[$i]->Specialty;
            $player->transfer_listed = (int) $xml->Team->PlayerList->Player[$i]->TransferListed;
            $player->cards = (int) $xml->Team->PlayerList->Player[$i]->Cards;
            $player->injury_level = (int) $xml->Team->PlayerList->Player[$i]->InjuryLevel;
            $player->stamina_skill = (int) $xml->Team->PlayerList->Player[$i]->StaminaSkill;
            $player->keeper_skill = (int) $xml->Team->PlayerList->Player[$i]->KeeperSkill;
            $player->playmaker_skill = (int) $xml->Team->PlayerList->Player[$i]->PlaymakerSkill;
            $player->scorer_skill = (int) $xml->Team->PlayerList->Player[$i]->ScorerSkill;
            $player->passing_skill = (int) $xml->Team->PlayerList->Player[$i]->PassingSkill;
            $player->winger_skill = (int) $xml->Team->PlayerList->Player[$i]->WingerSkill;
            $player->defender_skill = (int) $xml->Team->PlayerList->Player[$i]->DefenderSkill;
            $player->set_pieces_skill = (int) $xml->Team->PlayerList->Player[$i]->SetPiecesSkill;

            // Role ID value
            $squad_preferences_roles = new Squad_preferences_role();
            if($player->role_id == NULL) {
                $this->CI->load->model('squad/squad_preferences_role');
                if($player->player_id == $trainer_id) {
                    $where = array(
                        'team_id' => $this->mg_profile->team_id,
                        'position_id' => 7
                    );
                } else {
                    $role = $this->get_player_role($player);
                        $where = array(
                            'team_id' => $this->mg_profile->team_id,
                            'position_id' => $role['position']
                        );
                }

                $squad_preferences_roles->where($where)->get();
                $player->role_id = $squad_preferences_roles->id;
            }

            // Score value
            $where = array(
                'id' => $player->role_id
            );
            $squad_preferences_roles->where($where)->get();

            if($squad_preferences_roles->position_id <= 6)
                $score = $this->get_player_score($player);
            else
                $score = array('score' => 0, 'individual_order' => 0);

            $player->score = $score['score'];
            $player->individual_order = $score['individual_order'];

            // Sold value
            $player->sold = 0;

            if(!$player->save())
                DM_log($player);

            $this->CI->load->model('squad/squad_player_previous_week');
            $squad_player_previous_week = new Squad_player_previous_week();
            $where = array(
                'team_id' => $player->team_id,
                'player_id' => $player->player_id
            );
            $squad_player_previous_week->where($where)->get();

            if($squad_player_previous_week->result_count() == 0) {
                foreach ($player->fields as $field) {
                    $squad_player_previous_week->$field = $player->$field;
                }
                unset($squad_player_previous_week->id);
                if(!$squad_player_previous_week->save())
                    DM_log($squad_player_previous_week);
            }
        }
    }

    function get_player_role ($player) {

        $this->CI->load->model('squad/squad_const_players_contribution');
        $contribution = new Squad_const_players_contribution();
            $where = array(
                'number_of_players' => 1,
                'individual_order' => 1
            );
        $contribution->where($where)->get();

        return $this->get_player_position($player, $contribution);
    }

    function get_player_score ($player) {
        $this->CI->load->model('squad/squad_preferences_role');
        $squad_preferences_role = new Squad_preferences_role();
        $squad_preferences_role->where('id', $player->role_id)->get();

        $this->CI->load->model('squad/squad_const_players_contribution');
        $contribution = new Squad_const_players_contribution();
        $where = array(
            'position' => $squad_preferences_role->position_id,
            'number_of_players' => 1
        );
        $contribution->where($where)->get();

        return $this->get_player_position($player, $contribution);
    }

    function get_player_position ($player, $contribution) {

        $contributions_by_position = array();
        $exits_contributions = FALSE;
        foreach ($contribution as $row) {
            $keeper_skill = $player->keeper_skill * ($row->center_back_keeper_skill + $row->wing_back_keeper_skill);
            $defender_skill = $player->defender_skill * ($row->center_back_defender_skill + $row->wing_back_defender_skill);
            $playmaker_skill = $player->playmaker_skill * ($row->midfield_playmaker_skill);
            $winger_skill = $player->winger_skill * ($row->wing_attack_winger_skill);
            $passing_skill = $player->passing_skill * ($row->wing_attack_passing_skill + $row->central_attack_passing_skill);
            $scorer_skill = $player->scorer_skill * ($row->wing_attack_scorer_skill + $row->central_attack_scorer_skill);

            $contributions_by_position[$row->id]  = $keeper_skill + $defender_skill + $playmaker_skill + $winger_skill + $passing_skill + $scorer_skill;

            $exits_contributions = TRUE;
        }

        if($exits_contributions) {
            $max = max($contributions_by_position);

            foreach ($contributions_by_position as $key => $value)
                if($max == $value) {
                    $contribution->where('id', $key)->get();
                    $data = array(
                        'position' => $contribution->position,
                        'individual_order' => $contribution->individual_order,
                        'score' => $value
                    );
                    return $data;
                }
        } else {
            $data = array(
                'position' => 0,
                'individual_order' => 0,
                'score' => 0
            );
            return $data;
        }
    }

    function build_squad_table_col() {

        $this->CI->load->model('squad/squad_preferences_table_col');
        $preferences_table_col = new Squad_preferences_table_col();

        $preferences_table_col->where('team_id', $this->mg_profile->team_id)->get();
        if($preferences_table_col->result_count() == 0) {
            $this->CI->load->model('squad/squad_const_table_col');
            $table_col = new Squad_const_table_col();
            $table_col->get();

            $preferences_table_col->team_id = $this->mg_profile->team_id;
            $preferences_table_col->is_checked = 1;

            foreach ($table_col as $col) {
                $preferences_table_col->col_id = $col->id;
                foreach ($preferences_table_col->fields as $field)
                    $preferences_table_col->$field;
                if(!$preferences_table_col->save_as_new())
                    DM_log($preferences_table_col);
            }
        }
    }
}

?>
