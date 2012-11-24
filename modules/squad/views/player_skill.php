<?php
$class_by_difference = $difference_flag;
$class_by_skill = NULL;

if($role_order == 1 AND ($key == 'defender_skill' OR $key == 'keeper_skill' OR $key == 'set_pieces_skill'))
    $class_by_skill = 'important_skill';
elseif($role_order == 2 AND ($key == 'defender_skill' OR $key == 'playmaker_skill'))
    $class_by_skill = 'important_skill';
elseif($role_order == 3 AND ($key == 'defender_skill' OR $key == 'playmaker_skill' OR $key == 'winger_skill'))
    $class_by_skill = 'important_skill';
elseif($role_order == 4 AND ($key == 'defender_skill' OR $key == 'playmaker_skill' OR $key == 'passing_skill'))
    $class_by_skill = 'important_skill';
elseif($role_order == 5 AND ($key == 'defender_skill' OR $key == 'playmaker_skill' OR $key == 'winger_skill' OR $key == 'passing_skill'))
    $class_by_skill = 'important_skill';
elseif($role_order == 6 AND ($key == 'winger_skill' OR $key == 'passing_skill' OR $key == 'scorer_skill'))
    $class_by_skill = 'important_skill';
else
    $class_by_skill = 'normal_skill';
?>

<td title="<?php echo number_format($difference_value, 0, ",", ".");?>">
    <span class="<?php echo $class_by_difference.' '.$class_by_skill;?>">
    <?php if ($key == 'player_form'):?>
        <img src="<?php echo site_url(ADDONPATH.'modules/squad/img/form/arrow'.$value.'.png')?>" class="player_form"/>
        <?php else:
        echo number_format($value, 0, ",", ".");
    endif;?>
        </span>
</td>