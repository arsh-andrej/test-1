<div id="book_{!!$bookId_INT!!}" class="book_page_out">

<div class="x_table">

<div class="x_row">

<div class="x_td td_left">

<div class="book_cover" style="background-image: url({!!$bookCoverURL_STR!!});"></div>

</div><!--x_td-->

<div class="x_td td_right">

<div class="book_title">{!!$booTitle_STR!!}</div>

<div class="book_author">{!!$bookAuthor_STR!!}, {!!$bookYear_INT!!} год, {!!$pagesCount_INT!!} страниц</div>
<br>
<div class="book_actions">
<a href="/books/edit-book-cover/{{$bookId_INT}}">Изменить обложку</a><br>
<a href="/books/edit-book/{{$bookId_INT}}">Редактировать информацию</a>
<br><br>
<div class="minorSmallButton" onclick="App.showOrHideBlockById('delete_book');">Удалить книгу...</div>
<div id="delete_book" style="display: none;"><br><br><div class="majorAJAXHTMLButton" onclick="App.AJAX.onButtonClick('', [], [], '/books/delete-book/{!!$bookId_INT!!}?mode=ajax', 0, this, 'default', '{{csrf_token()}}')">Подтвердить</div></div>
</div>

</div><!--x_td-->

</div><!--x_row-->

</div><!--x_table-->

</div>