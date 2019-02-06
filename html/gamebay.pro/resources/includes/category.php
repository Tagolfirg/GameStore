<li class="layer"><a href="#" onclick="items_get('all')">Все товары</a></li>

<?php
        $mysqli = database::connect();
        $mysqli->set_charset("utf8");
        $store = $mysqli->query("SELECT * FROM `store_categories`");
        while($result = mysqli_fetch_array($store)): ?>
        <li class="layer"><a href="#" onclick="items_get(<?=$result["id"];?>)"><?=$result["name"];?></a></li>
        <?php endwhile; database::close($mysqli); ?>
