<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Blog module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Blog
 */
class Module_Manager extends Module {

    public $version = '1.20.929';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Manager',
                'spanish' => 'Manager',
            ),
            'description' => array(
                'en' => 'Base for the others modules of Hattrick MG.',
                'spanish' => 'Base para los otros modulos de Hattrick MG.',
            ),
            'frontend'	=> false,
            'backend'	=> true,
            'skip_xss'	=> true,
            'menu'		=> 'content',
        );
    }

    public function install()
    {
        $this->dbforge->drop_table('mg_profiles');

        $tables = array (
            'mg_profiles' => array (
                'id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'user_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'unique' => true),
                'username' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'unique' => true),
                'team_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => true, 'unique' => true, 'key' => true),
                'team_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'unique' => true, 'key' => true),
                'user_token' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => true),
                'user_token_secret' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => true),
                'valid_token' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'default' => 0),
                'league_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => true),
                'season' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => true),
                'match_round' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => true),
                'training_date' => array('type' => 'DATETIME', 'null' => true),
                'economy_date' => array('type' => 'DATETIME', 'null' => true),
                'cup_match_date' => array('type' => 'DATETIME', 'null' => true),
                'series_match_date' => array('type' => 'DATETIME', 'null' => true),
                'currency_name' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => true),
                'currency_rate' => array('type' => 'FLOAT', 'null' => true),
            ),
        );

        $query = $this->db->get('users');
        foreach ($query->result() as $row) {
            $manager_profiles[] = array('user_id' => $row->id, 'username' => $row->username);
        }

        // Let's try running our DB Forge Table and inserting some settings
        if ( ! $this->install_tables($tables))
        {
            return FALSE;
        }

        // Let's try running our DB Forge Table and inserting some settings
        if ( ! $this->db->insert_batch('mg_profiles', $manager_profiles) )
        {
            return FALSE;
        }

        return TRUE;
    }

    public function uninstall()
    {
        $this->dbforge->drop_table('mg_profiles');

        return TRUE;
    }

    public function upgrade($old_version)
    {
        return true;
    }
}