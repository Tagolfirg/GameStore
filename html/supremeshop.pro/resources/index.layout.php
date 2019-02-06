
<html class="">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
  <meta charset="utf-8">
  <meta name="description" content="<?=main["description"];?>">
  <meta name="keywords" content="<?=main["keywords"];?>">
  <title><?=main["name"];?> - <?=$args[2];?></title>


  <link rel="shortcut icon" href="<?=main["favicon"];?>" type="image/png">

  <link href="/assets/css/jquery.toastmessage.css" rel="stylesheet" media="screen">
  <script src="//code.jquery.com/jquery.js"></script>
  <script type="text/javascript" src="//code.jquery.com/ui/1.10.0/jquery-ui.js"></script>
  <script src="/assets/Lollipop/js/bootstrap.min.js"></script>
  <script src="/assets/Lollipop/js/respond.js"></script>
  <script src="/assets/js/jquery.toastmessage.js"></script>
  <link href="/assets/admin/css/font-awesome.css" rel="stylesheet">
  <link href="/assets/Lollipop/css/style.css" rel="stylesheet" media="screen">
  <link href="/assets/Lollipop/css/bootstrap-glyphicons.css" rel="stylesheet" media="screen">
  <link href="/assets/Lollipop/css/bootstrapcssleque.css" rel="stylesheet" media="screen">
  <link href="/assets/Lollipop/css/limev2.css" rel="stylesheet" media="screen">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7"></script>

  <style>
  html, body {background-image: url(<?=main["background"];?>);background-attachment: fixed;background-size: cover;background-repeat: no-repeat;}
  body {font-family: Arial;font-size: 13px;color: black;text-shadow:;}
  .modal-content {background-color: #111736;}
  .pop_file img {width: 143px;height: 143px;max-width: 100%;padding-left: 29px;}
  .pop_file img {width: 143px;height: 143px;max-width: 100%;padding-left: 29px;}
  .modal-content {color: rgb(0, 0, 0);}
  .tab-box {width: 796px;}
  .block-top, #side-right .block-top {background: url() no-repeat, url(/assets/Lollipop/img/block-top-blue.png);}
  input[type="button"], input[type="submit"], input[type="reset"], button {background: ;}
  .full-nav li:hover, .full-nav li.cur {background: ;}
  .full-box .buy-item .coast {background: ;}
  #h-search #submit3:hover {background-position: right -26px;}
  #h-search #submit3 {background: url(/assets/Lollipop/img/h-search.png) no-repeat right 0;float: left;border: none;height: 26px;width: 70px;margin: 0;-webkit-transition: none;-moz-transition: none;-ms-transition: none;-o-transition: none;transition: none;}
</style>

</head>
<body data-twttr-rendered="true" style="overflow: auto;">
  <div id="main">
    <div id="header">
      <ul id="h-nav" style="background: black"><?php require(BASE_PATH.'/resources/includes/navigation.php'); ?></ul>
      <div id="h-poster"><div id="h-shadw"></div><a style="background: url() no-repeat;width: 250px;" href="/" id="h-logo"></a><img src="<?=main["header"];?>" /></div>
      <div id="h-searchbar"><div id="h-search"><script>function test(a){if(a=="13"){$("#test").click()}}</script><input type="text" placeholder="Введите фразу и нажмите Enter..." onkeyup="test(event.keyCode)" id="field"><input type="submit" value="" id="submit3" onclick="location.href = '/search?field='+$('#field').val();" ></div>
      <div id="h-alphabet"></div>
      <div class="rcol"><div id="bags"><a href="/orders" rel="nofollow"style="text-decoration: none;">Мои покупки</a></div></div>
    </div>
    <div id="cat-bar"><ul class="cat-list"><?php require(BASE_PATH.'/resources/includes/category.php'); ?></ul></div></div>
    <section id="middle" style="min-height: 80%;background: rgba(0, 0, 0, 0.62);"><aside id="side-left" style="position: relative; left: -4px; top: -14px;"><?php require(BASE_PATH.'/resources/includes/leftbox.php'); ?></aside>
    <div class="layer"><div id="content"><aside id="side-right" style="position: relative; left: -3px; top: -3px;"><?php require(BASE_PATH.'/resources/includes/rightbox.php'); ?></aside><div id="content-c"><? require($empty); ?></div></div></div></section></div>





      <script type="text/javascript" >
         (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
         m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
         (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

         ym(<?=main["metrikaId"];?>, "init", {
              id:<?=main["metrikaId"];?>,
              clickmap:true,
              trackLinks:true,
              accurateTrackBounce:true,
              webvisor:true
         });

         function items_get(id) {
         $("#content-c").html('');
         $.ajax({
           url: '/store/items_get/' + id,
           dataType: 'json',
           type: 'POST',
           success: function(data){
               if(data["status"] == 'error') return;
             else {
               var items = data["msg"];
               if(items.length == 0) return;
               items.forEach(function(element) {
                 if(element.coupon != 0) {
                   $("#content-c").append(`<div class="item-loop">
                     <div class="coast" style="right: 140px;left: 7px;background: rgba(0, 216, 76, 0.7);text-align: center;">`+ element.coupon +`%</div>
                     <div class="coast">`+ element.price +` руб</div>
                     <div class="name"><a href="/item/`+ element.id +`">`+ element.name +`</a></div>
                     <div class="poster"><a href="/item/`+ element.id +`"><img src="`+ element.image +`"></a></div>
                   </div>
                     `);
                 } else {
                   $("#content-c").append(`<div class="item-loop">
                     <div class="coast">`+ element.price +` руб</div>
                     <div class="name"><a href="/item/`+ element.id +`">`+ element.name +`</a></div>
                     <div class="poster"><a href="/item/`+ element.id +`"><img src="`+ element.image +`"></a></div>
                   </div>
                     `);
                 }

               });
             }
           },
           error: function (err) {
              alert('error');
           }
         });

       }

      </script>
      <noscript><div><img src="https://mc.yandex.ru/watch/<?=main["metrikaId"];?>" style="position:absolute; left:-9999px;" alt="" /></div></noscript>



</body></html>
