<div class="well span7 offset2">
    <form method="post" accept-charset="utf-8" action="<?php echo site_url(uri_string().'/cols')?>">
        <h2><?php echo lang('preferences_edit_cols');?></h2>

        <table>
            <tr>
                <?php foreach ($table_cols as $col): ?>
                <th>
                    <?php  echo anchor('squad/order/'.$col->col_label, img(array('src' => site_url(ADDONPATH.'modules/squad/img/table_cols/'.CURRENT_LANGUAGE.'/'.$col->col_label.'.png')))); ?>
                </th>
                <?php endforeach; ?>

            </tr>
            <tr>
                <?php foreach ($preferences_table_cols as $preferences_table_col): ?>
                <td><?php echo form_checkbox($preferences_table_col->col_id, 'accept', $preferences_table_col->is_checked); ?></td>
                <?php endforeach; ?>
            </tr>
        </table>

        <input type="submit" name="submit" value="<?php echo lang('label_save_changes');?>" class="btn" />
    </form>
</div>

<div class="well span5 offset3">
    <h2><?php echo lang('preferences_edit_positions');?></h2>
    <form method="post" accept-charset="utf-8" action="<?php echo site_url(uri_string().'/roles')?>">

        <table class="table table-hover table-condensed">
            <?php foreach ($roles as $role): ?>
            <tr>
                <td>
                    <?php
                    if($role->is_default)
                        echo lang('position_'.$role->position_id);
                    else
                        if(isset($role->custom_role_label))
                            echo form_input($role->id, $role->custom_role_label);
                        else
                            echo form_input($role->id, lang('position_'.$role->position_id));
                    ?>
                </td>
                <td>
                    <?php
                    if(!isset($role->custom_bg_color))
                        $value = get_bg_color($role->position_id);
                    else
                        $value = $role->custom_bg_color;
                    ?>
                    <input type="color" name="color_<?php echo $role->id;?>" value="<?php echo $value?>">
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <input type="submit" name="submit" value="<?php echo lang('label_save_changes');?>" class="btn" />
    </form>
</div>