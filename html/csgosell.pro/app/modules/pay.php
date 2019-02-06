<?php

get('/buy/([a-zA-Z0-9\-_]+)', function($id){
  tpl(['pay','pay/index', 'Покупка товара #'.$id, $id]);
});
post('/buy/([a-zA-Z0-9\-_]+)', function($id) {

  if(!isset($_POST["method"])) return alert('error', 'method dont found'); $method = $_POST["method"];
  if(!isset($_POST["value"])) return alert('error', 'value dont found'); $value = $_POST["value"];
  if(!isset($_POST["email"])) return alert('error', 'email dont found'); $email = $_POST["email"];

  $error = false;

      // Получаем игру
  $mysqli = database::connect();  $mysqli->set_charset("utf8");
  $sql = $mysqli->query("SELECT * FROM `store` WHERE id = '$id'");
  $u = $sql->fetch_assoc();

  $price = $u["price"];

  $coupon = false; 

      // Регистрация купона
  if(isset($_POST["coupon"])) {

    $code = $_POST["coupon"];
    $coupon = $mysqli->query("SELECT * FROM `store_coupons` WHERE code = '$code'");

    if(mysqli_num_rows($sql) != null) {
      $coupon = $coupon->fetch_assoc();
      $coupon = $coupon["procent"] / 100;
      $coupon *= $price;
    }

  }

  if(($price - $coupon) > 0) $price -= $coupon;

  $price = round($price * $value);

  if($method == 'qiwi') $cMethod = 'qiwi';
  else if($method == 'AC' || $method == 'PC') $cMethod = 'Yandex';
  else if($method == 'primepayer') $cMethod = 'primepayer';

      // Создание платежа
  $date = date("Y-m-d H:i:s");
  $mysqli->query("INSERT INTO `payments` (`sum`, `store`, `date`, `coupon`, `method`, `value`, `email`) VALUES ('$price', '$id', '$date', '$code', '$cMethod', '$value', '$email')");

  $sql = $mysqli->query("SELECT * FROM `payments` WHERE `sum` = '$price' and `store` = '$id' and `date` = '$date' order by id desc limit 1");
  $u = $sql->fetch_assoc();

  if($method == 'qiwi') header("Location: /buy/qiwi/".$u["id"]);
  else if($method == 'AC' || $method == 'PC') {

    $params = array(
      'receiver' => Yandex["number"],
      'quickpay-form' => 'small',
      'targets' => 'Оплата заказа №'.$u["id"],
      'paymentType' => $method,
      'sum' => $price,
      'label' => '432432',
      'successURL' => 'http://'.$_SERVER["HTTP_HOST"].'/api/yandex/success/'.$u["id"]
    );

    header("Location: https://money.yandex.ru/quickpay/confirm.xml?".http_build_query($params));

  }
  else if($method == 'primepayer') {



    $data = [
      'shop' => primepayer["id"],
      'payment' => $u["id"],
      'amount' => $price,
      'description' => 'Оплата заказа #'.$u["id"],
      'currency' => 3
    ];

    ksort($data, SORT_STRING);
    $sign = hash('sha256', implode(':', $data).':'.primepayer["secret"]);

    $_SESSION["hash"] = $sign; 

    print('<form method="POST" action="https://primepayer.com/payment"><input name="shop" type="hidden"       value="'.$data['shop'].'"><input name="payment" type="hidden"     value="'.$data['payment'].'"><input name="amount" type="hidden"      value="'.$data['amount'].'"><input name="description" type="hidden" value="'.$data['description'].'"><input name="currency" type="hidden"    value="'.$data['currency'].'"><input name="sign"  type="hidden"       value="'.$sign.'"><button id="nextBTN">Продолжить оплату</button></form><script>document.getElementById("nextBTN").click();</script>');



  }


  database::close($mysqli);

});


post('/prime/success', function(){

  $id = $_POST["payment"];

  $data = [
    'shop' => $_POST["shop"],
    'payment' => $id,
    'amount' => round($_POST["amount"]),
    'description' => 'Оплата заказа #'.$id,
    'currency' => 3
  ];

  ksort($data, SORT_STRING);
  $sign = hash('sha256', implode(':', $data).':'.primepayer["secret"]);

  if($_SESSION["hash"] == $sign) {

    $mysqli = database::connect();  $mysqli->set_charset("utf8");

    $sql = $mysqli->query("SELECT * FROM `payments` WHERE id = '$id' and method = 'primepayer' and status = '0'");
    $u = $sql->fetch_assoc();

    $pay_id = $u["id"];
    $pay_store = $u["store"];
    $pay_coupon = $u["coupon"];
    $pay_email = $u["email"];
    $pay_date = $u["date"];

    $mysqli->query("UPDATE `payments` SET `status` = '1' WHERE `id` = '$pay_id'");

    $content = '';
    $distribution = $mysqli->query("select gk.id, store.name, gk.key from `store` as store, `store_keys` as gk, `payments` as pay where pay.store = store.id and pay.id = '$pay_id' and gk.store = store.id and gk.store = '$pay_store' and gk.status = 0 limit ".$u["value"]);
    while($result = mysqli_fetch_array($distribution)):
      $key_id = $result["id"];
      $mysqli->query("UPDATE `store_keys` SET `status` = '1', `coupon` = '$pay_coupon', `email` = '$pay_email', `date` = '$pay_date' WHERE `id` = '$key_id'");
      $content .= $result['name'].' - '.$result['key'].'<br />';
    endwhile;
    phpmailer($pay_email, 'Покупка на сайте ', $content);

    $_SESSION["content"] = $content;
    return header("Location: /pay/success");

  } else return alert('error', 'hash error');


});


