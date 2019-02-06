<?php

post('/store/items_get/([a-zA-Z0-9\-_]+)', function($id) {
    //$memcached = new Memcached();
    //$memcached->addServer('localhost', 11211);

    if(!$id) $id = 'all';
    $data = array();
    //if($memcached->get('store-items-'.$id)) return $memcached->get('store.items');
    $mysqli = database::connect(); $mysqli->set_charset("utf8");
    if($id != 'all') $store = $mysqli->query("select store.id, store.name, store.image, store.coupon, count(gk.id) as counts, store.price, store.rank, category.name as catname from `store` as store, `store_categories` as category, `store_keys` as gk WHERE category.id = store.categories and store.categories = '$id' and gk.store = store.id and gk.status = 0 GROUP BY store.id ORDER BY store.rank");
    else $store = $mysqli->query("select store.id, store.name, store.image, store.coupon, count(gk.id) as counts, store.price, store.rank, category.name as catname from `store` as store, `store_categories` as category, `store_keys` as gk WHERE category.id = store.categories and gk.store = store.id and gk.status = 0 GROUP BY store.id ORDER BY store.rank");

    $count = mysqli_num_rows($store);
    if($count == 0) return alert('error', 'Не найдено товаров!');

    while($result = mysqli_fetch_array($store)) {

      $data[] = array(
        'id' => $result["id"],
        'name' => $result["name"],
        'image' => $result["image"],
        'price' => round($result["price"]),
        'coupon' => $result["coupon"]
      );

    } database::close($mysqli);

    $data = json_decode(json_encode($data));
    $data = alert('success', $data);
    //$memcached->set('store-items-'.$id, $data, 180);
    return $data;
});


