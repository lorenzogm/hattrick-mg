<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Catalogo_model   Modelo para el módulo catálogo
 * 
 * @package     catalogo
 * @author      willaser
 * @link        http://imhosb.com
 */
class Economy_model extends CI_Model {

    function getSeasonalEconomies($team_id, $season = NULL) {

        $this->db->where('team_id', $team_id);
        if(is_null($season))
            $this->db->order_by('Season', 'ASC');
        else
            $this->db->where('Season', $season);
        $query = $this->db->get('mg_economy_seasonal');

        if(is_null($season))
            return $query->result();
        else
            return $query->row();
    }

    function getEconomySeasonalFields() {
        $fields = $this->db->list_fields('mg_economy_seasonal');

        for($i=0;$i<=1;++$i)
            unset($fields[$i]);

        return $fields;
    }

    function getCurrentSeason ($team_id) {
        $this->db->select_max('Season');
        $this->db->where('team_id', $team_id);
        $query = $this->db->get('mg_training_teams');

        return $query->row('Season');
    }

    function getSeasons ($team_id) {
        $this->db->where('team_id', $team_id);
        $this->db->order_by('Season', 'DESC');
        $query = $this->db->get('mg_training_teams');
        $result = $query->result();

        $seasons = array();
        foreach ($result as $row) {
            $flag = FALSE;
            foreach ($seasons as $season)
                if ($season == $row->Season)
                    $flag = TRUE;
            if (!$flag)
                $seasons[] = $row->Season;
        }
        return $seasons;
    }

    function isExists($team_id, $season) {
        $this->db->where('team_id', $team_id);
        $this->db->where('Season', $season);
        $cols = $this->db->get('mg_economy_seasonal');

        return $cols->num_rows();
    }

    function insertSeason($insertData) {
        $this->db->insert('mg_economy_seasonal', $insertData);
    }

    function updateSeason($updateData, $team_id, $season) {
        $this->db->where('team_id', $team_id);
        $this->db->where('Season', $season);
        $this->db->update('mg_economy_seasonal', $updateData);
    }

    function getPlayersToShow ($team_id) {
        $this->db->where('team_id', $team_id);
        $this->db->where('ShowOnTraining', TRUE);
        $query = $this->db->get('mg_squad_preferences_roles');

        foreach ($query->result() as $row) {
            $rolesToShow[] = $row->ID;
        }

        $this->db->where('team_id', $team_id);
        $this->db->where_in('RolID', $rolesToShow);
        $this->db->order_by('RolID', 'ASC');
        $query = $this->db->get('mg_squad_players');

        return $query->result();
    }

    function getEconomyMarketPrice($team_id, $season, $playerToShow = NULL) {

        if(isset($playerToShow))
            foreach ($playerToShow as $player) {
                $this->db->where('team_id', $team_id);
                $this->db->where('Season', $season);
                $this->db->where('PlayerID', $player);
                $this->db->group_by('PlayerID');
                $this->db->order_by('ID', 'ASC');
                $query = $this->db->get('mg_economy_marketprice');
                $result = $query->row();

                if($result != NULL)
                    $array[] = $result;
            }
        return $array;
    }

    function getPlayer($team_id, $playerID) {

        $this->db->where('team_id', $team_id);
        $this->db->where('PlayerID', $playerID);
        $query = $this->db->get('mg_squad_players');

        return $query->row();
    }

    function getPrice($team_id, $playerID, $season, $matchRound){
        $this->db->where('team_id', $team_id);
        $this->db->where('PlayerID', $playerID);
        $this->db->where('Season', $season);
        $this->db->where('MatchRound', $matchRound);
        $query = $this->db->get('mg_economy_marketprice');

        return $query->row('Price');
    }

}