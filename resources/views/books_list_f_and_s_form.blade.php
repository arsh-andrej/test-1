<form id="{{$HTMLFormId_STR}}" class="HTMLForm" method="post" onclick="App.onMultiboxFormClick(event);">
@csrf
{!!$formContent_HTML!!}
</form>