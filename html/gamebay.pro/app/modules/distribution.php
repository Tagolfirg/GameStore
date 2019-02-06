<?php

class VK
 {
   // Выбор ключа ВК
   public static function generation() {
     $random = rand(0, (count(VK["tokens"]) - 1));
     return '&access_token='.VK["tokens"][$random].'&v=5.85';
   }
   // Проверка условий ВК
   public static function check($method, $url, $uid) {
     $vk_token = VK::generation();
     if($method == 'vk_like') {
         // Делаем разделение между Owner и item_id
         $matches = explode("_", $url);
         $owner_id = $matches[0];
         $item_id = $matches[1];
         $d = @file_get_contents('https://api.vk.com/method/likes.isLiked?user_id='.$uid.'&type=post&owner_id='.$owner_id.'&item_id='.$item_id.$vk_token);
         // Проверяем наличие лайка
         $d = json_decode($d, true);
         if(isset($d["error"])) return $error = $d["error"]["error_msg"];
         if($d["response"]["liked"] != 1) return $error = array('id' => $url, 'msg' => 'На записи вконтакте не найден лайк!');
     }
     else if($method == 'vk_sub') {
        $eid = $url;
         // Проверяем если слово public
         if(stristr($url, 'public')) {
           preg_match('/(\d+)/s', $url, $value);
           $url = $value[1];
         }
         $d = @file_get_contents('https://api.vk.com/method/groups.isMember?user_id='.$uid.'&group_id='.$url.$vk_token);
         $d = json_decode($d, true);
         if($d["response"] != 1) return $error = array('id' => $eid, 'msg' => 'Не подписан на группу!');
     }
     else if($method == 'vk_repost') {
          $eid = $url;
         $matches = explode("_", $url);
         $owner_id = $matches[0];
         $item_id = $matches[1];
         $d = @file_get_contents('https://api.vk.com/method/wall.get?owner_id='.$uid.'&count=20'.$vk_token);
         $d = json_decode($d, true);
         if(isset($d["error"])) return $error = array('id' => 'null', 'msg' => $d["error"]["error_msg"]);
         $success = false;
          foreach ($d["response"]["items"] as $key) {
            if(!isset($key["copy_history"])) continue;
            if($key["copy_history"][0]["id"] == $item_id and $key["copy_history"][0]["owner_id"] == $owner_id) {
              $success = true; break;
            }
          }
          if(!$success) return $error = array('id' => 'repost'.$eid, 'msg' => 'Не найден репост записи!');
     }
     else return $error = 'Метод не найден!';
   }
 }

 class distribution {
  public static function check($method, $url, $id) {
      //$memcached = new Memcached();
      //$memcached->addServer('localhost', 11211);
      $error = false; // Нет ошибок
      $uid = $_SESSION["id"];
      // if($memcached->get($uid.$method.$url)) return;
      if($method == 'vk_like') $error = VK::check($method, $url, $uid);
      else if($method == 'vk_sub') $error = VK::check($method, $url, $uid);
      //else if($method == 'yt_sub') $error = YT::checksub($url, $id);
      else if($method == 'vk_repost') $error = VK::check($method, $url, $uid);
      if($error) return $error;
      //else $memcached->set($uid.$method.$url, 'true', 180);
  }
}


get('/distribution', function(){
    tpl(['index', 'distribution/index', 'Раздачи игр']);
});
get('/distribution/item/([a-zA-Z0-9\-_]+)', function($id){
    tpl(['index', 'distribution/item', 'Раздача игры', $id]);
});
post('/distribution/check/([a-zA-Z0-9\-_]+)', function($id) {
  $error = false;
  $mysqli = database::connect();
  $sql = $mysqli->query("SELECT * FROM `distribution` WHERE id = '$id'");
  $u = $sql->fetch_assoc();
  if(mysqli_num_rows($sql) == null) return alert('error', 'Игра не найдена!');
  else {
    $keys = $mysqli->query("SELECT * FROM `distribution_keys` WHERE game_id = '$id' and status = 0");
    if(mysqli_num_rows($keys) == null) return alert('error', 'Товар закончился!');
    if(User::check()) {
      $uid = $_SESSION["id"];
      $check = $mysqli->query("SELECT * FROM `distribution_keys` WHERE game_id = '$id' and user_id = '$uid'");
      if(mysqli_num_rows($check) != null) return alert('error', 'Вы уже учавствовали в этой игре');
      $missions = json_decode($u["missions"], true);
      foreach ($missions as $key) {
        $error = distribution::check($key["mission"], $key["value"], $id);
        if($error) return alert('error', $error);
      }
      if(!$error) {
        $get_key = $mysqli->query("SELECT * FROM `distribution_keys` WHERE game_id = '$id' and status = '0' LIMIT 1");
        if(mysqli_num_rows($get_key) == null) return alert('error', 'Товар закончился!');
        $gk = $get_key->fetch_assoc(); $key_id = $gk["id"]; $key = $gk["s.key"];
        $get = $mysqli->query("UPDATE `distribution_keys` SET `status` = '1', `user_id` = '$uid' WHERE `id` = '$key_id';");
        alert('success', 'Ваш товар: '.$key);
      }
    } else return alert('error', 'Авторизуйтесь!');
  }
  database::close($mysqli);
});

