<?php

if(isset($_GET["field"])):
  $field = htmlspecialchars($_GET["field"]);
      $mysqli = database::connect();
      $mysqli->set_charset("utf8");
      $distribution = $mysqli->query("select store.id, store.name, store.coupon, store.image, count(gk.id) as counts, store.price, store.rank, category.name as catname from `store` as store, `store_categories` as category, `store_keys` as gk WHERE category.id = store.categories and gk.store = store.id and gk.status = 0 and store.name like '%$field%' GROUP BY store.id ORDER BY store.rank");
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
<?php endwhile; database::close($mysqli); endif; ?>
