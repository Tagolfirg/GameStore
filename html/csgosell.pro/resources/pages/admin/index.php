<?php $mysqli = database::connect();

  $market = $mysqli->query("SELECT * FROM `store`");
  $market = mysqli_num_rows($market);
  $distribution = $mysqli->query("SELECT * FROM `distribution`");
  $distribution = mysqli_num_rows($distribution);
  $coupons = $mysqli->query("SELECT * FROM `store_coupons`");
  $coupons = mysqli_num_rows($coupons);
  $payAll = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and method != 'fakeBuy'");
  $payAll = $payAll->fetch_assoc();
  $payAll = $payAll["sum"];
  $payToday = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and date >= CURDATE() and method != 'fakeBuy'");
  $payToday = $payToday->fetch_assoc();
  $payToday = $payToday["sum"];
  $payTodayQiwi = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and date >= CURDATE() and method = 'qiwi'");
  $payTodayQiwi = $payTodayQiwi->fetch_assoc();
  $payTodayQiwi = $payTodayQiwi["sum"];
  $payTodayYA = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and date >= CURDATE() and method = 'yandex'");
  $payTodayYA = $payTodayYA->fetch_assoc();
  $payTodayYA = $payTodayYA["sum"];
  $payTodayPP = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and date >= CURDATE() and method = 'primepayer'");
  $payTodayPP = $payTodayPP->fetch_assoc();
  $payTodayPP = $payTodayPP["sum"];
  $payTodayFK = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and date >= CURDATE() and method = 'freekassa'");
  $payTodayFK = $payTodayFK->fetch_assoc();
  $payTodayFK = $payTodayFK["sum"];
  $payAllQiwi = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and method = 'qiwi'");
  $payAllQiwi = $payAllQiwi->fetch_assoc();
  $payAllQiwi = $payAllQiwi["sum"];
  $payAllYA = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and method = 'yandex'");
  $payAllYA = $payAllYA->fetch_assoc();
  $payAllYA = $payAllYA["sum"];
  $payAllPP = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and method = 'primepayer'");
  $payAllPP = $payAllPP->fetch_assoc();
  $payAllPP = $payAllPP["sum"];
  $payAllFK = $mysqli->query("SELECT SUM(`sum`) as sum FROM `payments` WHERE status = 1 and method = 'freekassa'");
  $payAllFK = $payAllFK->fetch_assoc();
  $payAllFK = $payAllFK["sum"];

  database::close($mysqli);
?>


<div class="col-12 mb-3 mt-2"><h3 class="page-title">Статистика</h3></div>

<div class="col-md-2"><div class="card"><div class="card-body"><h5 class="card-title">Товаров</h5><h6 class="card-subtitle mb-2 text-muted">В магазине</h6><h3><?=$market;?></h3></div></div></div>
<div class="col-md-2"><div class="card"><div class="card-body"><h5 class="card-title">Товаров</h5><h6 class="card-subtitle mb-2 text-muted">В Раздачах</h6><h3><?=$distribution;?></h3></div></div></div>
<div class="col-md-3"><div class="card"><div class="card-body"><h5 class="card-title">Заработали</h5><h6 class="card-subtitle mb-2 text-muted">всего</h6><h3><?=$payAll;?> руб.</h3></div></div></div>
<div class="col-md-3"><div class="card"><div class="card-body"><h5 class="card-title">Заработали</h5><h6 class="card-subtitle mb-2 text-muted">сегодня</h6><h3><?=$payToday;?> руб.</h3></div></div></div>
<div class="col-md-2"><div class="card"><div class="card-body"><h5 class="card-title">Купонов</h5><h6 class="card-subtitle mb-2 text-muted">Активных</h6><h3><?=$coupons;?></h3></div></div></div>

<div class="col-md-6 mt-5">
  <h3 class="page-title mb-4">Заработок (сегодня)</h3>
  <div class="form-group mb-4"><label>Qiwi</label><input type="text" class="form-control" placeholder="<?=$payTodayQiwi;?> руб." disabled></div>
  <div class="form-group mb-4"><label>Яндекс.Деньги</label><input type="text" class="form-control" placeholder="<?=$payTodayYA;?> руб." disabled></div>
  <div class="form-group mb-4"><label>PrimePayer</label><input type="text" class="form-control" placeholder="<?=$payTodayPP;?> руб." disabled></div>
  <div class="form-group mb-4"><label>FreeKassa</label><input type="text" class="form-control" placeholder="<?=$payTodayFK;?> руб." disabled></div>
</div>
<div class="col-md-6 mt-5">
  <h3 class="page-title mb-4">Заработок (всего)</h3>
  <div class="form-group mb-4"><label>Qiwi</label><input type="text" class="form-control" placeholder="<?=$payAllQiwi;?> руб." disabled></div>
  <div class="form-group mb-4"><label>Яндекс.Деньги</label><input type="text" class="form-control" placeholder="<?=$payAllYA;?> руб." disabled></div>
  <div class="form-group mb-4"><label>PrimePayer</label><input type="text" class="form-control" placeholder="<?=$payAllPP;?> руб." disabled></div>
  <div class="form-group mb-4"><label>FreeKassa</label><input type="text" class="form-control" placeholder="<?=$payAllFK;?> руб." disabled></div>
</div>

<div class="col-md-12">
  <h3 class="page-title mb-4">Фейк-покупка</h3>
  <form action="/fake/buy" method="post">
    <div class="form-group">
      <label for="exampleFormControlSelect1">Выберите товар</label>
      <select class="form-control" id="exampleFormControlSelect1" name="game">
        <?php $mysqli = database::connect(); $mysqli->set_charset("utf8");
        $distribution = $mysqli->query("select store.id, store.name, count(gk.id) as counts, store.price, store.rank, category.name as catname from `store` as store, `store_categories` as category, `store_keys` as gk WHERE category.id = store.categories and gk.store = store.id and gk.status = 0 GROUP BY store.id ORDER BY store.rank");
        while($result = mysqli_fetch_array($distribution)): ?> 
          <option value="<?=$result["id"];?>"><?=$result["id"];?> | <?=$result["name"];?></option>
        <?php endwhile; database::close($mysqli); ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Купить</button>  
  </form>
</div>
