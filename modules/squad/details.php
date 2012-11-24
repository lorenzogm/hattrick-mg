<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Blog module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Blog
 */
class Module_Squad extends Module {

    public $version = '1.21.012';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Squad',
                'spanish' => 'Plantilla',
            ),
            'description' => array(
                'en' => 'Display a practical view of the players of the squad.',
                'spanish' => 'Muestra una práctiva vista de los jugadores de la plantilla.',
            ),
            'frontend'	=> true,
            'backend'	=> false,
            'skip_xss'	=> true,
            'menu'		=> 'content',
        );
    }

    public function install()
    {
        $this->dbforge->drop_table('mg_squad_players');
        $this->dbforge->drop_table('mg_squad_player_previous_weeks');
        $this->dbforge->drop_table('mg_squad_preferences_roles');
        $this->dbforge->drop_table('mg_squad_preferences_table_cols');

        $this->dbforge->drop_table('mg_squad_const_table_cols');
        $this->dbforge->drop_table('mg_squad_const_players_contributions');

        $tables = array(
            'mg_squad_players' => array(
                'id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'team_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'key' => true),
                'team_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'player_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'unique' => true),
                'role_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'first_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'last_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'age' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'age_days' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'tsi' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'player_form' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'experience' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'loyalty' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'mother_club_bonus' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'leadership' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'salary' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'country_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'is_abroad' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'specialty' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'transfer_listed' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'sold' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'cards' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'injury_level' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'stamina_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'keeper_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'playmaker_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'scorer_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'passing_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'winger_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'defender_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'set_pieces_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'score' => array('type' => 'FLOAT', 'unsigned' => true, 'null' => false),
                'individual_order' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
            ),
            'mg_squad_player_previous_weeks' => array(
                'id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'team_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'key' => true),
                'team_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'player_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'unique' => true),
                'role_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'first_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'last_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
                'age' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'age_days' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'tsi' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'player_form' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'experience' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'loyalty' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'mother_club_bonus' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'leadership' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'salary' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'country_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'is_abroad' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'specialty' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'transfer_listed' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'sold' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'cards' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'injury_level' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
                'stamina_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'keeper_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'playmaker_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'scorer_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'passing_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'winger_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'defender_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'set_pieces_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'score' => array('type' => 'FLOAT', 'unsigned' => true, 'null' => false),
                'individual_order' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
            ),
            'mg_squad_preferences_roles' => array(
                'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'team_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'key' => true),
                'is_default' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'position_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'key' => true),
                'custom_role_label' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => true),
                'custom_bg_color' => array('type' => 'VARCHAR', 'constraint' => 11, 'null' => true),
                'show_on_training' => array('type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false),
            ),
            'mg_squad_preferences_table_cols' => array(
                'id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'team_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'key' => true),
                'col_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'key' => true),
                'is_checked' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
            ),
            'mg_squad_const_table_cols' => array(
                'id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'col_label' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => false),
            ),
            'mg_squad_const_players_contributions' => array(
                'id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'position' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'number_of_players' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'individual_order' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'center_back_keeper_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'center_back_defender_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'wing_back_keeper_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'wing_back_defender_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'midfield_playmaker_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'wing_attack_winger_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'wing_attack_passing_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'wing_attack_scorer_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'central_attack_scorer_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
                'central_attack_passing_skill' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false),
            ),
        );

        $squad_const_table_cols = array (
            array ('id' => 1, 'col_label' => 'role'),
            array ('id' => 2, 'col_label' => 'country_id'),
            array ('id' => 3, 'col_label' => 'first_name'),
            array ('id' => 4, 'col_label' => 'age'),
            array ('id' => 5, 'col_label' => 'tsi'),
            array ('id' => 6, 'col_label' => 'salary'),
            array ('id' => 7, 'col_label' => 'state'),
            array ('id' => 8, 'col_label' => 'specialty'),
            array ('id' => 9, 'col_label' => 'player_form'),
            array ('id' => 10, 'col_label' => 'experience'),
            array ('id' => 11, 'col_label' => 'stamina_skill'),
            array ('id' => 12, 'col_label' => 'playmaker_skill'),
            array ('id' => 13, 'col_label' => 'winger_skill'),
            array ('id' => 14, 'col_label' => 'scorer_skill'),
            array ('id' => 15, 'col_label' => 'passing_skill'),
            array ('id' => 16, 'col_label' => 'defender_skill'),
            array ('id' => 17, 'col_label' => 'keeper_skill'),
            array ('id' => 18, 'col_label' => 'set_pieces'),
            array ('id' => 19, 'col_label' => 'loyalty'),
            array ('id' => 20, 'col_label' => 'score'),
        );

        $contribution_fields = array (
            'id',
            'position',
            'number_of_players',
            'individual_order',
            'center_back_keeper_skill',
            'center_back_defender_skill',
            'wing_back_keeper_skill',
            'wing_back_defender_skill',
            'midfield_playmaker_skill',
            'wing_attack_winger_skill',
            'wing_attack_passing_skill',
            'wing_attack_scorer_skill',
            'central_attack_scorer_skill',
            'central_attack_passing_skill',
        );

        $contributions_values = array (
            array (1, 1, 1, 1, 0, 90, 44, 60, 28, 0, 0, 0, 0, 0),
            array (2, 2, 1, 1, 0, 100, 0, 52, 28, 0, 0, 0, 0, 0),
            array (3, 2, 1, 2, 0, 75, 0, 38, 38, 0, 0, 0, 0, 0),
            array (4, 2, 1, 4, 0, 81, 0, 68, 19, 29, 0, 0, 0, 0),
            array (5, 2, 2, 1, 0, 96, 0, 50, 27, 0, 0, 0, 0, 0),
            array (6, 2, 2, 2, 0, 72, 0, 36, 36, 0, 0, 0, 0, 0),
            array (7, 2, 2, 4, 0, 78, 0, 66, 19, 28, 0, 0, 0, 0),
            array (8, 2, 3, 1, 0, 88, 0, 46, 25, 0, 0, 0, 0, 0),
            array (9, 2, 3, 2, 0, 66, 0, 33, 33, 0, 0, 0, 0, 0),
            array (10, 2, 3, 4, 0, 71, 0, 60, 17, 26, 0, 0, 0, 0),
            array (11, 3, 1, 1, 0, 47, 0, 92, 17, 53, 0, 0, 0, 0),
            array (12, 3, 1, 2, 0, 40, 0, 70, 23, 65, 0, 0, 0, 0),
            array (13, 3, 1, 3, 0, 52, 0, 100, 7, 34, 0, 0, 0, 0),
            array (14, 3, 1, 5, 0, 71, 0, 69, 17, 34, 0, 0, 0, 0),
            array (15, 4, 1, 1, 0, 42, 0, 19, 100, 0, 24, 0, 0, 34),
            array (16, 4, 1, 2, 0, 22, 0, 10, 94, 0, 24, 0, 0, 51),
            array (17, 4, 1, 3, 0, 62, 0, 27, 94, 0, 15, 0, 0, 23),
            array (18, 4, 1, 4, 0, 36, 0, 25, 88, 57, 30, 0, 0, 24),
            array (19, 4, 2, 1, 0, 39, 0, 18, 93, 0, 22, 0, 0, 32),
            array (20, 4, 2, 3, 0, 21, 0, 10, 88, 0, 22, 0, 0, 47),
            array (21, 4, 2, 4, 0, 57, 0, 25, 88, 0, 14, 0, 0, 22),
            array (22, 4, 2, 4, 0, 34, 0, 23, 82, 53, 28, 0, 0, 22),
            array (23, 4, 3, 1, 0, 34, 0, 16, 83, 0, 20, 0, 0, 28),
            array (24, 4, 3, 2, 0, 19, 0, 8, 78, 0, 20, 0, 0, 42),
            array (25, 4, 3, 3, 0, 51, 0, 22, 78, 0, 13, 0, 0, 19),
            array (26, 4, 3, 4, 0, 30, 0, 21, 73, 47, 25, 0, 0, 20),
            array (27, 5, 1, 1, 0, 21, 0, 35, 52, 90, 23, 0, 0, 11),
            array (28, 5, 1, 2, 0, 8, 0, 17, 44, 100, 27, 0, 0, 14),
            array (29, 5, 1, 3, 0, 26, 0, 46, 44, 76, 19, 0, 0, 6),
            array (30, 5, 1, 5, 0, 25, 0, 28, 66, 59, 15, 0, 0, 16),
            array (31, 6, 1, 1, 0, 0, 0, 0, 0, 19, 13, 24, 37, 100),
            array (32, 6, 1, 3, 0, 0, 0, 0, 42, 13, 24, 12, 57, 61),
            array (33, 6, 1, 4, 0, 0, 0, 0, 0, 43, 19, 40, 26, 61),
            array (34, 6, 1, 6, 0, 0, 0, 0, 42, 13, 35, 12, 57, 61),
            array (35, 6, 2, 1, 0, 0, 0, 0, 0, 18, 12, 22, 32, 94),
            array (36, 6, 2, 3, 0, 0, 0, 0, 39, 12, 22, 11, 54, 58),
            array (37, 6, 2, 4, 0, 0, 0, 0, 0, 41, 18, 38, 25, 58),
            array (38, 6, 2, 6, 0, 0, 0, 0, 39, 12, 33, 11, 54, 58),
            array (39, 6, 3, 1, 0, 0, 0, 0, 0, 17, 11, 20, 32, 86),
            array (40, 6, 3, 3, 0, 0, 0, 0, 36, 11, 21, 10, 49, 53),
            array (41, 6, 3, 4, 0, 0, 0, 0, 0, 37, 16, 34, 22, 53),
            array (42, 6, 3, 6, 0, 0, 0, 0, 36, 11, 30, 10, 49, 53),
        );

        $squad_const_contributions = array ();
        foreach ($contributions_values as $key => $value)
            foreach ($contribution_fields as $field_key => $field_value)
                $squad_const_contributions[$key][$field_value] = $value[$field_key];

        // Let's try running our DB Forge Table and inserting some settings
        if ( ! $this->install_tables($tables))
        {
            return FALSE;
        }

        // Let's try running our DB Forge Table and inserting some settings
        if (
            ! $this->db->insert_batch('mg_squad_const_table_cols', $squad_const_table_cols)
            OR
            ! $this->db->insert_batch('mg_squad_const_players_contributions', $squad_const_contributions)
        )
        {
            return FALSE;
        }

        return TRUE;
    }

    public function uninstall()
    {
        $this->dbforge->drop_table('mg_squad_players');
        $this->dbforge->drop_table('mg_squad_player_previous_weeks');
        $this->dbforge->drop_table('mg_squad_preferences_roles');
        $this->dbforge->drop_table('mg_squad_preferences_table_cols');

        $this->dbforge->drop_table('mg_squad_const_table_cols');
        $this->dbforge->drop_table('mg_squad_const_players_contributions');

        return TRUE;
    }

    public function upgrade($old_version)
    {
        return true;
    }
}