<button type="button" class="btn" onclick="location.href='squad/preferences'"><?php echo lang('label_preferences');?></button>

<form method="post" accept-charset="utf-8" action="<?php echo site_url(uri_string());?>">
    <input type="submit" name="submit" value="<?php echo lang('label_save_changes');?>" class="btn" />
    <table>
        <thead>
        <tr>
            <?php foreach ($table_cols as $col): ?>
            <?php $preferences_table_cols->where(array('col_id' => $col->id, 'team_id' => $mg_profile->team_id))->get()?>
            <?php if ($preferences_table_cols->is_checked): ?>
                <th>
                    <?php
                    if($this->uri->segment(4) == $col->col_label)
                        $url = site_url('manager/squad/order/'.$col->col_label.'/inv');
                    else
                        $url = site_url('manager/squad/order/'.$col->col_label);
                    ?>
                    <a href="<?php echo $url;?>">
                        <img
                                src="<?php echo site_url(ADDONPATH.'modules/squad/img/table_cols/'.CURRENT_LANGUAGE.'/'.$col->col_label.'.png');?>"
                                alt="<?php echo $col->col_label;?>"
                                />
                    </a>
                </th>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($players as $player): ?>
            <?php
            $player_previous_week = $players_previous_week->where('player_id', $player->player_id)->get();
            $player_role->where('id', $player->role_id)->get();
            if(isset($player_role->custon_bg_color))
                $bg_color = $player_role->custom_bg_color;
            else
                $bg_color = get_bg_color($player_role->position_id);
            ?>
        <tr style="background-color: <?php echo $bg_color ?>">
            <?php foreach ($table_cols as $col): ?>
            <?php $preferences_table_cols->where(array('col_id' => $col->id, 'team_id' => $mg_profile->team_id))->get()?>
            <?php $preferences_table_cols->stored;?>
            <?php if ($preferences_table_cols->is_checked): ?>
                <?php
                switch($col->id){
                    case '1':
                        echo '<td>';
                        echo form_dropdown($player->player_id, $roles, $player_role->position_id);
                        echo '</td>';
                        break;
                    case '2':
                        echo '<td>';
                        display_flag($player->country_id);
                        echo '</td>';
                        break;
                    case '3':
                        echo '<td>';
                        echo $player->first_name . ' ' . $player->last_name;
                        echo '</td>';
                        break;
                    case '4':
                        echo '<td>';
                        echo $player->age . '.' . $player->age_days;
                        echo '</td>';
                        break;
                    case '5':
                        display_player_value('tsi', $player, $player_previous_week);
                        break;
                    case '6':
                        display_player_value('salary', $player, $player_previous_week);
                        break;
                    case '7':
                        echo '<td>';
                        display_injuries($player->injury_level);
                        display_cards($player->cards);
                        display_market($player->transfer_listed);
                        echo '</td>';
                        break;
                    case '8':
                        echo '<td>';
                        display_specialty($player->specialty, lang('specialty_'.$player->specialty));
                        echo '</td>';
                        break;
                    case '9':
                        display_player_value('player_form', $player, $player_previous_week);
                        break;
                    case '10':
                        display_player_value('experience', $player, $player_previous_week);
                        break;
                    case '11':
                        display_player_value('stamina_skill', $player, $player_previous_week, $player_role->position_id);
                        break;
                    case '12':
                        display_player_value('playmaker_skill', $player, $player_previous_week, $player_role->position_id);
                        break;
                    case '13':
                        display_player_value('winger_skill', $player, $player_previous_week, $player_role->position_id);
                        break;
                    case '14':
                        display_player_value('scorer_skill', $player, $player_previous_week, $player_role->position_id);
                        break;
                    case '15':
                        display_player_value('passing_skill', $player, $player_previous_week, $player_role->position_id);
                        break;
                    case '16':
                        display_player_value('defender_skill', $player, $player_previous_week, $player_role->position_id);
                        break;
                    case '17':
                        display_player_value('keeper_skill', $player, $player_previous_week, $player_role->position_id);
                        break;
                    case '18':
                        display_player_value('set_pieces_skill', $player, $player_previous_week);
                        break;
                    case '19':
                        display_player_value('loyalty', $player, $player_previous_week);
                        break;
                    case '20':
                        echo '<td>';
                        $squad_preferences_roles = new Squad_preferences_role();
                        $squad_preferences_roles->where('id', $player->role_id)->get();
                        if($squad_preferences_roles->position_id <= 6) {
                            echo number_format($player->score/350, 1);
                            display_player_individual_order($player->individual_order);
                        }
                        echo '</td>';
                        /*
                        <?php if (is_col_on('20', $mg_profile->team_id)): ?>
                        <?php $score = $this->squad_model->get_player_score($player);?>
                        <?php $description = $this->squad_model->get_individual_order($score['individual_order']);?>
                        <td><?php echo number_format($score['Score']/350, 1); ?> <?php display_player_individual_order($score['individual_order'], $description);?></td>
                        <?php endif;
                        */
                        break;
                }
                ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo form_hidden('form', '1'); ?>
    <input type="submit" name="submit" value="<?php echo lang('label_save_changes');?>" class="btn" />
</form>