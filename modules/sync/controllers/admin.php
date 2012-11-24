<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 * @author 		PyroCMS Dev Team
 * @package 	PyroCMS\Core\Modules\Blog\Controllers
 */
class Admin extends Admin_Controller
{

    /**
     * The constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index() {

        if($this->input->post('submit') != NULL) {
            $post = $this->input->post();
            unset($post['submit']);

            $this->load->model('user_panel/user_profile');
            $user_profile = new User_profile();
            $this->load->model('sync/synchronization');
            $synchronization = new Synchronization();
            switch ($post['type']) {

                case 'all':
                    $user_profile->get();

                    foreach ($user_profile as $row) {
                        if($row->valid_token) {
                            $where = array(
                                'user_id' => $row->user_id,
                                'position_queue !=' => 0
                            );
                            if($synchronization->where($where)->count() == 0)
                                $this->_build_sync($row->user_id);
                        }
                    }

                    $where = array(
                        'position_queue !=' => 0
                    );

                    break;

                case 'non_synced':
                    $where = array(
                        'position_queue !=' => 0
                    );

                    break;

                case 'user':
                    $user_profile->where('user_id', $post['user_id'])->get();
                    if($user_profile->valid_token) {
                        $this->_build_sync($post['user_id']);
                        $where = array(
                            'user_id' => $post['user_id'],
                            'position_queue !=' => 0
                        );
                    } else {
                        set_flashdata('El usuario no tiene ningún token válido.');
                        redirect('admin/add_syncs');
                    }
                    break;

                default:
                    set_flashdata('Error en el tipo de sincronización');
                    redirect('admin/add_syncs');
            }
            $synchronization = new Synchronization();
            $synchronization->where($where)->get();

            $this->load->library('sync/sync_sync');
            foreach ($synchronization as $row) {
                $user_profile = new User_profile();
                $user_profile->where('user_id', $row->user_id)->get();
                if($user_profile->valid_token) {
                    $sync = new Sync_sync($row->user_id);
                    $sync->get_data();
                }
            }
        }

        $this->load->model('manager/profile');
        $profile = new Profile();
        $profile->get();

        $this->template
            ->title($this->module_details['name'])
            ->set('profile', $profile)
            ->build('sync');
        $this->load->view('admin/index');
    }

    function _build_sync ($user_id) {
        $this->load->model('sync/synchronization');
        $synchronization = new Synchronization();
        $synchronization->select_max('position_queue')->get();
        $position_queue = $synchronization->position_queue;

        $dateTime = new DateTime();

        unset($synchronization->id);
        $synchronization->user_id = $user_id;
        $synchronization->start_date = (string) $dateTime->format('Y-m-d H:i:sP');
        $synchronization->end_date = 0;
        $synchronization->position_queue = ++$position_queue;

        db_save_as_new($synchronization);
    }
}

/* End of file sync.php */
/* Location: ./app/modules/sync/controllers/sync.php */