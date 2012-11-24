<?php include ('economy_menu.php'); ?>

<table>
    <tr>
        <td>Season</td>
        <?php for($i=1;$i<=16;++$i):?>
        <td><?php echo $i;?></td>
        <?php endfor;?>
    </tr>

    <?php foreach($players as $player):?>
    <?php $player_info = $this->economy_model->get_player($team_id, $player->player_id);?>
    <tr>
        <td><?php echo $player_info->first_name;?> <?php echo $player_info->last_name;?></td>
        <?php for($match_round=0;$match_round<=15;++$match_round):?>
        <?php $price = $this->economy_model->get_price($team_id, $player->player_id, $season, $match_round);?>
        <td><?php echo $price;?></td>
        <?php endfor;?>
    </tr>
    <?php endforeach;?>

</table>