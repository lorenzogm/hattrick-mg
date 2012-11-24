<?php echo anchor('manager/economy/add_season', 'Add new season');?>

<table>
    <tr>
        <td><?php echo lang('table_season');?></td>
        <td colspan="2"><?php echo lang('table_constant_income');?></td>
        <td colspan="3"><?php echo lang('table_constant_costs');?></td>
        <td><?php echo lang('table_constant_balance');?></td>
        <td colspan="3"><?php echo lang('table_variable_income');?></td>
        <td colspan="5"><?php echo lang('table_variable_costs');?></td>
        <td><?php echo lang('table_variable_balance');?></td>
        <td><?php echo lang('table_balance');?></td>
    </tr>
    <tr>
        <?php foreach ($fields as $field):?>
        <td><?php echo lang('table_'.$field)?></td>
            <?php endforeach;?>
    </tr>

    <?php foreach($seasonal_economies as $seasonal_economy):?>
    <?php $constant_income = $seasonal_economy->income_spectators + $seasonal_economy->income_sponsors;?>
    <?php $constant_costs = $seasonal_economy->costs_arena + $seasonal_economy->costs_staff + $seasonal_economy->costs_youth;?>
    <?php $constant_balance = $constant_income-$constant_costs;?>
    <?php $variable_income = $seasonal_economy->income_sold_players + $seasonal_economy->income_sold_players_commission + $seasonal_economy->income_temporary;?>
    <?php $variable_costs = $seasonal_economy->costs_players + $seasonal_economy->costs_financial + $seasonal_economy->costs_bought_players + $seasonal_economy->costs_arenaBuilding + $seasonal_economy->costs_temporary;?>
    <?php $variable_balance = $variable_income-$variable_costs;?>
    <tr>
        <td><?php echo anchor('manager/economy/addSeason/'.$seasonal_economy->season, $seasonal_economy->season.'Edit');?></td>
        <td><?php echo $seasonal_economy->income_spectators;?></td>
        <td><?php echo $seasonal_economy->income_sponsors;?></td>
        <td><?php echo $seasonal_economy->costs_arena;?></td>
        <td><?php echo $seasonal_economy->costs_staff;?></td>
        <td><?php echo $seasonal_economy->costs_youth;?></td>
        <td><?php echo $constant_balance;?></td>
        <td><?php echo $seasonal_economy->income_sold_players;?></td>
        <td><?php echo $seasonal_economy->income_sold_players_commission;?></td>
        <td><?php echo $seasonal_economy->income_temporary;?></td>
        <td><?php echo $seasonal_economy->costs_players;?></td>
        <td><?php echo $seasonal_economy->costs_bought_players;?></td>
        <td><?php echo $seasonal_economy->costs_arena_building;?></td>
        <td><?php echo $seasonal_economy->costs_temporary;?></td>
        <td><?php echo $seasonal_economy->costs_financial;?></td>
        <td><?php echo $variable_balance;?></td>
        <td><?php echo $seasonal_economy->weeks_total;?></td>
    </tr>
    <?php endforeach;?>

</table>