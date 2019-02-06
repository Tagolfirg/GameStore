<?php
  $mysqli = database::connect(); $mysqli->set_charset("utf8");
  $id = $args[2];
  $title = $mysqli->query("SELECT * FROM `distribution` WHERE `id` = '$id'");
  $title = mysqli_fetch_array($title);
  database::close($mysqli);
?>

<div class="bg-white shadow-sm rounded p-4 mt-4 col-12">
  <h5> Редактировать раздачу </h5>

  <form action="/admin/distribution/edit" method="post">

  <input type="hidden" name="id" value="<?=$args[2];?>">

  <div class="form-group mt-4">
    <label>Название игры</label>
    <input type="text" class="form-control" name="title" placeholder="Название игры" value="<?=$title["name"]; ?>" required>
  </div>
  <div class="form-group mt-4">
    <label>Ссылка на картинку</label>
    <input type="text" class="form-control" name="image" placeholder="Ссылка на картинку" value="<?=$title["image"]; ?>" required>
  </div>
  <div class="form-group">
    <label>Добавить ключи (Через новую строку)</label>
    <textarea class="form-control" rows="3" name="keys"></textarea>
  </div>
  <div class="form-group">
    <label>Условия</label>

    <?php require(BASE_PATH.'/resources/pages/admin/distribution/rules.php'); ?>

    <div id="missions">


      <?php $missions = json_decode($title["missions"], true); $i = 0;
      foreach ($missions as $key):
        $value = $key["value"]; $mission = $key["mission"];

        echo '<div class="row mb-4" id="item_'.$i.'">
          <div class="col-md-3">
            <select id="inputState" class="form-control" name="mission[]">';
            if($mission == 'vk_like') echo '<option value="'.$mission.'">ВК Лайк</option>';
            else if($mission == 'vk_sub') echo '<option value="'.$mission.'">ВК Подписка</option>';
            else if($mission == 'vk_repost') echo '<option value="'.$mission.'">VK Репост</option>';
            echo '</select></div><div class="col"><input type="text" class="form-control" placeholder="Введите условие..." name="value[]" value="'.$value.'"></div><div class="col-md-2"><button type="button" class="btn btn-secondary btn-block" onclick="mission_delete('.$i.')">Удалить</button></div></div>';
            echo '<script> var i = '.$i.'</script>'; $i++;

        endforeach;


    ?>

    </div>

    <button type="button" class="btn btn-secondary" onclick="mission_add()">Добавить условие + </button>
    <button type="submit" class="btn btn-danger">Добавить игру на раздачу </button>

  </form>



  </div>

</div>
