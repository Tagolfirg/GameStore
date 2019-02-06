<div class="bg-white shadow-sm rounded p-4 mt-4 col-12">
  <h5> Добавить товар </h5>


  <form method="post" enctype="multipart/form-data">

  <div class="form-group mt-4">
    <label>Название игры</label>
    <input type="text" class="form-control" name="title" placeholder="Название игры" required>
  </div>

  <div class="form-group mt-4">
    <label>Цена (в рублях)</label>
    <input type="text" class="form-control" name="price" value="1" required>
  </div>
  <div class="form-group">
    <label for="exampleFormControlFile1">Ссылка на картинку</label>
    <input type="file" class="form-control-file" name="imageupload" required>
  </div>

  <div class="form-group mt-4">
    <label>Скидка % (0% - Без скидки)</label>
    <input type="text" class="form-control" name="procent" value="0" required>
  </div>

  <div class="form-group">
    <label>Обновлять ключи</label>
    <select class="form-control" name="updated">
      <option value="1">Да</option>
      <option value="0">Нет</option>
    </select>
  </div>


  <div class="form-group mt-4">
    <label>Категория</label>
    <select id="inputState" class="form-control rounded-0" name="category">
      <?php
      $mysqli = database::connect();
      $mysqli->set_charset("utf8");
      $store = $mysqli->query("SELECT * FROM `store_categories`");
      while($result = mysqli_fetch_array($store)): ?>
        <option value="<?=$result['id'];?>"><?=$result['name'];?></option>
      <?php endwhile; database::close($mysqli); ?>
    </select>
  </div>


  <div class="form-group">
    <label>База ключей (Через новую строку)</label>
    <textarea class="form-control" rows="3" name="keys"></textarea>
  </div>


  <div class="form-group">
    <label>Описание</label>
    <textarea class="form-control" rows="3" id="editor1" name="text"></textarea>
  </div>


  <script>
                CKEDITOR.replace( 'editor1' );
            </script>

    <button type="submit" class="btn btn-danger">Добавить</button>

  </form>

</div>
