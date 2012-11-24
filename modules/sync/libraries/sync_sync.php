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

require_once BASEPATH . 'libraries/Form_validation' . EXT;

class Sync_sync {

    protected $CI;
    protected $mg_profile;

    protected $PHT;

    protected $consumer_key = 0;
    protected $consumer_secret = 0;
    protected $callback = 0;

    private $sync_block = TRUE;
    private $get_club = TRUE;
    private $duplicated_team = TRUE;

    private $squad_block = TRUE;
    private $build_roles = TRUE;
    private $get_players = TRUE;
    private $build_squad_table_col = TRUE;

    private $training_block = TRUE;
    private $update_last_training = TRUE;
    private $get_training_events = TRUE;
    private $build_seasons = TRUE;
    private $build_training_team = TRUE;
    private $build_training_player = TRUE;
    private $get_training_last_match = TRUE;

    private $economy_block = TRUE;
    private $get_weekly_economy = TRUE;
    private $build_seasonal_economy = TRUE;
    private $build_economy_market_price = TRUE;

    function __construct($user_id) {
        $this->CI = &get_instance();

        $this->CI->config->load('sync/config');

        foreach ($this->CI->config->config as $key => $value)
            if(isset($this->$key))
                $this->$key = $value;

        $pht_config = array(
            'consumer_key' => $this->consumer_key,
            'consumer_secret' => $this->consumer_secret,
            'callback' => $this->callback
        );

        $this->CI->load->library('sync/PHT', $pht_config);
        $this->PHT = new PHT($pht_config);

        $this->CI->load->model('manager/profile');
        $this->mg_profile = new Profile();
        $this->mg_profile->where('user_id', $user_id)->get();

        if($this->mg_profile->valid_token)
            $this->get_auth();

        $this->CI->load->language('sync/sync');
    }

    /**
     * sync::authorize()
     *
     * Solicitamos la autorizaci�n del usuario, mediante su usuario y contrase�a, para conectarnos a hattrick.org
     *
     * @return void
     */
    function authorize() {

        // Comprobamos si el usuario tiene ya 'tokens' guardadas en la base de datos. De ser as�, no spanish necesaria una nueva autorizaci�n.

        if ($this->mg_profile->valid_token)
            $this->get_data();

        // Si el usuario no ten�a 'tokens', vamos a necesitar su autorizaci�n.
        else {

            /*
              You must supply your chpp crendentials and a callback url.
              User will be redirected to this url after login
              You can add your own parameters to this url if you need,
              they will be kept on user redirection
             */
            try {
                // if you need some scope (rights) you have to set it as parameter
                $url = $this->PHT->getAuthorizeUrl();
            } catch (HTError $e) {
                echo $e->getMessage();
            }
            /*
              Be sure to store $HT in session before redirect user
              to Hattrick chpp login page
             */
            $_SESSION['PHT'] = serialize($this->PHT);

            // Redireccionamos al siguiente paso.
            redirect($url);
        }
    }

    /**
     * sync::save_tokens()
     *
     * Guardamos las 'tokens' en nuestra base de datos.
     *
     * @return void
     */
    function save_tokens() {

        $this->PHT = unserialize($_SESSION['PHT']);
        /*
          When user is redirected to your callback url
          you will received two parameters in url
          oauth_token and oauth_verifier
          use both in next function:
         */
        try {
            $this->PHT->retrieveAccessToken($_GET['oauth_token'], $_GET['oauth_verifier']);
            /*
              Now access is granted for your application
              You can save user token and token secret and/or request xml files
             */
            $this->mg_profile->user_id = $this->CI->current_user->user_id;
            $this->mg_profile->username = $this->CI->current_user->username;
            $this->mg_profile->user_token = $this->PHT->getOauthToken();
            $this->mg_profile->user_token_secret = $this->PHT->getOauthTokenSecret();
            $this->mg_profile->valid_token = 1;

            if(!$this->mg_profile->save())
                DM_log($this->mg_profile);

            // Redireccionamos al siguiente paso.
            redirect('sync');
        } catch (HTError $e) {
            echo $e->getMessage();
        }
    }

