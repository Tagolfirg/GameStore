<?php

  define('HTTPS', 0);
  define('OFFLINE', 0);

  define('main', array(
    'name' => 'PRIMESELL.PRO',
    'description' => 'топ лучший магазин аккаунтов и ключей! здесь вы найдете ключи и аккаунты steam, origin, uplay, warface, fortnite WoT и многое другое!',
    'keywords' => 'купить аккаунт кс го, купить фортнайт, аккаунт кс го, купить стим аккаунт, купить кс го, аккаунт с ножом, аккаунт с инвентарем, cs go steam, кс го стим, fortnite, купить аккаунт wot',
    'favicon' => '/assets/favicon.png',
    'vkgroup' => 126222438,
    'metrikaId' => 51909536,
    'support' => 'https://vk.me/id524147149',
    'background' => 'http://fxgames.ru/igri/screenshots/ori_and_the_blind_forest/315.jpg',
    'header' => '/assets/cs5.png',
    'debug' => 1
  ));

  /* SQL
    Данные для подключения к базе данных
  */
  define('SQL', array(
    'host' => 'localhost',
    'user' => 'maxon',
    'password' => '9rUi6mTMgUFuyqH',
    'database' => 'primesell.pro'
  ));

  // Вконтакте
  define('VK', array(
    'id' => 6815651,
    'key' => 'fhyYlNJxAu6e3RBfixuO',
    'tokens' => array('cd2782955ce4808aab19469cda1070caeb51da8a42aa2adb66dede6b56824dabad029fc7210b51d1016ea', '5b54e2307e7fdcf63aa6e1aa7f202327342f992c3dae5192256f709b803496dd106d9ec7f743541dd1f9d', '4afc117c372cdfd64040ed3ea9dca4dad3e64b6627e7fd48c6466f7ed6ae55b9e76df8f0218526169f411', 'bea570349220d8211019a9231cfca9712240390e49e86f807c763e2f919628190ee3d0e866d0683c43a66', '2ed49adf0ee92387e7da40ff475a1abc41bfae5489be48ab84b9d84c5b6f3c49b1568a9bed98430dbf9f3')
  ));


  // Платежные

  define('QIWI', array(
    'payment' => 1,
    'number' => '79088333238',
    'token' => 'f1b29e4c4c0eb2ae28cd7cb6ca83eda6',
    'comment' => 'primesell'
  ));

  define('Yandex', array(
    'payment' => 1,
    'number' => '410016659537038',
    'token' => 'aczJGcCqcXGR7NMGqfGlOiPG'
  ));

  define('primepayer', array(
    'payment' => 1,
    'id' => 1778,
    'secret' => 'zmgx4syynbxwdnwew9vfplzjewo2ai2h3zekabqsvmwwxpky0x51voyvwnnmjxoy' 
  ));

  define('FREEKASSA', array( 
    'payment' => 0, 
    'id' => 00000, 
    'secret1' => '00000000', 
    'secret2' => '00000000' 
  )); 