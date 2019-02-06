<?php

require('modules/user.php');

get('/', function(){
    tpl(['index', 'index', 'Лучший магазин аккаунтов и ключей!']);
});

get('/search', function(){
    tpl(['index', 'search', 'Поиск']);
});

get('/rules', function(){
    tpl(['index', 'rules', 'Правила сайта']);
});

get('/garant', function(){
    tpl(['index', 'garant', 'Гарантии']);
});

get('/youtube', function(){
    tpl(['index', 'youtube', 'Видео проверки нашего сайта']);
});

get('/item/([a-zA-Z0-9\-_]+)', function($id){
    tpl(['index', 'item', 'Покупка товара', $id]);
});

require('modules/distribution.php');
require('modules/comments.php');
require('modules/store.php');
require('modules/orders.php');
require('modules/pay.php');

if(User::check()) {

  get('/profile', function(){
      tpl(['index', 'profile', 'Личный кабинет']);
  });

  // Администратору
  if(User::admin()) {

    get('/admin', function(){
      tpl(['admin', 'admin/index', 'Панель администратора']);
    });

  }

}
