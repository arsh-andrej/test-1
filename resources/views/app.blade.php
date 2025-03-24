<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Тестовое задание</title>
    <link rel="stylesheet" href="{{ asset('res/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('res/css/aux_errors.css') }}">
    <script src="{{ asset('res/js/app.js') }}"></script>
    <style>.HTMLForm { max-width: 450px; }</style>
</head>
<body>

<div id="root_out">

<div id="root_top_out" class="inroot_layer_out">
<div class="menu_elem_out"><a href="/">главная</a></div><div class="menu_elem_out"><a href="/books/add-book">добавить книгу</a></div><div class="menu_elems_delim"></div>
<div class="menu_elem_out menu_elem_log_out_{!!$isVisitorLogIn_INT!!}"><a href="/users/log-out">выйти</a></div><div class="menu_elem_out menu_elem_reg_user_{!!$isVisitorLogIn_INT!!}"><a href="/users/reg-user">регистрация</a></div><div class="menu_elem_out menu_elem_log_in_{!!$isVisitorLogIn_INT!!}"><a href="/users/log-in">вход</a></div>
</div><!--root_top_out-->

<div id="root_content_out" class="inroot_layer_out">{!! $rootContent_HTML !!}</div><!--root_content_out-->

<div id="root_bottom_out" class="inroot_layer_out"></div><!--root_bottom_out-->

</div><!--root_out-->

</body>
</html>
