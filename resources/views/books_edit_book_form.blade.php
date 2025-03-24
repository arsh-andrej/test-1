<form id="{{$formId_STR}}" class="HTMLForm" method="post">

@csrf

{!!$book_author!!}<br>

{!!$book_title!!}<br>

{!!$book_year!!}<br>

{!!$genre_title!!}<br>

{!!$pages_count!!}<br>

<div id="{{$AJAXButtonId_STR}}" onclick="App.AJAX.onButtonClick('{{$formId_STR}}', {{$fieldsIdsSet_ARR}}, {{$fieldsNamesSet_ARR}}, '{{$requestAddr_STR}}', 1, this, 'default', '{{csrf_token()}}');" class="majorAJAXHTMLButton">Подтвердить</div>

</form>