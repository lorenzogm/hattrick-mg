<?php


function get_role_bg_color($position_id) {
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

function get_bg_color($mg_profile, $season, $match_round) {
    $current_season = $mg_profile->season;
    $current_match_round = $mg_profile->match_round;

    if($season == $current_season AND $match_round == $current_match_round)
        $bg_color = "class=current_match_round";
    else
        $bg_color = NULL;
    return $bg_color;
}
?>