  <div class="bg-white shadow-sm rounded p-4 mt-4 col-12">
    <h5> Добавить игру на раздачу </h5>

    <form action="/admin/distribution/add" method="post">

    <div class="form-group mt-4">
      <label>Название игры</label>
      <input type="text" class="form-control" name="title" placeholder="Название игры" required>
    </div>
    <div class="form-group mt-4">
      <label>Ссылка на картинку</label>
      <input type="text" class="form-control" name="image" placeholder="Ссылка на картинку" required>
    </div>
    <div class="form-group">
      <label>База ключей (Через новую строку)</label>
      <textarea class="form-control" rows="3" name="keys"></textarea>
    </div>
    <div class="form-group">
      <label>Условия</label>
      <?php require(BASE_PATH.'/resources/pages/admin/distribution/rules.php'); ?>
      <div id="missions">

          <div class="row mb-4" id="item_0">
            <div class="col-md-3">
              <select id="inputState" class="form-control" name="mission[]">
                    <option value="vk_sub" selected>ВК Подписка</option>
                    <option value="vk_like">ВК Лайк</option>
                    <option value="vk_repost">ВК Репост</option>
              </select>
            </div>
            <div class="col">
              <input type="text" class="form-control" placeholder="Введите условие..." name="value[]">
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-secondary btn-block" onclick="mission_delete(0)">Удалить</button>
            </div>
          </div>

      </div>

      <button type="button" class="btn btn-secondary" onclick="mission_add()">Добавить условие + </button>
      <button type="submit" class="btn btn-danger">Добавить игру на раздачу </button>

    </form>



    </div>

  </div>



<script>var i = 1;</script>
