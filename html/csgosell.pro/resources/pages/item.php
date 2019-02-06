

<?php

	$error = false; // 0 ошибок
	$id = $args[3]; // id игры

	// Получаем игру
	$mysqli = database::connect();  $mysqli->set_charset("utf8");
	$sql = $mysqli->query("SELECT * FROM `store` WHERE id = '$id'");
	$u = $sql->fetch_assoc();

	// Проверяем существует ли игра
	if(mysqli_num_rows($sql) == null) $error = 'Игра не найдена';
	else {

		$keys = $mysqli->query("SELECT * FROM `store_keys` WHERE store = '$id' and status = 0");
		$count = mysqli_num_rows($keys);
		if($count == 0) $error = 'Ключи закончились';

	}
	database::close($mysqli);
?>

<?php if($error): print('<div class="panel" style="margin-right: 15px;border-radius: 0px;"><div class="panel-heading"><h3 class="panel-title"> Внимание!</h3></div><div class="panel-body yobject-marked">'.$error.'</div></div>'); else: ?>

	<script>document.title = '<?=$u["name"];?> - <?=main["name"];?>';</script>

	<div class="full-box" style="    border: 4px solid #2986a5;">
		<div class="poster">
			<img src="<?=$u["image"];?>">
		</div>
		<div class="infor">
			<div class="item-top">
				<span><?=$u["name"];?></span>
			</div>
			<div class="buy-item" style="padding: 10px;margin-top: 10px;">
				<div class="coast">В наличии: <?=$count;?> шт.</div>

				<? if($u["coupon"] != 0): ?>
					<div class="coast"><?=$u["price"];?> руб.</div>
					<div class="buy-link"><?=$u["coupon"];?> %.</div>
				<? else: ?>
					<div class="coast"><?=$u["price"];?> руб.</div>
				<? endif; ?>

				<a href="/buy/<?=$u["id"];?>" style="cursor: pointer;width: 100%;text-align: center;margin-top: 5px;font-size: 19px;text-decoration: none;background-image: linear-gradient(0deg, #2e3192, #1bffff);height: 50px;line-height: 50px;border-radius: 3px;" class="buy-link">Купить товар</a>
			</div>
		</div>
	</div>
	<div class="full-content" style="background: white;padding: 10px; margin-right: 10px;">
		<div class="lcol">
			<ul class="full-nav"><li class="cur">Краткое описание</li></ul>
			<div class="tab-box cur" style="width: 580px;"><?=$u["text"];?></div>
		</div>
	</div>


<?php endif;?>
