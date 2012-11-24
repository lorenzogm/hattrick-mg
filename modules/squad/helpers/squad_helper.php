<?php

function display_flag($flag_id) {

    echo img(array('src' => site_url(ADDONPATH.'modules/squad/img/flags/'.$flag_id.'flag.gif'), 'class' => 'flag'));
}

function display_injuries($injury_level) {

    if($injury_level == 0)
        echo img(array('src' => site_url(ADDONPATH.'modules/squad/img/injuries/bruised.png'), 'title' => 'Lastimado', 'class' => 'injury'));
    else if ($injury_level >0) {
        echo $injury_level;
        if($injury_level == 1)
            $description = 'Lesionado: '.$injury_level.' semana';
        else
            $description = 'Lesionado: '.$injury_level.' semanas';
    echo img(array('src' => site_url(ADDONPATH.'modules/squad/img/injuries/injured.png'), 'title' => $description, 'class' => 'injury'));
    }
}

function display_specialty($specialty_id, $specialty_description) {
    if($specialty_id != 0)
        echo img(array('src' => site_url(ADDONPATH.'modules/squad/img/specialties/spec'.$specialty_id.'.png'), 'title' => $specialty_description, 'class' => 'specialty'));
}

function display_cards($cards_id) {

    if($cards_id != 0) {
        if($cards_id == 3)
            $description = 'Sancionado';
        else
            $description = 'La tercera acarrea suspensión';
        echo img(array('src' => site_url(ADDONPATH.'modules/squad/img/cards/'.$cards_id.'.png'), 'title' => $description, 'class' => 'cards'));
    }
}

function display_market($market_id) {

    if($market_id) {
        $description = 'Transferible';
        echo img(array('src' => site_url(ADDONPATH.'modules/squad/img/market.png'), 'title' => $description, 'class' => 'market'));
    }
}

function display_player_value ($key, $player, $players_previous_week, $role_order = NULL) {

    $CI = & get_instance();
    $CI->load->model('manager/profile');
    $CI->mg_profile = new Profile();
    $CI->mg_profile->where('user_id', $CI->current_user->id)->get();

    if($key == 'salary')
        $player_value = ($player->$key)/$CI->mg_profile->currency_rate;
    else
        $player_value = $player->$key;

    $data['key'] = $key;
    $data['role_id'] = $player->role_id;
    $data['role_order'] = $role_order;
    $data['value'] = $player_value;
    $data['difference_value'] = 0;
    $data['difference_flag'] = 'zero';

    if($players_previous_week != NULL) {

        if($key == 'salary')
            $player_last_training_value = ($players_previous_week->$key)/$CI->mg_profile->currency_rate;
        else
            $player_last_training_value = $players_previous_week->$key;

        if($player_value < $player_last_training_value)
            $data['difference_flag'] = 'negative';
        elseif($player_value == $player_last_training_value)
            $data['difference_flag'] = 'zero';
        elseif($player_value > $player_last_training_value)
            $data['difference_flag'] = 'positive';

        $data['difference_value'] = $player_value - $player_last_training_value;
    }

    $CI->load->view('player_skill', $data);
}

function display_player_individual_order($individual_order_id) {
    ?>
    <img
        src="<?php echo site_url(ADDONPATH.'modules/squad/img/individuals_orders/IndividualOrder'.$individual_order_id.'.png');?>"
        title="<?php echo lang('individual_order_'.$individual_order_id);?>"
        class="individual_order"
        />
    <?php
}

function get_bg_color($position_id) {
    switch($position_id) {
        case 1:
            return '#B40404';
        case 2:
            return '#FF8000';
        case 3:
            return '#B45F04';
        case 4:
            return '#AEB404';
        case 5:
            return '#04B431';
        case 6:
            return '#0174DF';
        case 7:
            return '#848484';
        case 8:
            return '#000000';
        case 9:
            return '#0B0B61';
        case 10:
            return '#610B5E';
    }
}
?>
