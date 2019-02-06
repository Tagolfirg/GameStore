<?php

  class User {

    // Проверка на авторизацию
    public static function check() {
        if(!isset($_SESSION["id"]) || !isset($_SESSION["name"]) || !isset($_SESSION["image"])) return false;
        else return true;
    }

    // Проверка на админа
    public static function admin() {

      if(!User::check()) return;
      $id = $_SESSION["id"];

      $mysqli = database::connect();
      $sql = $mysqli->query("SELECT * FROM `users` WHERE vk_id = '$id'");
      $u = $sql->fetch_assoc();
      $admin = $u["admin"];
      database::close($mysqli);
      return $admin;

    }

  }

  /* Авторизация и выход */
  get('/auth', function(){

    $params = array(
      'client_id' => VK["id"],
      'redirect_uri' => 'http://'.$_SERVER["HTTP_HOST"].'/auth',
      'response_type' => 'code'
    );

    $loginlink = "https://oauth.vk.com/authorize?".urldecode(http_build_query($params));

    if(!isset($_GET['code'])) return header("Location: ".$loginlink);

        $result = false; // Нет данных о пользователе
        $params = array('client_id' => VK["id"],'client_secret' => VK["key"],'code' => $_GET['code'],'redirect_uri' => $params['redirect_uri']);

        $token = @file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params)));
        $token = json_decode($token, true);
        if(!$token) return header("Location: ".$loginlink);

        // Если есть токен
        if (isset($token['access_token'])) {

            $params = array(
                'uids'         => $token['user_id'],
                'fields'       => 'uid,first_name,last_name,screen_name,sex,bdate,photo_200',
                'access_token' => $token['access_token'],
                'v' => 5.85
            );

          $userInfo = file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params)));

           $userInfo = json_decode($userInfo, true);

            if (isset($userInfo['response'][0]['id'])) {
                $userInfo = $userInfo['response'][0];
                $result = true;
            }
        }

        // Выводим результат
        if ($result) {


            $id = $userInfo['id'];
            $name = $userInfo['first_name'].' '.$userInfo['last_name'];
            $image = $userInfo['photo_200'];

            $_SESSION["id"] = $id;
            $_SESSION["name"] = $name;
            $_SESSION["image"] = $image;

            $mysqli = database::connect(); $mysqli->set_charset("utf8");

            $sql = $mysqli->query("SELECT * FROM `users` WHERE vk_id = '$id'");

            if(mysqli_num_rows($sql) == null) $mysqli->query("INSERT INTO `users`(`vk_id`, `name`, `image`) VALUES ('$id','$name','$image')");
            else $mysqli->query("UPDATE `users` SET `name`='$name',`image`='$image' WHERE vk_id = '$id'");

            database::close($mysqli);

        }

    header("Location: /distribution");

  });

  get('/logout', function(){
    session_destroy();
    header("Location: /");
  });
