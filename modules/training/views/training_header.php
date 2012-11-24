<thead>
<tr>
    <th><?php echo lang('label_match_round');?></th>
    <?php
    for ($match_round = 0; $match_round <= 15; ++$match_round):?>
        <?php $ht_match_round = $match_round + 1;?>
        <th <?php echo get_bg_color($mg_profile, $season, $ht_match_round);?>>
            <?php echo $match_round + 1;?>
        </th>
        <?php endfor; ?>
</tr>

<tr>
    <th><?php echo lang('label_training');?></th>
    <?php
    for ($match_round = 0; $match_round <= 15; ++$match_round) {
        $ht_match_round = $match_round+1;
        $training_teams = new Training_team();
        $where = array(
            'team_id' => $mg_profile->team_id,
            'season' => $season
        );
        $training_trained = $training_teams->where($where)->get();
        $training_type = explode('-', $training_trained->training_type);
        $training_level = explode('-', $training_trained->training_level);
        $stamina_training_part = explode('-', $training_trained->stamina_training_part);
        ?>
        <th <?php echo get_bg_color($mg_profile, $season, $ht_match_round);?>>
            <?php if ($edit_view):?>
            <select name="<?php echo 'training_type-'.$match_round;?>" class="select-training">
                <?php foreach ($training_types as $value): ?>
                <option value="<?php echo $value->id ?>" <?php if ($value->id == $training_type[$match_round]) echo 'selected' ?>>
                    <p class="text_high"><?php echo lang('training_type_'.$value->id);?></p>
                </option>
                <?php endforeach;?>
            </select>
            <?php else:?>
            <?php foreach ($training_types as $value):?>
                <?php if ($value->id == $training_type[$match_round]):?>
                    <?php echo lang('training_type_'.$value->id);?>
                    <?php endif;?>
                <?php endforeach;?>
            <?php endif;?>
        </th>
        <?php }?>
</tr>

<tr>
    <th><?php echo lang('label_stamina');?></th>
    <?php
    for ($match_round = 0; $match_round <= 15; ++$match_round) {
        $ht_match_round = $match_round+1;
        ?>
        <th <?php echo get_bg_color($mg_profile, $season, $ht_match_round);?>>
                <?php if ($edit_view):?>
                <input type="number" min="0" max="100" step="1" name="<?php echo 'stamina_training_part-'.$match_round;?>" value="<?php echo $stamina_training_part[$match_round];?>" class="input-training" > %
                <?php else:?>
                <?php echo $stamina_training_part[$match_round];?>%
                <?php endif;?>
        </th>
        <?php } ?>
</tr>
</thead>