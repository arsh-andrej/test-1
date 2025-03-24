<div class="HTMLField actHTMLField selHTMLField HTMLFieldTitleWithQuestMark" id="{{ $HTMLFieldId_STR }}"{!!$HTMLFieldName_STR ?? ''!!}>

<div class="HTMLFieldTitle">{{ $HTMLFieldTitle_STR }}</div>

<div class="HTMLButton auxHTMLButton absolHTMLButton helpHTMLButton HTMLFieldHelpButton" onclick="App.inNodeShowOrHideHTMLWinByClass(this.parentNode, 'helpHTMLWin');"></div>
<select class="HTMLInput">{!!$selectOptions_HTML!!}</select>

<div class="HTMLWin helpHTMLWin HTMLFieldWin" style="display: none;">
<div class="HTMLButton auxHTMLButton absolHTMLButton closeHTMLButton HTMLWinCloseButton" onclick="App.hideHTMLWinByNode(this.parentNode);"></div>
<div class="HTMLWinText">{!! $HTMLFieldDescr_HTML !!}</div>
</div>

<div class="HTMLWin errorHTMLWin HTMLFieldWin" style="display: none;">
<div class="HTMLButton auxHTMLButton absolHTMLButton closeHTMLButton HTMLWinCloseButton" onclick="App.hideHTMLWinByNode(this.parentNode);"></div>
<div class="HTMLWinText"></div>
</div>

</div>