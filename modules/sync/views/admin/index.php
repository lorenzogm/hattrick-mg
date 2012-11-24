<div id="box_user_panel">
    <form method="post" accept-charset="utf-8" action="<?php echo site_url(uri_string());?>">
        <input type="hidden" name="form" value="report">
        <div class="em"><p class="title_high">Añadir sincronizaciones</p></div>
        <div class="em">
            <label for="type">Tipo</label>
            <select id="type" name="type">
                <option value="all">Sincronizar todos</option>
                <option value="non_synced">Sincronizar lista de espera</option>
                <option value="user">Sincronizar usuario</option>
            </select>
            <label for="user_id">Usuario</label>
            <select id="user_id" name="user_id">
                <?php foreach ($profile as $row):?>
                <option value="<?php echo $row->user_id;?>"><?php echo $row->username . ' - ' . $row->team_name;?></option>
                <?php endforeach;?>
            </select>
            <input type="submit" name="submit" value="Sincronizar" class="button" />
    </form>
</div>
