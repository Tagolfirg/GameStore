  <?php

  $error = false; // 0 ошибок
  $id = $args[3];

  // Получаем игру
  $mysqli = database::connect();  $mysqli->set_charset("utf8");
  $sql = $mysqli->query("SELECT * FROM `payments` WHERE id = '$id' and method = 'qiwi' and status = '0'");
  $u = $sql->fetch_assoc();

  // Проверяем существует ли игра
  if(mysqli_num_rows($sql) == null) $error = 'Чек не найден!';

  database::close($mysqli);
  ?>


<div class="row">


    <? if($error): ?><div class="col-12">
      <div class="bg-white rounded shadow-sm p-4">
        <h5>Ошибка</h5>
        <span class="text-muted"><?=$error;?></span>
      </div>

      <a href="/" class="btn btn-success btn-block mt-4">Вернуться на сайт</a>
    </div>
    <? else: ?>

      <div class="col-md-3">

      <div class="bg-white rounded shadow-sm p-4">

        <span class="text-muted">Оплата чека</span>
        <h5 class="mb-3">#<?=$id;?></h5>

        <span class="text-muted">Способ оплаты</span>
        <h5 class="mb-3">QIWI Банк</h5>

        <span class="text-muted">Магазин</span>
        <h5 class="mb-3"><?=main["name"];?></h5>

      </div>

      </div>

      <div class="col">


      <div class="bg-white rounded shadow-sm p-4">

        <h3>Реквизиты для оплаты</h3>

        <span class="text-muted">Кошелек</span>
        <h5 class="mb-3">+<?=QIWI["number"];?></h5>

        <span class="text-muted">Комментарий</span>
        <h5 class="mb-3"><?=QIWI["comment"];?>#<?=$id;?></h5>

        <span class="text-muted">Сумма</span>
        <h5 class="mb-3"><?=$u["sum"];?> руб</h5>        

        <form onsubmit="pay_window(); return false;" data-parsley-validate>
                        <button type="submit" class="btn btn-info">Перейти к оплате</button>
                        <button onclick="checkPay()" type="button" class="btn btn-success" id="check_pay">Проверить платёж</button>
                    </form>

      </div>
  <p class="text-center mt-4 text-muted">Если вы не нажмете кнопку "Проверить платеж" после оплаты <br /> средства могуть быть не зачислены!</p>



      </div>


  <script>
    function pay_window() {
      var sum = Math.round(<?=$u["sum"];?>);
      var w = window.open(`https://qiwi.com/payment/form/99?blocked[0]=account&amountInteger=`+ sum +`&amountFraction=0&extra['comment']=<?=QIWI["comment"];?>%23<?=$id;?>&extra['account']=+<?=QIWI["number"];?>&blocked[2]=comment&blocked[1]=sum`, 'Оплата', 'height=800,width=1100');
      var waiter = setInterval(function(){
        if(w.closed){
          clearInterval(waiter);
          $('#check_pay').click();
        }
      }, 1000);
    }
    function checkPay() {
      $.ajax({
        url: '/api/qiwi/<?=$id;?>',
        dataType: 'json',
        type: 'POST',
        data:{'sum': Math.round(<?=$u["sum"];?>)},
        success: function(data) {
          if(data["status"] == 'success') window.location = '/pay/success';
          else alert(data["msg"]);
        },
        error: function (err) {
           alert('1123');
        }
      });
    }
  </script>


  <? endif; ?>