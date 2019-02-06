
<div class="col-12 mb-4 mt-3">

  <div class="row">
    <div class="col-12 col-sm-9"><h3 class="page-title">Раздача игр</h3></div>
    <div class="col-12 col-sm-3 text-center text-sm-left mb-0">
      <a href="/admin/distribution/add" class="btn btn-block btn-danger text-white">Добавить товар</a>
    </div>
  </div>

</div>



<?php
			$mysqli = database::connect();
			$mysqli->set_charset("utf8");
			$distribution = $mysqli->query("SELECT game.id, game.name, game.image, count(game.id) as counts FROM `distribution` as game, `distribution_keys` as dk WHERE game.id = dk.`game_id` and dk.status = 0 GROUP BY game.id");
			while($result = mysqli_fetch_array($distribution)):
?>

  <div class="col col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 mb-4">
    <div class="card" style="height: 100%"><img class="card-img-top" src="<?=$result["image"];?>" >
        <div class="card-body">
            <h4 class="card-title"><?=$result["name"];?></h4>
            <p class="card-text">Ключей осталось: <?=$result["counts"];?></p>
            <a href="/distribution/item/<?=$result["id"];?>" class="btn btn-primary btn-block">Открыть на сайте</a>
            <a href="/admin/distribution/edit/<?=$result["id"];?>" class="btn btn-info btn-block">Редактировать</a>
            <a href="/admin/distribution/delete/<?=$result["id"];?>" class="btn btn-danger btn-block">Удалить</a>
        </div>
    </div>


  </div>

<?php endwhile; database::close($mysqli); ?>
