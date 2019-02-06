
<?php
        $mysqli = database::connect();
        $mysqli->set_charset("utf8");
        $store = $mysqli->query("select store.id, store.name, pay.sum, pay.coupon, store.image from `store` as store, `payments` as pay WHERE pay.status = 1 and store.id = pay.store ORDER BY pay.id DESC LIMIT 7");
        while($result = mysqli_fetch_array($store)): ?>

        <div class="item-poster">
          <?php if($result["coupon"] != null): ?>
          <div class="coast" style="top: 20px;background: rgba(0, 216, 76, 0.7);text-align: center;">Купон: <?=$result["coupon"];?></div>
          <?php endif; ?>
          <div class="coast">Цена: <?=$result["sum"];?> ₽</div>
          <div class="name"><a href="/item/<?=$result["id"];?>"><?=$result["name"];?></a></div>
          <div class="poster-shadw"></div>
          <img src="<?=$result["image"];?>" />
        </div>

        <?php endwhile; database::close($mysqli); ?>
