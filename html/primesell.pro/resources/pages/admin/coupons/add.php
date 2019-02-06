<div class="bg-white shadow-sm rounded p-4 mt-4 col-12">
  <h5> Добавить купон </h5>

  <form action="/admin/coupons/add" method="post">

  <div class="form-group mt-4">
    <label>Код</label>
    <input type="text" class="form-control" name="code" placeholder="Код" required>
  </div>

  <div class="form-group mt-4">
    <label>Кол-во %</label>
    <input type="text" class="form-control" name="procent" value="5" placeholder="Кол-во %" required>
  </div>


    <button type="submit" class="btn btn-danger">Добавить купон </button>

  </form>


</div>
