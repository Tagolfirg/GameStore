
<script type="text/javascript">

function updateComment(id, comment) {

  var newcomment = prompt("Введите новый комментарий", comment);

  if(newcomment) {

    $.ajax({
      url: '/api/admin/comments',
      type: 'GET',
      data: {
        'method': 'update',
        'id': id,
        'text': newcomment
      },
      success: function(data) {
        alert(data);
      },
      error: function (err) {
        alert('error');
      }
    });

  }

}

function successComment(id) {

  $.ajax({
    url: '/api/admin/comments',
    type: 'GET',
    data: {
      'method': 'success',
      'id': id
    },
    success: function(data) {
      alert(data);
    },
    error: function (err) {
      alert('error');
    }
  });

}

function deleteComment(id) {

  $.ajax({
    url: '/api/admin/comments',
    type: 'GET',
    data: {
      'method': 'delete',
      'id': id
    },
    success: function(data) {
      alert(data);
    },
    error: function (err) {
      alert('error');
    }
  });

}

function fakeaddComment() {

  var name = $('#fake_name').val();
  var photo = $('#fake_photo').val();
  var text = $('#fake_text').val();

  $.ajax({
    url: '/api/admin/comments',
    type: 'GET',
    data: {
      'method': 'fake',
      'name': name,
      'photo': photo,
      'text': text
    },
    success: function(data) {
      alert(data);
    },
    error: function (err) {
      alert('error');
    }
  });

}
</script>


<div class="col-12 mb-3 mt-2"><div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom"><h1 class="h2">Комментарии</h1></div></div>



<div class="col">

  <?php
  $mysqli = database::connect(); $mysqli->set_charset("utf8");
  $sql = $mysqli->query("SELECT * FROM `comments` ORDER BY id DESC LIMIT 100");
  while($result = mysqli_fetch_array($sql)):
    ?>

    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title"><?=$result["name"];?></h5>
        <p class="card-text"><?=$result["review"];?></p>
        <?php if($result["status"] == 0): ?>
          <a href="#" class="btn btn-success text-dark" onclick="successComment(<?=$result["id"];?>)">Одобрить</a>
        <?php endif; ?>
        <a href="#" class="btn btn-outline-secondary text-dark" onclick="updateComment(<?=$result["id"];?>, `<?=$result["review"];?>`)">Редактировать</a>
        <a href="#" class="btn btn-outline-danger text-dark" onclick="deleteComment(<?=$result["id"];?>)">Удалить</a>
      </div>
    </div>

  <?php endwhile; database::close($mysqli); ?>

</div>

<div class="col-lg-3">

  <div class="card">
    <div class="card-header">Фейк отзыв</div>
    <div class="card-body">
      <form>

        <div class="form-group">
          <label>Имя Фамилия</label>
          <input type="text" class="form-control" id="fake_name" placeholder="Максон Петух">
        </div>

        <div class="form-group">
          <label>Фотография</label>
          <input type="text" class="form-control" id="fake_photo" placeholder="ГОЛЫЙ МАКСОН .jpeg">
        </div>

        <div class="form-group">
          <textarea class="form-control" id="fake_text" rows="3" placeholder="Сайт хуйня"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block" onclick="fakeaddComment()">Создать</button>
      </form>
    </div> </div>

  </div>