    /**
     * sync::get_data()
     *
     * Obtenemos todos los datos.
     *
     * @return void
     */
    function get_data() {

        try {
            // Get club xml and save into DB
            set_time_limit(0);

            if($this->sync_block) {
                if($this->get_club)
                    $this->get_club();

                if($this->duplicated_team)
                    $this->duplicated_team();
            }

            if($this->squad_block) {
                $this->CI->load->library('squad/sync_squad');
                $squad = new Sync_squad($this->mg_profile->user_id);
                if($this->build_roles)
                    $squad->build_roles();
                if($this->get_players)
                    $squad->get_players();
                if($this->build_squad_table_col)
                    $squad->build_squad_table_col();
            }

            $this->CI->load->model('squad/squad_player');
            $player = new Squad_player();
            $player->where('team_id', $this->mg_profile->team_id)->get();

            if($this->training_block) {
                $this->CI->load->library('training/sync_training');
                $training = new Sync_training($this->mg_profile->user_id);

                if($this->update_last_training)
                    $training->update_last_training();
                if($this->get_training_events)
                    foreach ($player as $player_row)
                        $training->get_training_events($player_row->player_id);
                if($this->build_seasons)
                    $training->build_seasons();
                if($this->build_training_team)
                    $training->build_training_team();
                if($this->build_training_player)
                    foreach ($player as $player_row)
                        $training->build_training_player($player_row->player_id);
                if($this->get_training_last_match)
                    foreach ($player as $player_row)
                        $training->get_training_last_match($player_row->player_id);
            }

            /*
            if($this->economy_block) {
                dump('economy');
                $this->CI->load->library('sync/sync_economy');
                $economy = new Sync_economy($this->mg_profile->user_id);
                if($this->get_weekly_economy)
                    $economy->get_weekly_economy();
                if($this->build_seasonal_economy)
                    $economy->build_seasonal_economy();
                if($this->build_economy_market_price)
                    $economy->build_economy_market_price($player_id);
            }
*/
            /*
             You can save $HT to session and reuse it on others pages.
             Connection is persisted in PHT instance.
            */

            $synchronization = new Synchronization();
            $where = array(
                'user_id' => $this->mg_profile->user_id,
                'position_queue !=' => 0
            );
            $date_time = new DateTime();
            $update = array(
                'end_date' => (string) $date_time->format('Y-m-d H:i:s'),
                'position_queue' => 0
            );

            if(!$synchronization->where($where)->update($update))
                DM_log($synchronization);

        } catch (HTError $e) {
            $this->mg_profile->valid_token = 0;
            db_save($this->mg_profile);

            $synchronization = new Synchronization();
            $where = array ('user_id' => $this->mg_profile->user_id);
            db_delete_all($synchronization, $where);

            echo $e->getMessage();
        }
    }

    /**
     * sync::get_auth()
     *
     * Realiza la autentificaci�n con las 'tokens' guardadas en la DB
     *
     * @return \CHPPConnection
     */
    function get_auth() {

        /*
          You don't need to login to Hattrick, you cas use
          user token (if you saved it) to retrieve xml
          Create PHT instance (no need to set a callback url)
          set user token and token secret, then request xml, it's easy :)
         */

        $this->PHT->setOauthToken($this->mg_profile->user_token);
        $this->PHT->setOauthTokenSecret($this->mg_profile->user_token_secret);
    }

    /**
     * sync::get_club()
     *
     * Obtenemos los datos del club y los guardamos en la DB
     *
     * @param   CHPPConnection  $HT         Clase con la que realizamos las consultas a hattrick.org
     * @return void
     */
    function get_club() {

        $team = $this->PHT->getTeam()->xmlText;
        $xml = simplexml_load_string($team);

        $this->mg_profile->team_id = (int) $xml->Team->TeamID;
        $this->mg_profile->team_name = (string) $xml->Team->TeamName;
        $this->mg_profile->league_id = (int) $xml->Team->League->LeagueID;

        $league = $this->PHT->getWorldDetailsByLeagueId($this->mg_profile->league_id)->xmlText;
        $xml = simplexml_load_string($league);
        $this->mg_profile->season = (int) $xml->LeagueList->League->Season;
        $this->mg_profile->match_round = (int) $xml->LeagueList->League->MatchRound;
        $this->mg_profile->economy_date = (string) $xml->LeagueList->League->EconomyDate;
        $this->mg_profile->cup_match_date = (string) $xml->LeagueList->League->CupMatchDate;

        $this->mg_profile->currency_name = (string) $xml->LeagueList->League->Country->CurrencyName;
        $currency_rate = (float) $xml->LeagueList->League->Country->CurrencyRate;
        $this->mg_profile->currency_rate = (float) str_replace(',', '.', $currency_rate);

        // Actualizamos el perfil del usuario con los datos recopilados de su equipo
        if(!$this->mg_profile->save())
            DM_log($this->mg_profile);
    }

    function duplicated_team() {

        /*
            $this->CI->load->model('user_panel/report');
            $report = new Report();
            $report->type = 'Duplicated team';
            $report->report = 'The new user ('.$this->mg_profile->user_id.') has the same team ('.$mg_profile->team_id.') than a previous user ('.$mg_profile->user_id.')';
            $report->user_id = $this->mg_profile->user_id;
            $datetime = new DateTime('now');
            $report->date = (string) $datetime->format('Y-m-d H:i:s');
            $report->resolved = 0;

            db_save($report);

            set_flashdata('El equipo que estás intentando sincronizar ya pertenece a otro usuario');
            redirect('home/message');
        */
    }
}

?>
