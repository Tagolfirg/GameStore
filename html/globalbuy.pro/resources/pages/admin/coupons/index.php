<div class="col">

  <div class="card mb-4">
                    <div class="card-block">

                      <div class="row">
                        <div class="col"><h3 class="card-title">Купоны</h3></div>
                        <div class="col-lg-4"><button onclick="window.location = '/admin/coupons/add'" type="button" class="btn btn-sm btn-secondary btn-block"><i class="fa fa-plus"></i> Добавить промокод</button></div>

                      </div>

                      <div class="table-responsive">
                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Код</th>
                              <th>Процент</th>
                              <th>Управление</th>
                            </tr>
                          </thead>
                          <tbody>

                            <?php
                                  $mysqli = database::connect();
                                  $mysqli->set_charset("utf8");
                                  $distribution = $mysqli->query("select * FROM `store_coupons` ORDER BY id DESC");
                                  while($result = mysqli_fetch_array($distribution)):
                            ?>

                              <tr>
                                <td><?=$result["id"];?></td>
                                <td><?=$result["code"];?></td>
                                <td><?=$result["procent"];?>%</td>
                                <td>
                                  <button type="button" onclick="window.location = '/admin/coupons/edit/<?=$result["id"];?>'" class="btn btn-sm btn-secondary"><i class="fa fa-edit"></i></button>
                                  <button type="button" onclick="window.location = '/admin/coupons/delete/<?=$result["id"];?>'" class="btn btn-sm btn-secondary"><i class="fa fa-remove"></i></button>
                                </td>
                              </tr>

                            <?php endwhile; database::close($mysqli); ?>



                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

</div>
