<form method="post" accept-charset="utf-8" action="<?php echo site_url(uri_string().'/roles')?>">
    <div class="span7 offset2 well">

        <h2><?php echo lang('label_edit_roles');?></h2>

        <?php foreach ($squad_preferences_roles as $role): ?>
        <div>
            <?php
            echo form_checkbox($role->id, 'accept', $role->show_on_training);
            if(isset($role->custom_role_label))
                echo $role->custom_role_label;
            else
                echo lang('position_'.$role->position_id); ?>
        </div>
        <?php endforeach; ?>


        <div>
            <?php echo lang('preferences_bubble');?>
            <img src="<?php echo site_url(ADDONPATH.'mcgannigan/mcgannigan_bla.png');?>"/>
        </div>

        <input type="submit" name="submit" value="<?php echo lang('label_save_changes');?>" class="save_button" />
    </div>
</form>