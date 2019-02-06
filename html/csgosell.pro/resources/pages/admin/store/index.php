  <div class="col">


<div class="row">


  <div class="col">

    <script>
      $( function() {
        $( "#sortable" ).sortable({
          opacity: 0.8,
          cursor: 'move',
          tolerance: 'pointer',
          items:'tr',
          placeholder: 'state',
          forcePlaceholderSize: true,
          update: function(event, ui){
            $.ajax({
               url: "/api/store/rank",
               type: 'GET',
               data: $(this).sortable("serialize"),
           });
          }
        });
        $( "#sortable" ).disableSelection();
      } );
    </script>

    <div class="card mb-4">
    									<div class="card-block">
    										<h3 class="card-title">Магазин</h3>
    										<div class="table-responsive">
    											<table class="table table-bordered">
    												<thead>
    													<tr>
    														<th>#</th>
                                <th>Картинка</th>
    														<th>Название</th>
                                <th>Цена (в руб.)</th>
                                <th>Скидка (%)</th>
                                <th>Управление</th>
    													</tr>
    												</thead>
    												<tbody id="sortable">

                              <?php
                              			$mysqli = database::connect();
                              			$mysqli->set_charset("utf8");
                              			$distribution = $mysqli->query("select * from `store` ORDER BY rank");
                              			while($result = mysqli_fetch_array($distribution)):
                              ?>

                                <tr class="ui-state-default" id="item-<?=$result["id"];?>">
      														<td><?=$result["id"];?></td>
                                  <td>
                                    <img onclick="fastImage(<?=$result["id"];?>)" src="<?=$result["image"];?>" style="    width: 64px; cursor: pointer" />
                                  </td>

      														<td> <div class="input-group mb-3">
                                        <input type="text" class="form-control" value="<?=$result["name"];?>" id="value-<?=$result["id"];?>">
                                        <div class="input-group-append">
                                          <button class="btn btn-outline-secondary" type="button" onclick="fastChange(<?=$result["id"];?>)"><i class="fa fa-check" aria-hidden="true"></i></button>
                                        </div>
                                      </div>
                                  </td>
                                  <td>

                                    <div class="input-group mb-3">
                                          <input type="text" class="form-control" value="<?=$result["price"];?>" id="price-<?=$result["id"];?>">
                                          <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="fastPrice(<?=$result["id"];?>)"><i class="fa fa-check" aria-hidden="true"></i></button>
                                          </div>
                                        </div>

                                  </td>
                                  <td>
                                    <div class="input-group mb-3">
                                          <input type="text" class="form-control" value="<?=$result["coupon"];?>" id="coupon-<?=$result["id"];?>">
                                          <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" onclick="fastСoupon(<?=$result["id"];?>)"><i class="fa fa-check" aria-hidden="true"></i></button>
                                          </div>
                                        </div>
                                  </td>
                                  <td>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="window.location = '/admin/store/edit/<?=$result["id"];?>'"><i class="fa fa-edit"></i></button>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="itemDelete(<?=$result["id"];?>)"><i class="fa fa-remove"></i></button>
                                  </td>
      													</tr>

                              <?php endwhile; database::close($mysqli); ?>



    												</tbody>
    											</table>
    										</div>
    									</div>
    								</div>

  </div>


<div class="col-md-3 mt-4">
  <a href="/admin/store/add" class="btn btn-block btn-danger text-white">Добавить товар</a>
  <a href="#" class="btn btn-block btn-danger mb-4 text-white" onclick="category_add()">Добавить категорию</a>

  <ul class="list-group">
    <?php
    $mysqli = database::connect();
    $mysqli->set_charset("utf8");
    $store = $mysqli->query("SELECT * FROM `store_categories`");
    while($result = mysqli_fetch_array($store)): ?>
    <li class="list-group-item">
      <span class="badge badge-secondary" style="cursor:pointer" onclick="category_edit(<?=$result["id"];?>)"><i class="fa fa-edit"></i></span>
      <a class="badge badge-secondary text-white mr-2" href="/admin/store/category/delete/<?=$result["id"];?>"><i class="fa fa-remove"></i></a>
      <?=$result["name"];?>
    </li>
    <?php endwhile; database::close($mysqli); ?>
  </ul>


</div>



<script>

function fastChange(id) {

  var name = $('#value-' + id).val();

    $.ajax({
      url: '/fast/store/name/' + id,
      dataType: 'json',
      type: 'POST',
      data:{'name': name},
      success: function(data){
        alert(data["msg"]);
      },
      error: function (err) {
         swal("Ошибка!", 'Не удалось отправить запрос!', "error");
      }
    });

}


function fastСoupon(id) {

  var coupon = $('#coupon-' + id).val();

    $.ajax({
      url: '/fast/store/coupon/' + id,
      dataType: 'json',
      type: 'POST',
      data:{'coupon': coupon},
      success: function(data){
        alert(data["msg"]);
      },
      error: function (err) {
         swal("Ошибка!", 'Не удалось отправить запрос!', "error");
      }
    });

}



function fastPrice(id) {

  var price = $('#price-' + id).val();

    $.ajax({
      url: '/fast/store/price/' + id,
      dataType: 'json',
      type: 'POST',
      data:{'price': price},
      success: function(data){
        alert(data["msg"]);
      },
      error: function (err) {
         swal("Ошибка!", 'Не удалось отправить запрос!', "error");
      }
    });

}


function itemDelete(id) {
  if (confirm("Вы действительно хотите удалить товар #" + id)) {
    window.location = '/admin/store/delete/' + id;
  } else alert("Вы отменили удаление");
}


function fastImage(id) {
  $('#imageId').val(id);
  $('#ImageChangeModal').modal('show');
}

</script>


</div>





<!-- Изменение картинки -->
<div class="modal fade" id="ImageChangeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Изменение картинки</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <form action="/fast/change/image" method="post" enctype="multipart/form-data">

          <input type="hidden" value="null" name="id" id="imageId">

          <div class="form-group">
            <label for="exampleFormControlFile1">Выберите новую картинку</label>
            <input type="file" class="form-control-file" name="imageupload">
          </div>

          <button type="submit" class="btn btn-primary">Обновить</button>

        </form>

      </div>
    </div>
  </div>
</div>
