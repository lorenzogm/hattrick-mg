<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Sync
 *
 * Sincronizaci�n con hattrick.org.
 * Toda la sincronizaci�n se hace fuera del entorno de CodeIgniter.
 *
 * @package
 *
 * @author ImHosb
 * @author www.imhosb.com
 *
 * @access public
 */
class Sync extends Public_Controller {

    /**
     * Sync::__construct()
     *
     * @return
     */
    function __construct() {
        parent::__construct();

        if(!isset($this->current_user->id))
            redirect('users/login');

        $this->load->model('manager/profile');
        $this->mg_profile = new Profile();
        $this->mg_profile->where('user_id', $this->current_user->id)->get();

        $this->load->language('sync');
    }

    /**
     * Sync::index()
     *
     * Muestra la vista con el men� de sincronizaci�n.
     *
     * @return
     */
    function index() {

        if(!$this->mg_profile->valid_token)
            $this->authorize();

        $synchronization = new Synchronization();
        $where = array(
            'user_id' => $this->current_user->id,
            'position_queue !=' => 0
        );
        $synchronization->where($where)->get();

        if($synchronization->result_count() == 0) {
            $synchronization->func('MAX', 'position_queue');
            $synchronization->get();

            $position_queue = $synchronization->position_queue;

            $dateTime = new DateTime();

            unset($synchronization->id);
            $synchronization->user_id = $this->current_user->id;
            $synchronization->start_date = (string) $dateTime->format('Y-m-d H:i:s');
            $synchronization->end_date = 0;
            $synchronization->position_queue = ++$position_queue;

            //db_save_as_new($synchronization);
            $synchronization->save_as_new();
        }

        $this->template
            ->title($this->module_details['name'])
            ->append_js('module::jquery-1.8.2.min.js')
            ->build('sync');
    }

    function authorize () {
        $this->load->library('sync_sync', $this->current_user->id);
        $this->sync_sync->authorize();
    }

    function save_tokens () {
        $this->load->library('sync_sync', $this->current_user->id);
        $this->sync_sync->save_tokens();
    }

    function get_data() {
        $this->load->library('sync_sync', $this->current_user->id);
        $this->sync_sync->get_data();
    }
}

/* End of file sync.php */
/* Location: ./app/modules/sync/controllers/sync.php */