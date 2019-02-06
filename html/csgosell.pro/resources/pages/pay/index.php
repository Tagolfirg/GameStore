  <?php

  $error = false; // 0 ошибок
  $id = $args[3];

  // Получаем игру
  $mysqli = database::connect();  $mysqli->set_charset("utf8");
  $sql = $mysqli->query("SELECT * FROM `store` WHERE id = '$id'");
  $u = $sql->fetch_assoc();

  // Проверяем существует ли игра
  if(mysqli_num_rows($sql) == null) $error = 'Товар не найден!';
  else {
    $keys = $mysqli->query("SELECT * FROM `store_keys` WHERE store = '$id' and status = 0");
    $count = mysqli_num_rows($keys);
    if($count == 0) $error = 'Ключи закончились';
  }

  database::close($mysqli);
  ?>


  <form method="post" class="row">

    <? if($error): ?><div class="col-12">
      <div class="bg-white rounded shadow-sm p-4">
        <h5>Ошибка</h5>
        <span class="text-muted"><?=$error;?></span>
      </div>
    </div>
    <? else: ?>

     <input type="hidden" id="method" name="method" value="qiwi">

     <div class="col-md-3">

      <div class="bg-white rounded shadow-sm p-4 mb-4">

        <span class="text-muted">Покупка товара</span>
        <h5 class="mb-3"><?=$u["name"];?></h5>

        <span class="text-muted">Цена</span>
        <h4 class="text-danger mb-3" id="priceTab"><?=$u["price"];?> руб.</h4>

        <span class="text-muted">Количество</span>
        <input type="number" class="form-control mb-3" name="value" value="1" min="1" max="<?=$count;?>" onchange="onprice(value)">

        <span class="text-muted">Купон</span>
        <input type="text" class="form-control mt-2" name="coupon" placeholder="Купон (При наличии)">

      </div>


      <div class="bg-white rounded shadow-sm p-4 mb-4">

        <span class="text-muted">Магазин</span>
        <h5 class="mb-3"><?=main["name"];?></h5>

        <span class="text-muted">Рейтинг</span>
        <h4 class="text-success mb-3">1034</h4>

        <span class="text-muted">Есть жалоба на магазин?</span>
        <a href="https://vk.me/public173535297" target="_blank"><h4 class="mb-3">Web2Pay</h4></a>
      

      </div>

    </div>

    <div class="col">

      <div class="bg-white rounded shadow-sm p-4">

        <span class="text-muted">Выберите метод оплаты</span>

        <div class="row mt-3">

          <?php if(QIWI["payment"] == 1): ?>
            <div class="col-6 col-sm-6 col-lg-3 px-1 mb-2 btnpay" onclick="buy('qiwi')"> 
              <div class="d-flex align-items-center justify-content-center h-100 p-2 btn-qiwi active"> <img style="max-height: 60px; max-width: 90%" src="/assets/pay/qiwi.png"> </div> 
            </div>
          <?php endif; ?>

          <?php if(Yandex["payment"] == 1): ?>
            <div class="col-6 col-sm-6 col-lg-3 px-1 mb-2 btnpay" onclick="buy('PC')"> 
              <div class="d-flex align-items-center justify-content-center h-100 p-2 btn-PC"> <img style="max-height: 60px; max-width: 90%" src="/assets/pay/ya.png"> </div> 
            </div>
            <div class="col-6 col-sm-6 col-lg-3 px-1 mb-2 btnpay" onclick="buy('AC')"> 
              <div class="d-flex align-items-center justify-content-center h-100 p-2 btn-AC"> <img style="max-height: 60px; max-width: 90%" src="/assets/pay/visa.png"> </div> 
            </div>
          <?php endif; ?>

          <?php if(primepayer["payment"] == 1): ?>
            <div class="col-6 col-sm-6 col-lg-3 px-1 mb-2 btnpay" onclick="buy('primepayer')"> 
              <div class="d-flex align-items-center justify-content-center h-100 p-2 btn-primepayer"> <img style="max-height: 60px; max-width: 90%" src="/assets/pay/prime.png"> </div> 
            </div>
          <?php endif; ?>

          <?php if(FREEKASSA["payment"] == 1): ?>
            <div class="col-6 col-sm-6 col-lg-3 px-1 mb-2 btnpay" onclick="buy('freekassa')"> 
              <div class="d-flex align-items-center justify-content-center h-100 p-2 btn-freekassa"> <img style="max-height: 60px; max-width: 90%" src="/assets/pay/freekassa.png"> </div> 
            </div>
          <?php endif; ?>

          <div class="col-12 mt-4">
            <span class="text-muted">Email для отправки товара</span>
            <input type="email" class="form-control mt-2" name="email" placeholder="Почта" required>

            <button type="submit" class="btn btn-success btn-block mt-4">Перейти к оплате</button>
          </div>

        </div>


        </div> 

        <?php require(BASE_PATH.'/resources/pages/comments.php'); ?>

      </div>


      <style type="text/css">
      .btnpay > div {
        border-radius: 4px;
        min-height: 80px;
        background-color: #ecf0f1;
        cursor: pointer;
      }
      .btnpay > div > img, .chechout > div > img {
        -webkit-filter: grayscale(1) invert(0.5) brightness(200%);
        filter: grayscale(1) brightness(100%);
      }

      .active {
        border: 2px solid #28a745;
      }
      .active img {
        filter: grayscale(0)!important;
      }
    </style>


    <script>
      function onprice(value) {
        var price = <?=$u["price"];?> * value;
        $('#priceTab').html(price + ' руб.');
      }

      function buy(method) {

        $('.btn-qiwi').removeClass('active');
        $('.btn-PC').removeClass('active');
        $('.btn-AC').removeClass('active');
        $('.btn-primepayer').removeClass('active');
        $('.btn-freekassa').removeClass('active');

        $('.btn-' + method).addClass('active');
        $('#method').val(method);

      }
    </script>


  <? endif; ?>
</form>