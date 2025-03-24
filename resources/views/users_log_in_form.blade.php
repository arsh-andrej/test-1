<div>

<form id="{{$formId_STR}}" class="HTMLForm" method="post">

@csrf

{!!$user_login!!}<br>

{!!$user_passw!!}<br>

<div id="{{ $AJAXButtonId_STR }}" onclick="App.AJAX.onButtonClick('{{ $formId_STR }}', {{ $fieldsIdsSet_ARR }}, {{ $fieldsNamesSet_ARR }}, '{{ $requestAddr_STR }}', 1, this, 'default', '{{csrf_token()}}');" class="majorAJAXHTMLButton">Подтвердить</div>

</form>
</div>