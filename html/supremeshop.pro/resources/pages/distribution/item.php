    <?php

      $error = false; // 0 ошибок
      $id = $args[3]; // id игры

      // Получаем игру
      $mysqli = database::connect();  $mysqli->set_charset("utf8");
      $sql = $mysqli->query("SELECT * FROM `distribution` WHERE id = '$id'");
      $u = $sql->fetch_assoc();

      // Проверяем существует ли игра
      if(mysqli_num_rows($sql) == null) $error = 'Игра не найдена';
      else {

        $keys = $mysqli->query("SELECT * FROM `distribution_keys` WHERE game_id = '$id' and status = 0");
        $count = mysqli_num_rows($keys);

        if($count == 0) $error = 'Ключи закончились';

        // Проверяем забирал ли я ключ?
        if(User::check()) {
          $uid = $_SESSION["id"];
          $check = $mysqli->query("SELECT * FROM `distribution_keys` WHERE game_id = '$id' and user_id = '$uid'");
          if(mysqli_num_rows($check) != null) {

            $u = $check->fetch_assoc();
            $key = $u["s.key"];

            $error = 'Вы уже учавствовали в игре! <br /> Ваш ключ: '.$key;
          }
        } else $error = '<a href="/auth" style="cursor: pointer;text-decoration: none;padding: 10px;background: #3498db;color: white;width: 100%;display: block;text-align: center;" class="buy-link">Войти через Вконтакте!</a>';

      }
      database::close($mysqli);
    ?>

    <?php if($error): print('<div class="panel" style="margin-right: 15px;border-radius: 0px;"><div class="panel-heading"><h3 class="panel-title"> Внимание!</h3></div><div class="panel-body yobject-marked">'.$error.'</div></div>'); else: ?>




      <div class="full-box">
      				<div class="poster">
      					<img src="<?=$u["image"];?>">
      				</div>
      				<div class="infor">
      					<div class="item-top">
      						<span><?=$u["name"];?></span>
      					</div>
      					<div class="buy-item">
      						<div class="coast">В наличии: <?=$count;?> шт.</div>
      					</div>
      				</div>
      			</div>


            <div class="full-content" style="background: white;padding: 10px; margin-right: 10px;">
            				<div class="lcol">
            					<ul class="full-nav"><li class="cur">Условия</li></ul>
            					<div class="tab-box cur" style="    width: 585px;">
                        <?php $missions = json_decode($u["missions"], true);
                        foreach ($missions as $key):
                          $value = $key["value"]; $mission = $key["mission"];
                          if($mission == 'vk_like') echo '<a href="https://vk.com/feed?w=wall'.$value.'" id="'.$value.'" class="btn btn-primary btn-block" target="_blank"><i style="line-height: 25px;" class="fa fa-vk" aria-hidden="true"></i> Поставить лайк</a>';
                          else if($mission == 'vk_sub') echo '<a href="https://vk.com/'.$value.'" id="'.$value.'" class="btn btn-primary btn-block" target="_blank"><i style="line-height: 25px;" class="fa fa-vk" aria-hidden="true"></i> Подписаться</a>';
                          else if($mission == 'vk_repost') echo '<a href="https://vk.com/feed?w=wall'.$value.'" id="repost'.$value.'" class="btn btn-primary btn-block" target="_blank"><i style="line-height: 25px;" class="fa fa-vk" aria-hidden="true"></i> Сделать репост</a>';
                          endforeach; ?>
                        <button type="submit" id="go_geme" class="btn btn-success mt-4 btn-block"><i class="fa fa-key" aria-hidden="true"></i> Получить игру</button>
            					</div>
            				</div>
            			</div>


    <script type="text/javascript">

  		$(document).ready(function() {

  			$('#go_geme').on('click',function(){

  			$.ajax({
  		    url: '/distribution/check/<?=$id;?>',
          dataType: 'json',
  		    type: 'POST',
  		    success: function(data){
            if(data["status"] == 'error') {
              $('#' + data["msg"]["id"]).addClass("btn-danger");
              swal("Ошибка!", data["msg"]["msg"], "error");
            }
            else swal("Поздравляем!", data["msg"], "success");
  		    },
  	      error: function (err) {
  	         swal("Ошибка!", 'Не удалось отправить запрос!', "error");
  	      }
  		  });

  		})

  	})



  	</script>

  <?php endif;?>