// Администратору
if(User::check()) if(User::admin()) {
    get('/admin/distribution', function(){
      tpl(['admin', 'admin/distribution/index', 'Модуль / Раздача игр']);
    });
    // Добавление товара
    get('/admin/distribution/add', function(){
      tpl(['admin', 'admin/distribution/add', 'Добавить игру на раздачу']);
    });
    post('/admin/distribution/add', function(){
      // Название раздачи (игры)
      $title = htmlspecialchars($_POST["title"]);
      // Ссылка на картинку
      $image = htmlspecialchars($_POST["image"]);
      $mysqli = database::connect(); $mysqli->set_charset("utf8");
      // Условия
        $data = array();
        $mission = json_decode(json_encode($_POST["mission"]));
        $value = json_decode(json_encode($_POST["value"]));
        for($i=0; $i < count($_POST["mission"]); $i++) {
          if(!$mission[$i] || !$value[$i]) continue;
          $data[] = array('mission' => $mission[$i] , 'value' => $value[$i]);
        }
        $data = json_encode($data);
        $data = $mysqli->real_escape_string($data);
      // Конец поиска условий
      $mysqli->query("INSERT INTO `distribution`(`name`, `missions`, `image`) VALUES ('$title','$data','$image')");
      $sql = $mysqli->query("SELECT * FROM `distribution` ORDER BY id DESC LIMIT 1");
      $u = $sql->fetch_assoc();
      $id = $u["id"];
      // Список ключей
      $keys = $_POST["keys"];
      $keys = explode("\n", $keys);
      foreach ($keys as $key) {
        $key = htmlspecialchars($key);
        if(!$key) continue;
        $mysqli->query("INSERT INTO `distribution_keys`(`s.key`, `game_id`) VALUES ('$key', '$id')");
      }
      database::close($mysqli);
      header("Location: /admin/distribution");
    });
    // Удаление товара
    get('/admin/distribution/delete/([a-zA-Z0-9\-_]+)', function($id){
      $mysqli = database::connect(); $mysqli->set_charset("utf8");
      $mysqli->query("DELETE FROM `distribution` WHERE id = '$id'");
      $mysqli->query("DELETE FROM `distribution_keys` WHERE game_id = '$id'");
      database::close($mysqli);
      header("Location: /admin/distribution");
    });
    // Редактирование товара
    get('/admin/distribution/edit/([a-zA-Z0-9\-_]+)', function($id) {
      tpl(['admin', 'admin/distribution/edit', $id]);
    });
    post('/admin/distribution/edit', function(){
      $id = htmlspecialchars($_POST["id"]);
      $title = htmlspecialchars($_POST["title"]);
      $image = htmlspecialchars($_POST["image"]);
      $mysqli = database::connect();
      $mysqli->set_charset("utf8");
      // Условия
        $data = array();
        $mission = json_decode(json_encode($_POST["mission"]));
        $value = json_decode(json_encode($_POST["value"]));
        for($i=0; $i < count($_POST["mission"]); $i++) {
          if(!$mission[$i] || !$value[$i]) continue;
          $data[] = array('mission' => $mission[$i] , 'value' => $value[$i]);
        }
        $data = json_encode($data);
        $data = $mysqli->real_escape_string($data);
      // Конец поиска условий
      $mysqli->query("UPDATE `distribution` SET `name` = '$title', `missions` = '$data', `image` = '$image' WHERE `id` = '$id'");
      // Список ключей
      $keys = $_POST["keys"];
      $keys = explode("\n", $keys);
      foreach ($keys as $key) {
        $key = htmlspecialchars($key);
        if(!$key) continue;
        $mysqli->query("INSERT INTO `distribution_keys`(`s.key`, `game_id`) VALUES ('$key', '$id')");
      }
      database::close($mysqli);
      header("Location: /admin/distribution");
    });
}
