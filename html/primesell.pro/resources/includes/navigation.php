<li><a href="/">Главная</a></li>
<li><a href="/comments" style="color: #3498db">Отзывы</a></li>
<li><a href="/distribution" style="color: #99ec0d;">Бесплатные ключи!</a></li>
<li><a href="<?=main["support"];?>" target="_blank">Поддержка</a></li>
<li><a href="/garant">Гарантии</a></li>
<!--li><a href="/youtube" style="color: #e74c3c">Видео-Проверки</a></li-->
<li><a href="/rules">Правила</a></li>

<?php if(User::check()): ?>
<li style="float: right"><a href="/logout">Выход</a></li>
<?php if(User::admin()): ?><li style="float: right"><a href="/admin">Панель-управления</a></li><?php endif; ?>
<?php else: ?>
<li style="float: right"><a href="/auth">Авторизация</a></li>
<?php endif; ?>