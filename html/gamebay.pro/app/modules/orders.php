<?php

get('/orders', function(){
  tpl(['index', 'orders', 'Мои покупки']);
});

post('/orders', function(){

  if(isset($_POST["email"])) {

    $to = $_POST["email"];
    if (filter_var($to, FILTER_VALIDATE_EMAIL)) {

      $content = '<h3>Ваши последние покупки</h3>';
      $mysqli = database::connect();  $mysqli->set_charset("utf8");
      $distribution = $mysqli->query("select store.name, gk.key FROM `store` as store, `store_keys` as gk WHERE gk.store = store.id and gk.status = 1 and gk.email = '$to' Limit 30");
      while($result = mysqli_fetch_array($distribution)):
        $content .= $result['name'].' - '.$result['key'].'<br />';
      endwhile;
      database::close($mysqli);
      if(mysqli_num_rows($distribution) != null) {
        phpmailer($to, 'Ваши последние покупки', $content);
        $_SESSION["success"] = 'Отправлено';
      } else $_SESSION["error"] = 'Покупок не найдено';

    }

  }

  header("Location: /orders");

});