post('/prime/notify', function(){
  return print("ok");
});


get('/api/yandex/success/([a-zA-Z0-9\-_]+)', function($id) {

  $mysqli = database::connect();  $mysqli->set_charset("utf8");
  $sql = $mysqli->query("SELECT * FROM `payments` WHERE id = '$id' and method = 'Yandex' and status = '0'");
  $u = $sql->fetch_assoc();
    // ТУТ ДОЛЖНА БЫТЬ СРАНАЯ ПРОВЕРКА

  $pay_id = $u["id"];
  $pay_store = $u["store"];
  $pay_coupon = $u["coupon"];
  $pay_email = $u["email"];
  $pay_date = $u["date"];

  $mysqli->query("UPDATE `payments` SET `status` = '1' WHERE `id` = '$pay_id'");

    // Выдача
  $content = '';
  $distribution = $mysqli->query("select gk.id, store.name, gk.key from `store` as store, `store_keys` as gk, `payments` as pay where pay.store = store.id and pay.id = '$pay_id' and gk.store = store.id and gk.store = '$pay_store' and gk.status = 0 limit ".$u["value"]);
  while($result = mysqli_fetch_array($distribution)):
    $key_id = $result["id"];
    $mysqli->query("UPDATE `store_keys` SET `status` = '1', `coupon` = '$pay_coupon', `email` = '$pay_email', `date` = '$pay_date' WHERE `id` = '$key_id'");
    $content .= $result['name'].' - '.$result['key'].'<br />';
  endwhile;
  phpmailer($pay_email, 'Покупка на сайте ', $content);

  updateItems($pay_store);

    // Оки
  $_SESSION["content"] = $content;
  return header("Location: /pay/success");


});

get('/buy/qiwi/([a-zA-Z0-9\-_]+)', function($id){
  tpl(['pay','pay/qiwi', 'Покупка товара #'.$id, $id]);
});
post('/api/qiwi/([a-zA-Z0-9\-_]+)', function($id){

  if(!isset($_POST["sum"])) return alert('error', 'Сумма не обнаружена');
  if($_POST["sum"] <= 0) return alert('error', 'Сумма не может быть меньше нуля');
  $sum = round(htmlspecialchars($_POST["sum"]));

  $mysqli = database::connect();  $mysqli->set_charset("utf8");
  $sql = $mysqli->query("SELECT * FROM `payments` WHERE id = '$id' and method = 'qiwi' and status = '0'");
  $u = $sql->fetch_assoc();

  if(mysqli_num_rows($sql) == null) return alert('error', 'Товар не обнаружен');

  if(QIWI::pay(round($sum), $u["id"])) { 

    $pay_id = $u["id"];
    $pay_store = $u["store"];
    $pay_coupon = $u["coupon"];
    $pay_email = $u["email"];
    $pay_date = $u["date"];

    $mysqli->query("UPDATE `payments` SET `status` = '1' WHERE `id` = '$pay_id'");

      // Выдача
    $content = '';
    $distribution = $mysqli->query("select gk.id, store.name, gk.key from `store` as store, `store_keys` as gk, `payments` as pay where pay.store = store.id and pay.id = '$pay_id' and gk.store = store.id and gk.store = '$pay_store' and gk.status = 0 limit ".$u["value"]);
    while($result = mysqli_fetch_array($distribution)):
      $key_id = $result["id"];
      $mysqli->query("UPDATE `store_keys` SET `status` = '1', `coupon` = '$pay_coupon', `email` = '$pay_email', `date` = '$pay_date' WHERE `id` = '$key_id'");
      $content .= $result['name'].' - '.$result['key'].'<br />';
    endwhile;
    phpmailer($pay_email, 'Покупка на сайте ', $content);


    updateItems($pay_store);

      // Оки
    $_SESSION["content"] = $content;
    return alert('success', '/pay/success');


  } else return alert('error', 'Оплата не прошла');



});



get('/pay/success', function(){
  tpl(['pay','pay/success', 'Успешная покупка']);
});

class QIWI {

  public static function pay($sum, $id) {
    $url = 'https://edge.qiwi.com/payment-history/v2/persons/'.QIWI["number"].'/payments?operation=IN&rows=10';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json','Authorization: Bearer '.QIWI["token"]]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, true);
    $id = QIWI["comment"].'#'.$id;

    foreach ($json["data"] as $key) if($key["sum"]["amount"] == $sum and $key["status"] == 'SUCCESS' and $key["sum"]["currency"] == 643 and $key["comment"] == $id) {
      return true;
    }

    return false;
  }

}


function updateItems($game) {

  $mysqli = database::connect(); $mysqli->set_charset("utf8");
  
  $check = $mysqli->query("SELECT *  FROM `store` WHERE id = '$game'");
  $ga = $check->fetch_assoc();

  if($ga["updated"] == 1) {

    $sql = $mysqli->query("SELECT COUNT(*) as count FROM `store_keys` WHERE store = '$game' and status = '0'");
    $u = $sql->fetch_assoc();
    if($u["count"] == 0) $mysqli->query("UPDATE `store_keys` SET status = '0', email = NULL, date = NULL, coupon = NULL WHERE store = '$game'");

  }
  
  database::close($mysqli);

}