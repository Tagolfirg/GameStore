<?php
  $mysqli = database::connect(); $mysqli->set_charset("utf8");
  $id = $args[3];
  $title = $mysqli->query("SELECT * FROM `store` WHERE `id` = '$id'");
  $title = mysqli_fetch_array($title);
  database::close($mysqli);
?>

<div class="bg-white shadow-sm rounded p-4 mt-4 col-12">
  <h5> Обновить товар </h5>


  <form method="post" enctype="multipart/form-data">

  <div class="form-group mt-4">
    <label>Название игры</label>
    <input type="text" class="form-control" name="title" placeholder="Название игры" value="<?=$title["name"]; ?>" required>
  </div>

  <div class="form-group mt-4">
    <label>Цена (в рублях)</label>
    <input type="text" class="form-control" name="price" value="<?=$title["price"]; ?>" required>
  </div>

  <input type="hidden" name="oldimage" value="<?=$title["image"]; ?>">

  <div class="form-group">
    <label for="exampleFormControlFile1">Обновить картинку</label>
    <input type="file" class="form-control-file" name="imageupload">
  </div>

  <div class="form-group mt-4">
    <label>Скидка % (0% - Без скидки)</label>
    <input type="text" class="form-control" name="procent" value="<?=$title["coupon"]; ?>" required>
  </div>

  <div class="form-group">
    <label>Обновлять ключи</label>
    <select class="form-control" name="updated">
      <option value="1">Да</option>
      <option value="0" <? if($title["updated"] == 0) echo 'selected'; ?>>Нет</option>
    </select>
  </div>

  <div class="form-group mt-4">
    <label>Категория</label>
    <select id="inputState" class="form-control rounded-0" name="category"><?php
      $mysqli = database::connect();
      $mysqli->set_charset("utf8");
      $store = $mysqli->query("SELECT * FROM `store_categories`");
      while($result = mysqli_fetch_array($store)):
        if($result['id'] == $title["categories"]) print('<option value="'.$result["id"].'" selected>Сейчас: '.$result['name']);
        else print('<option value="'.$result["id"].'">'.$result["name"].'</option>');
      endwhile; database::close($mysqli); ?>
    </select>
  </div>


  <div class="form-group">
    <label>Ключики</label>
    <textarea class="form-control" rows="3" name="keys"><?php $mysqli = database::connect(); $mysqli->set_charset("utf8"); $store = $mysqli->query("SELECT * FROM `store_keys` WHERE store = '$id' and status = '0'"); while($result = mysqli_fetch_array($store)): echo $result["key"].PHP_EOL; endwhile; database::close($mysqli); ?></textarea>
  </div>


  <div class="form-group">
    <label>Описание</label>
    <textarea class="form-control" rows="3" id="editor1" name="text"><?=$title["text"]; ?></textarea>
  </div>

  <script>
                CKEDITOR.replace( 'editor1' );
            </script>


    <button type="submit" class="btn btn-danger">Добавить</button>

  </form>

</div>
