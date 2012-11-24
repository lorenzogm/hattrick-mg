<tbody>
<?php
    if(isset($preferences_roles->custom_bg_color))
        $bg_color = $preferences_roles->custom_bg_color;
    else
        $bg_color = get_role_bg_color($preferences_roles->position_id);
    ?>
<tr bgcolor="<?php echo $bg_color;?>">
    <td><?php echo $players->first_name . ' ' . $players->last_name;?></td>

    <?php for ($match_round = 0; $match_round <= 15; ++$match_round): ?>
    <?php
    $training_players = new Training_player();
    $where = array(
        'player_id' => $player->player_id,
        'team_id' => $player->team_id,
        'season' => $season
    );

    $cell_color = 'grey';
    $training_players->where($where)->get();
    if($training_players->result_count() == 0) {
        $training_percentage_trained[$match_round] = 0;
        $training_minutes_trained[$match_round] = 0;
        $training_auto_set[$match_round] = 1;
    } else {
        $training_players->where($where)->get();
        $training_percentage_trained = explode('-', $training_players->percentage_trained);
        $training_minutes_trained = explode('-', $training_players->minutes_trained);
        $training_auto_set = explode('-', $training_players->auto_set);

        if($training_percentage_trained[$match_round] == 100 AND $training_minutes_trained[$match_round] == '90')
            $cell_color = '79a93b';
        elseif($training_minutes_trained[$match_round] > '0')
            $cell_color = 'ca822e';
    }

    $where = array(
        'player_id' => $player->player_id,
        'team_id' => $player->team_id,
        'season' => $season,
        'match_round' => $match_round + 1,
        'skill_id !=' => 2
    );
    $training_events = new Training_event();
    $training_events->where($where)->get();
    ?>
    <td bgcolor="<?php echo $cell_color;?>" title="<?php  echo $training_minutes_trained[$match_round] ?> %">
        <?php if ($edit_view):?>
        <div><input class="input-training" type="number" min="0" max="90" step="1" name="<?php echo 'minutes_trained-'.$player->player_id.'-'.$match_round;?>" value="<?php  echo $training_minutes_trained[$match_round] ?>" maxlength="3"> m</div>
        <div><input class="input-training" type="number" min="0" max="100" step="1" name="<?php echo 'percentage_trained-'.$player->player_id.'-'.$match_round;?>" value="<?php  echo $training_percentage_trained[$match_round] ?>" maxlength="3"> %</div>
        <?php else:?>
        <?php foreach ($training_events as $training_event): ?>
            <?php if (!is_array($training_event)): ?>
                <?php
                if($training_event->new_level > $training_event->old_level)
                    $arrow = 'skill_level_up';
                else
                    $arrow = 'skill_level_down';
                ?>
                <img src="<?php echo site_url(ADDONPATH.'modules/training/img/skill_level/'.$arrow.'.png');?>" title="<?php echo lang('skill_'.$training_event->skill_id);?>" />
                <?php echo $training_event->new_level;?>
                <?php endif; ?>
            <?php endforeach; ?>
                    <?php if($training_auto_set[$match_round] == 0):?>
            <img
                src="<?php echo site_url(ADDONPATH.'modules/training/img/!.png');?>"
                alt="Minutos entrenados"
                class="confirm"
                id="<?php echo $player->team_id.'-'.$player->player_id.'-'.$season.'-'.$match_round.'-'.$player->first_name.' '.$player->last_name.'-'.site_url('training/minutes_trained');?>"
                />
        <?php endif;?>
        <?php
        foreach ($predictions as $prediction):?>
            <?php if($prediction->player_id == $player->player_id AND $prediction->match_round == $match_round):?>
                <img src="<?php echo site_url(ADDONPATH.'modules/training/img/training_predictor.png');?>" class="prediction" title="<?php echo lang('skill_'.$prediction->skill_id);?>" />
        <?php endif;?>
            <?php endforeach;?>
        <?php endif;?>
    </td>
    <?php endfor; ?>
</tr>
</tbody>