<?php

  Nanite::get('/comments', function(){
      tpl(['pay', 'comments', 'Комментарии']);
  });

  Nanite::post('/comments/auth', function() {
    if(!isset($_REQUEST['token'])) $_SESSION["error"] = 'Токен не найден!';
    else {
      $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_REQUEST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
      $user = json_decode($s, true);
      $_SESSION["ulogin"] = $user['identity'];
      $_SESSION["uname"] = htmlspecialchars($user['first_name'].' '.$user['last_name']);
      $_SESSION["uimage"] = $user['photo'];
    }
    if(isset($_GET["match"])) header("Location: ".$_GET["match"]);
    else header("Location: /");
  });

  Nanite::get('/api/comments', function(){
    if(!isset($_SESSION["ulogin"])) return alert('error', 'ulogin');
    else if($_REQUEST["method"] == 'send') {
        if(!isset($_REQUEST["text"])) return alert('error', 'text');
        $profile = $_SESSION["ulogin"];
        $name = $_SESSION["uname"];
        $image = $_SESSION["uimage"];
        $text = htmlspecialchars($_REQUEST["text"]);
        $date = date("H:i:s");
        $mysqli = database::connect(); $mysqli->set_charset("utf8");
        $mysqli->query("INSERT INTO `comments` (`name`, `avatar`, `review`, `date`, `profile`) VALUES ('$name', '$image', '$text', '$date', '$profile')");
        database::close($mysqli);
    }
  });

  if(User::check()) if(User::admin()) {
    Nanite::get('/admin/comments', function(){
        tpl(['admin', 'admin/comments', 'Комментарии']);
    });
    Nanite::get('/api/admin/comments', function(){

      if(!isset($_GET["method"])) return alert('error', 'dont find method || id || text');

      else if($_GET["method"] == 'success') {
        $id = $_GET["id"];
        $mysqli = database::connect(); $mysqli->set_charset("utf8");
        $mysqli->query("UPDATE `comments` SET `status` = '1' WHERE id = '$id'");
        database::close($mysqli);
        die("ok");
      }

      else if($_GET["method"] == 'update') {
        $id = $_GET["id"];
        $text = $_GET["text"];
        $mysqli = database::connect(); $mysqli->set_charset("utf8");
        $mysqli->query("UPDATE `comments` SET `status` = '1', `review` = '$text' WHERE id = '$id'");
        database::close($mysqli);
        die("ok");
      }

      else if($_GET["method"] == 'delete') {
        $id = $_GET["id"];
        $mysqli = database::connect(); $mysqli->set_charset("utf8");
        $mysqli->query("DELETE FROM `comments` WHERE `id` = '$id'");
        database::close($mysqli);
        die("ok");
      }

      else if($_GET["method"] == 'fake') {
        $name = $_GET["name"]; $image = $_GET["photo"]; $text = $_GET["text"];
        $date = date("H:i:s");
        $mysqli = database::connect(); $mysqli->set_charset("utf8");
        $mysqli->query("INSERT INTO `comments` (`name`, `avatar`, `review`, `date`, `profile`, `status`) VALUES ('$name', '$image', '$text', '$date', 'fake', '1')");
        database::close($mysqli);
        die("ok");
      }

    });
  }