<?php
$mysqli = database::connect(); $mysqli->set_charset("utf8");

$distribution = $mysqli->query("SELECT game.id, game.name, game.image, count(game.id) as counts FROM `distribution` as game, `distribution_keys` as dk WHERE game.id = dk.`game_id` and dk.status = 0 GROUP BY game.id");

while($result = mysqli_fetch_array($distribution)):

  print('<div class="item-loop">
    <div class="coast">ост. '.$result["counts"].'</div>
    <div class="name"><a href="/distribution/item/'.$result["id"].'">'.$result["name"].'</a></div>
    <div class="poster"><a href="/distribution/item/'.$result["id"].'"><img src="'.$result["image"].'"></a></div>
    </div>');

  endwhile; database::close($mysqli); ?>
