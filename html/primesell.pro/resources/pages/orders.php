<div class="panel" style="    margin-right: 15px;border-radius: 0px;">
  <div class="panel-heading">
    <h3 class="panel-title"> Мои покупки</h3>
  </div>
  <div class="panel-body">

    <?php

      if(isset($_SESSION["success"])) {
        print($_SESSION["success"]);
        unset($_SESSION["success"]);
      }

      if(isset($_SESSION["error"])) {
        print($_SESSION["error"]);
        unset($_SESSION["error"]);
      }

    ?>

    <form class="form-inline" method="post">
    						<input type="text" id="mydata1" class="inputtext" style="margin-top: 10px;" placeholder="Введите e-mail" name="email">
    						<input type="submit" id="btn" style="margin-top: 10px;background: #981803;" class="btnr" value="Отправить">
    					</form>

</div>
</div>
