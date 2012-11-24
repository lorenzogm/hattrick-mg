<?php echo form_open(uri_string());?>
<input type="hidden" name="form" value="add_season">
<table>
    <?php foreach($fields as $field):?>
    <tr>
        <td><?php echo $field?></td>
        <td><input name="<?php echo $field;?>" type="number" value="<?php echo $values[$field];?>" required <?php if($field == 'season') echo 'autofocus';?>></td>
    </tr>
    <?php endforeach;?>
</table>
<button type="submit">Enviar</button>

<?php echo form_close();?>