if(User::check()) if(User::admin()) {

  get('/admin/store', function(){
    tpl(['admin', 'admin/store/index', 'Магазин']);
  });


  post('/fake/buy', function(){

    $id = $_POST["game"];

    $mysqli = database::connect(); $mysqli->set_charset("utf8");

      // Получаем игру
      $mysqli = database::connect();  $mysqli->set_charset("utf8");
      $sql = $mysqli->query("SELECT * FROM `store` WHERE id = '$id'");
      $u = $sql->fetch_assoc();

      $price = $u["price"];

      $code = false;
      $cMethod = 'fakeBuy';
      $value = 1;
      $email = 'maxon@petuh.com';


          // Создание платежа
      $date = date("Y-m-d H:i:s");
      $mysqli->query("INSERT INTO `payments` (`sum`, `store`, `date`, `coupon`, `method`, `value`, `email`) VALUES ('$price', '$id', '$date', '$code', '$cMethod', '$value', '$email')");

      $sql = $mysqli->query("SELECT * FROM `payments` WHERE `sum` = '$price' and `store` = '$id' and `date` = '$date' order by id desc limit 1");
      $u = $sql->fetch_assoc();

      $pay_id = $u["id"];
      $mysqli->query("UPDATE `payments` SET `status` = '1' WHERE `id` = '$pay_id'");

      database::close($mysqli);
      header("Location: /admin");
  });

  get('/admin/store/add', function() {
    tpl(['admin', 'admin/store/add', 'Магазин']);
  });

  get('/admin/store/delete/([a-zA-Z0-9\-_]+)', function($id){
      $mysqli = database::connect(); $mysqli->set_charset("utf8");
      $mysqli->query("DELETE FROM `store_keys` WHERE store = '$id'");
      $mysqli->query("DELETE FROM `store` WHERE id = '$id'");
      database::close($mysqli);
      header("Location: /admin/store");
    });


    get('/admin/store/edit/([a-zA-Z0-9\-_]+)', function($id){
        tpl(['admin', 'admin/store/edit', 'Магазин', $id]);
    });

    post('/admin/store/edit/([a-zA-Z0-9\-_]+)', function($id) {

      if(isset($_FILES['imageupload']))  {

        $path="/storage/";
        $name = date('YmdHis').rand(100,1000).'.jpg';//Name of the File
        $temp = $_FILES['imageupload']['tmp_name'];
        $image = $path.$name;

        if(move_uploaded_file($temp, BASE_PATH.$image)) $status = 1;
        else $image = $_POST["oldimage"];

      }

      if(!isset($_POST["title"])) return alert('error', 'title dont find'); $title = $_POST["title"];
      if(!isset($_POST["price"])) return alert('error', 'price dont find'); $price = $_POST["price"];
      if(!isset($_POST["procent"])) return alert('error', 'procent dont find'); $procent = $_POST["procent"];
      if(!isset($_POST["category"])) return alert('error', 'category dont find'); $category = $_POST["category"];
      $text = $_POST["text"];

      $updated = $_POST["updated"];

        $mysqli = database::connect(); $mysqli->set_charset("utf8");

        $mysqli->query("DELETE FROM `store_keys` WHERE store = '$id' and status = '0'");

        $keys = $_POST["keys"];
        $keys = explode("\n", $keys);
        foreach ($keys as $key) {
          $key = htmlspecialchars($key);
          if(!$key) continue;
          $mysqli->query("INSERT INTO `store_keys`(`key`, `store`) VALUES ('$key', '$id')");
        }

        $mysqli->query("UPDATE `store` SET `name` = '$title', `price` = '$price', `coupon` = '$procent', `image` = '$image', `categories` = '$category', `text` = '$text', `updated` = '$updated' WHERE `id` = '$id'");
        database::close($mysqli);

      header("Location: /admin/store");

    });

  post('/admin/store/add', function(){

        $path="/storage/";
        $name = date('YmdHis').rand(100,1000).'.jpg';//Name of the File
        $temp = $_FILES['imageupload']['tmp_name'];

        $image = $path.$name;

        if(move_uploaded_file($temp, BASE_PATH.$image)) {

            if(!isset($_POST["title"])) return alert('error', 'title dont find'); $title = $_POST["title"];
            if(!isset($_POST["price"])) return alert('error', 'price dont find'); $price = $_POST["price"];
            if(!isset($_POST["procent"])) return alert('error', 'procent dont find'); $procent = $_POST["procent"];
            if(!isset($_POST["category"])) return alert('error', 'category dont find'); $category = $_POST["category"];
            if(!isset($_POST["keys"])) return alert('error', 'keys dont find'); $keys = $_POST["keys"];
            if(!isset($_POST["text"])) return alert('error', 'text dont find'); $text = $_POST["text"];

            $updated = $_POST["updated"];

            $mysqli = database::connect(); $mysqli->set_charset("utf8");

            // Получение номера старого товара
            $sql = $mysqli->query("SELECT * FROM `store` ORDER BY id DESC LIMIT 1");
            $u = $sql->fetch_assoc();
            $id = $u["id"];

            if(!$id) $id = 1;

            // Создание товара
            $mysqli->query("INSERT INTO `store`(`name`, `price`, `coupon`, `image`, `categories`, `text`, `rank`, `updated`) VALUES ('$title','$price','$procent', '$image', '$category', '$text', '$id', '$updated')");

            if($id != 1) $id++;

            // Добавление ключей
            $keys = explode("\n", $keys);
            foreach ($keys as $key) {
              $key = htmlspecialchars($key);
              if(!$key) continue;
              $mysqli->query("INSERT INTO `store_keys`(`key`, `store`) VALUES ('$key', '$id')");
            }

            database::close($mysqli);

            header("Location: /admin/store");

        } else return alert('error', 'upload image error');



  });

  // Удаление товара
  get('/admin/store/delete/([a-zA-Z0-9\-_]+)', function($id){
      $mysqli = database::connect(); $mysqli->set_charset("utf8");
      $mysqli->query("DELETE FROM `store_keys` WHERE store = '$id'");
      $mysqli->query("DELETE FROM `store` WHERE id = '$id'");
      database::close($mysqli);
      header("Location: /admin/store");
    });


  // Перенос товаров
  get('/api/store/rank', function(){

    $mysqli = database::connect(); $mysqli->set_charset("utf8");

    $i = 0;

    foreach($_GET["item"] as $item) {
      $mysqli->query("UPDATE `store` SET `rank` = '$i' WHERE `id` = '$item'");
      $i++;
    }

    database::close($mysqli);

    return print("ok: ".$i);

  });

  // Добавить категорию
  post('/admin/store/category/add', function() {
      if(!isset($_POST["name"])) return alert('error', 'Имя не обнаружено'); $name = $_POST["name"];
      if($name == '' || !$name) return alert('error', 'Пустая категория');
      $mysqli = database::connect(); $mysqli->set_charset("utf8");
      $mysqli->query("INSERT INTO `store_categories`(`name`) VALUES ('$name')");
      database::close($mysqli);
      return alert('success', 'Вы успешно добавили категорию');
    });
  // Удаление категории
  get('/admin/store/category/delete/([a-zA-Z0-9\-_]+)', function($id){
      $mysqli = database::connect(); $mysqli->set_charset("utf8");
      $mysqli->query("DELETE FROM `store` WHERE categories = '$id'");
      $mysqli->query("DELETE FROM `store_categories` WHERE id = '$id'");
      database::close($mysqli);
      header("Location: /admin/store");
    });
  // Редактирование категории
  post('/admin/store/category/edit/([a-zA-Z0-9\-_]+)', function($id) {
    if(!isset($_POST["name"])) return alert('error', 'Имя не обнаружено');
    $name = htmlspecialchars($_POST["name"]);
    if($name == '' || !$name) return alert('error', 'Пустая категория');
    $mysqli = database::connect(); $mysqli->set_charset("utf8");
    $mysqli->query("UPDATE `store_categories` SET `name` = '$name' WHERE `id` = '$id'");
    database::close($mysqli);
    return alert('success', 'Категория успешно изменена');
  });



  // Купоны
  get('/admin/coupons', function(){
    tpl(['admin', 'admin/coupons/index', 'Купоны']);
  });


  // Добавить купон
  get('/admin/coupons/add', function(){
    tpl(['admin', 'admin/coupons/add', 'Добавить купон']);
  });
  post('/admin/coupons/add', function() {

    $code = $_POST["code"];
    $procent = $_POST["procent"];

      $mysqli = database::connect(); $mysqli->set_charset("utf8");
      $mysqli->query("INSERT INTO `store_coupons`(`code`, `procent`) VALUES ('$code', '$procent')");
      database::close($mysqli);
    header("Location: /admin/coupons");
    });

    get('/admin/coupons/edit/([a-zA-Z0-9\-_]+)', function($id){
      tpl(['admin', 'admin/coupons/edit', 'Редактировать купон', $id]);
    });
  // Редактирование купона
  post('/admin/coupons/edit/([a-zA-Z0-9\-_]+)', function($id) {

    $code = $_POST["code"];
    $procent = $_POST["procent"];

    $mysqli = database::connect(); $mysqli->set_charset("utf8");
    $mysqli->query("UPDATE `store_coupons` SET `code` = '$code', `procent` = '$procent' WHERE `id` = '$id'");
    database::close($mysqli);
    header("Location: /admin/coupons");
  });

  // Удаление купона
  get('/admin/coupons/delete/([a-zA-Z0-9\-_]+)', function($id){
    $mysqli = database::connect(); $mysqli->set_charset("utf8");
    $mysqli->query("DELETE FROM `store_coupons` WHERE id = '$id'");
    database::close($mysqli);
    header("Location: /admin/coupons");
  });


// Быстрое редактирование

post('/fast/store/name/([a-zA-Z0-9\-_]+)', function($id){

  $mysqli = database::connect(); $mysqli->set_charset("utf8");

  $name = $_POST["name"];
  $mysqli->query("UPDATE `store` SET `name` = '$name' WHERE `id` = '$id'");
  database::close($mysqli);

  return alert('success', 'Изменено!');

});

post('/fast/store/price/([a-zA-Z0-9\-_]+)', function($id){

  $mysqli = database::connect(); $mysqli->set_charset("utf8");

  $price = $_POST["price"];
  $mysqli->query("UPDATE `store` SET `price` = '$price' WHERE `id` = '$id'");
  database::close($mysqli);

  return alert('success', 'Изменено!');

});

post('/fast/store/coupon/([a-zA-Z0-9\-_]+)', function($id){

  $mysqli = database::connect(); $mysqli->set_charset("utf8");

  $coupon = $_POST["coupon"];
  $mysqli->query("UPDATE `store` SET `coupon` = '$coupon' WHERE `id` = '$id'");
  database::close($mysqli);

  return alert('success', 'Изменено!');

});


post('/fast/change/image', function(){

  $mysqli = database::connect(); $mysqli->set_charset("utf8");

  $id = $_POST["id"];
  $status = false;

  $path="/storage/";
  $name = date('YmdHis').rand(100,1000).'.jpg';//Name of the File
  $temp = $_FILES['imageupload']['tmp_name'];
  $image = $path.$name;
  if(move_uploaded_file($temp, BASE_PATH.$image)) $status = true;


  if($status) {
    $mysqli->query("UPDATE `store` SET `image` = '$image' WHERE `id` = '$id'");
  }

  database::close($mysqli);

  header("Location: /admin/store");

});




}
