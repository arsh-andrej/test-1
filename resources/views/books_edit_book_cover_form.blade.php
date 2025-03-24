{!!$HTMLContent_HTML!!}

<div class="books_form_out">
<form action="{!!$formAction_STR!!}" class="HTMLForm" method="post" enctype="multipart/form-data">

<input type="hidden" name="_token" value="{{csrf_token()}}">
{!!$book_cover_file_input!!}<br><br>

<div onclick="this.parentNode.submit();" class="majorAJAXHTMLButton">Загрузить</div>

</form>
</div>