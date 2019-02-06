
<?php
  $mysqli = database::connect(); $mysqli->set_charset("utf8");
  $id = $args[3];
  $title = $mysqli->query("SELECT * FROM `store_coupons` WHERE `id` = '$id'");
  $title = mysqli_fetch_array($title);
  database::close($mysqli);
?>

<div class="bg-white shadow-sm rounded p-4 mt-4 col-12">
  <h5> Редактировать купон </h5>

  <form action="/admin/coupons/edit/<?=$id;?>" method="post">

  <div class="form-group mt-4">
    <label>Код</label>
    <input type="text" class="form-control" name="code" value="<?=$title["code"]; ?>" placeholder="Код" required>
  </div>

  <div class="form-group mt-4">
    <label>Кол-во %</label>
    <input type="text" class="form-control" name="procent" value="<?=$title["procent"]; ?>" placeholder="Кол-во %" required>
  </div>


    <button type="submit" class="btn btn-danger">Редактировать купон </button>

  </form>


</div>
