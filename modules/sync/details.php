<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Blog module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Blog
 */
class Module_Sync extends Module {

    public $version = '1.21.012';

    public function info()
    {
        return array(
            'name' => array(
                'en' => 'Synchronization',
                'spanish' => 'Sincronización',
            ),
            'description' => array(
                'en' => 'Synchronization with Hattrick.',
                'spanish' => 'Sincronización con Hattrick.',
            ),
            'frontend'	=> true,
            'backend'	=> true,
            'skip_xss'	=> true,
            'menu'		=> 'content',
        );
    }

    public function install()
    {

        $this->dbforge->drop_table('mg_synchronizations');

        $tables = array(
            'mg_synchronizations' => array (
                'id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'auto_increment' => true, 'primary' => true),
                'user_id' => array('type' => 'INT', 'unsigned' => true, 'constraint' => 11, 'null' => false, 'key' => true),
                'start_date' => array('type' => 'DATETIME', 'null' => false, 'key' => true),
                'end_date' => array('type' => 'DATETIME', 'null' => true, 'key' => true),
                'position_queue' => array('type' => 'INT', 'constraint' => 11, 'null' => false),
            ),
        );

        // Let's try running our DB Forge Table and inserting some settings
        if ( ! $this->install_tables($tables))
        {
            return FALSE;
        }

        return TRUE;
    }

    public function uninstall()
    {
        $this->dbforge->drop_table('mg_synchronizations');

        return TRUE;
    }

    public function upgrade($old_version)
    {
        return true;
    }
}
