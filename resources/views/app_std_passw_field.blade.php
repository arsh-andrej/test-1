<div class="HTMLField actHTMLField strHTMLField HTMLFieldTitleWithQuestMark" id="{{ $HTMLFieldId_STR }}">

<div class="HTMLFieldTitle">{{ $HTMLFieldTitle_STR }}</div>

<div class="HTMLButton auxHTMLButton absolHTMLButton helpHTMLButton HTMLFieldHelpButton" onclick="App.inNodeShowOrHideHTMLWinByClass(this.parentNode, 'helpHTMLWin');"></div>
<input class="HTMLInput" type="text" autocomplete="off">

<div class="HTMLWin helpHTMLWin HTMLFieldWin" style="display: none;">
<div class="HTMLButton auxHTMLButton absolHTMLButton closeHTMLButton HTMLWinCloseButton" onclick="App.hideHTMLWinByNode(this.parentNode);"></div>
<div class="HTMLWinText">{!! $HTMLFieldDescr_HTML !!}</div>
</div>

<div class="HTMLWin errorHTMLWin HTMLFieldWin" style="display: none;">
<div class="HTMLButton auxHTMLButton absolHTMLButton closeHTMLButton HTMLWinCloseButton" onclick="App.hideHTMLWinByNode(this.parentNode);"></div>
<div class="HTMLWinText"></div>
</div>

</div>