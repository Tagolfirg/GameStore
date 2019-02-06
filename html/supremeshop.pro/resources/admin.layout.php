<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="icon" href="/assets/admin/images/favicon.ico">
	<title>Админ-панель</title>
  <link href="/assets/admin/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/admin/css/font-awesome.css" rel="stylesheet">
  <link href="/assets/admin/css/style.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="/assets/ckeditor/ckeditor.js"></script>
</head>
<body>
	<div class="container-fluid" id="wrapper">
		<div class="row">
			<nav class="sidebar col-xs-12 col-sm-4 col-lg-3 col-xl-2">
				<h1 class="site-title"><a href="/admin"><em class="fa fa-rocket"></em> <?=main["name"];?></a></h1>

				<a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><em class="fa fa-bars"></em></a>
				<ul class="nav nav-pills flex-column sidebar-nav">

					<li class="nav-item"><a class="nav-link" href="/admin"><em class="fa fa-bars"></em> Главная</a></li>
					<li class="nav-item"><a class="nav-link" href="/admin/store"><em class="fa fa-shopping-basket"></em> Магазин</a></li>
					<li class="nav-item"><a class="nav-link" href="/admin/coupons"><em class="fa fa-shopping-basket"></em> Магазин.Купоны</a></li>
					<li class="nav-item"><a class="nav-link" href="/admin/distribution"><em class="fa fa-gift"></em> Раздача игр</a></li>
					<li class="nav-item"><a class="nav-link" href="/admin/comments"><em class="fa fa-users"></em> Комментарии</a></li>

				</ul>
				<a href="/logout" class="logout-button"><em class="fa fa-power-off"></em> Выход</a>
			</nav>
			<main class="col-xs-12 col-sm-8 col-lg-9 col-xl-10 pt-3 pl-4 ml-auto">
				<section class="row">
					<div class="col-sm-12">
						<section class="row">
							<? require($empty); ?>
						</section>
						<section class="row" style="margin-top: 100px;">
							<div class="col-12 mt-1 mb-4">Разработано <a href="https://vk.me/ekirishima">Павел К.</a></div>
						</section>
					</div>
				</section>
			</main>
		</div>
	</div>



	<script>


	function mission_add() {
	  $("#missions").append(`<div class="row mb-4" id="item_`+ i +`">
	    <div class="col-md-3">
	      <select id="inputState" class="form-control" name="mission[]">
	            <option value="vk_sub" selected>ВК Подписка</option>
	            <option value="vk_like">ВК Лайк</option>
	            <option value="vk_repost">VK Репост</option>
	      </select>
	    </div>
	    <div class="col">
	      <input type="text" class="form-control" placeholder="Введите условие..." name="value[]">
	    </div>
	    <div class="col-md-2">
	      <button type="button" class="btn btn-secondary btn-block" onclick="mission_delete(`+ i +`)">Удалить</button>
	    </div>
	  </div>

	  `);  i++;
	}

	function mission_delete(num) {
	  document.getElementById("item_" + num).remove();
	}

	function category_add() {

  var name = prompt("Название категории");

  if(name != null || name != "") {

    $.ajax({
      url: '/admin/store/category/add',
      dataType: 'json',
      type: 'POST',
      data:{'name': name},
      success: function(data){
        if(data["status"] == 'error') swal("Ошибка!", data["msg"], "error");
        else location.reload();
      },
      error: function (err) {
         swal("Ошибка!", 'Не удалось отправить запрос!', "error");
      }
    });

  }

}


function category_edit(id) {

  var name = prompt("Название категории");

  if(name != null || name != "") {

    $.ajax({
      url: '/admin/store/category/edit/' + id,
      dataType: 'json',
      type: 'POST',
      data:{'name': name},
      success: function(data){
        if(data["status"] == 'error') swal("Ошибка!", data["msg"], "error");
        else location.reload();
      },
      error: function (err) {
         swal("Ошибка!", 'Не удалось отправить запрос!', "error");
      }
    });

  }

}


	</script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="/assets/admin/dist/js/bootstrap.min.js"></script>

    <script src="/assets/admin/js/chart.min.js"></script>
    <script src="/assets/admin/js/chart-data.js"></script>
    <script src="/assets/admin/js/easypiechart.js"></script>
    <script src="/assets/admin/js/easypiechart-data.js"></script>
    <script src="/assets/admin/js/bootstrap-datepicker.js"></script>
    <script src="/assets/admin/js/custom.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>

	</body>
</html>
