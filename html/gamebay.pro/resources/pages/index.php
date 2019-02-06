
<?php
$mysqli = database::connect(); $mysqli->set_charset("utf8");

$distribution = $mysqli->query("SELECT game.id, game.name, game.image, count(game.id) as counts FROM `distribution` as game, `distribution_keys` as dk WHERE game.id = dk.`game_id` and dk.status = 0 GROUP BY game.id");

while($result = mysqli_fetch_array($distribution)):

  print('<div class="item-loop">
    <div class="coast" style="right: 99px;left: 7px;background: rgba(0, 216, 76, 0.7);text-align: center;">БЕСПЛАТНО!</div>
    <div class="coast">ост. '.$result["counts"].'</div>
    <div class="name"><a href="/distribution/item/'.$result["id"].'">'.$result["name"].'</a></div>
    <div class="poster"><a href="/distribution/item/'.$result["id"].'"><img src="'.$result["image"].'"></a></div>
    </div>');

  endwhile; database::close($mysqli); ?>

<?php
      $mysqli = database::connect();
      $mysqli->set_charset("utf8");
      $distribution = $mysqli->query("select store.id, store.name, store.image, store.coupon, count(gk.id) as counts, store.price, store.rank, category.name as catname from `store` as store, `store_categories` as category, `store_keys` as gk WHERE category.id = store.categories and gk.store = store.id and gk.status = 0 GROUP BY store.id ORDER BY store.rank");
      while($result = mysqli_fetch_array($distribution)):
?>
<div class="item-loop">
  <? if($result["coupon"] != 0): ?>
  <div class="coast" style="right: 140px;left: 7px;background: rgba(0, 216, 76, 0.7);text-align: center;"><?=$result["coupon"];?>%</div>
  <? endif; ?>
  <div class="coast"><?=round($result["price"]);?> руб.</div>
  <div class="name"><a href="/item/<?=$result["id"];?>"><?=$result["name"];?></a></div>
  <div class="poster"><a href="/item/<?=$result["id"];?>"><img src="<?=$result["image"];?>"></a></div>
</div>
<?php endwhile; database::close($mysqli); ?>
