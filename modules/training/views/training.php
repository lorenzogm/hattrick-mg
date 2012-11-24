<div>
    <button type="button" class="btn" onclick="location.href='<?php echo site_url('training');?>'"><?php echo lang('label_training');?></button>
    <button type="button" class="btn" onclick="location.href='<?php echo site_url('training/'.$season.'/edit_view');?>'"><?php echo lang('label_edit_view');?></button>
    <button type="button" class="btn" onclick="location.href='<?php echo site_url('training/preferences');?>'"><?php echo lang('label_preferences');?></button>

    <button type="button" class="btn" onclick="location.href='training/add_season'">
        <img src="<?php echo site_url(ADDONPATH.'modules/training/img/add_season.png');?>">
        <?php echo lang('label_add_season');?>
    </button>

    <ul id="seasons">
        <li><?php echo lang('label_season');?></li>
        <?php foreach ($seasons as $value): ?>
        <?php
        $selected_season = NULL;
        if($season == $value->season)
            $selected_season = 'class="seasons-selected"';
        ?>
        <li <?php echo $selected_season;?>>
            <a href="<?php echo site_url('training/'.$value->season.'/'.$edit_view);?>"><?php echo $value->season;?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php if ($edit_view):?>
<form method="post" accept-charset="utf-8" action="<?php echo site_url(uri_string())?>">
    <input type="submit" name="submit" value="<?php echo lang('label_save_changes');?>" class="btn" />
    <?php endif;?>
    <table id="table-training" class="table table-condensed">
        <?php
        $data['mg_profile'] = $mg_profile;
        $data['seasons'] = $seasons;
        $data['season'] = $season;
        $data['edit_view'] = $edit_view;
        $data['training_types'] = $training_types;
        $this->load->view('training/training_header', $data);

        foreach ($players_to_show as $player) {
            $data['player'] = $players->where('player_id', $player)->get();
            $data['preferences_roles'] = $preferences_roles->where('id', $players->role_id)->get();
            $data['predictions'] = $predictions;

            if ($preferences_roles->show_on_training)
                $this->load->view('training/training_player', $data);
        }
        ?>
    </table>
<?php if ($edit_view):?>
    <input type="submit" name="submit" value="<?php echo lang('label_save_changes');?>" class="btn" />
    </form>
    <?php endif;?>

<div class="well">
    <h3><?php echo lang('training_description_title');?></h3>

    <?php $predictor_img = '<img class="img_predictor" src="'.site_url(ADDONPATH.'modules/training/img/training_predictor.png').'" />';?>

    <ol>
        <li><h5><?php echo lang('training_description_1');?></h5></li>
        <li><h5><?php echo lang('training_description_2');?></h5></li>
        <li><h5><?php echo lang('training_description_3');?></h5></li>
        <li><h5><?php echo lang('training_description_4');?></h5></li>
        <li><h5><?php echo lang('training_description_5');?></h5></li>
    </ol>
    <img src="<?php echo site_url(ADDONPATH.'modules/training/img/mcgannigan/mcgannigan_bla.png');?>"  class="preferences_mcgannigan" >
</div>