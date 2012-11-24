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
class Player_model extends CI_Model {

    function getPlayer($playerID, $team_id) {

        $this->db->where('PlayerID', $playerID);
        $this->db->where('team_id', $team_id);
        $query = $this->db->get('ht_players');

        return $query->row();
    }

}