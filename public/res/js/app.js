
//была ли серьёзная ошибка
//например : "Системная ошибка"
const RESULT_DEFS_IS_HARD_ERR	=	'aa';

//была ли простая ошибка
//например : "Пользователь с таким логином уже зареган"
const RESULT_DEFS_IS_SOFT_ERR	=	'ab';

//1 если нет ошибок(два верхних элемента == '0')
const RESULT_DEFS_IS_SUCCESS	=	'ac';

//текст ошибки или ответа
//результат работы который увидит пользователь
//например : "Регистрация успешно завершена"
//например : "Пользователь с таким логином уже зареган"
const RESULT_DEFS_MESSAGE	=	'ae';

//любые доп данные для работы приложения
const RESULT_DEFS_ADD_DATA	=	'af';
	
	//пуст ли массив с доп данными
	const ADD_DATA_DEFS_IS_EMPTY	=	'ba';
	
	//ID HTMLField в котором произошла ошибки
	const ADD_DATA_DEFS_ERROR_FIELD_ID	=	'bb';
	
	//действие которое должен выполнить обработчик в app.js
	const PARAMS_DEFS_ACTION	=	'bc';
		
		//
		const PARAMS_ACTION_SHOW_ERROR_IN_FIELD_WIN	=	'1';
		
		//
		const PARAMS_ACTION_SUBMIT_FORM_TO_PATH	=	'2';
		
	//путь по которому нужно отправить пользователя
	const PARAMS_DEFS_SUBMIT_PATH	=	'bd';
	
const App =   {
    
	'retElemNodeById'   :   function (elemId_STR) {
		
		let elemNode_OBJ    =   document.getElementById(elemId_STR);
		let result_ARR  =   [false, {}];
		
		if (elemNode_OBJ !== undefined) {
			
			result_ARR  =   [true, elemNode_OBJ];
			
		} return result_ARR;
		
	},//App.retElemNodeById
	
	//class - это один класс
	//className - это "class1 class2 и тд"
	//вернёт true/false
	'retHasNodeClass'   :   function (elemNode_OBJ, class_STR) {
		
		return new RegExp("(^|\\s)" + class_STR + "(\\s|$)").test(elemNode_OBJ.className);
		
	},//App.retHasNodeClass
	
	'retFindNodesByClass'  :   function (insideNode_OBJ, findClass_STR, findAllNodes_INT) {
		
		let result_ARR  =   [false, {}, [], 0];
		
		//массив результата вызова в рекурсии
		let callResult_ARR  =   [];
		
		//insideNode_OBJ.children;
		
		//insideNode_OBJ.childElementCount;
		
		for (let i = 0; i < insideNode_OBJ.childElementCount; i++) {
			
			let cycle_elemNode_OBJ =   insideNode_OBJ.children[i];
			
			if (this.retHasNodeClass(cycle_elemNode_OBJ, findClass_STR)) {
				
				//если нужно найти первый попавшийся узел
				if (findAllNodes_INT == 0) {
					
					result_ARR  =   [true, cycle_elemNode_OBJ];
					break;
					
				} else {
					
					//нужно найти абсолютно все узлы
					result_ARR[0]	=	true;
					result_ARR[2][result_ARR[3]]	=	cycle_elemNode_OBJ;
					result_ARR[3]	=	result_ARR[3] + 1;
					
				}//если нужно найти первый попавшийся узел
				
			}
			
			if (cycle_elemNode_OBJ.childElementCount > 0) {
				
				if (findAllNodes_INT == 0) {
					
					let call_result_ARR  =   this.retFindNodesByClass(cycle_elemNode_OBJ, findClass_STR, findAllNodes_INT);
					
					if (call_result_ARR[0]) { result_ARR  =   call_result_ARR; break; }
					
				} else {
					
					let call_result_ARR	=	this.retFindNodesByClass(cycle_elemNode_OBJ, findClass_STR, 1);
					
					//нужно найти абсолютно все узлы
					result_ARR[0]	=	true;
					result_ARR[2]	=	result_ARR[2].concat(call_result_ARR[2]);
					result_ARR[3]	=	result_ARR[3] + call_result_ARR[3];
					
				}
				
			}
			
		} return result_ARR;
		
	},//App.retFindNodesByClass
	
	'hideHTMLWinByNode' :   function (winNode_OBJ) { winNode_OBJ.style.display  =   'none'; },
	'showHTMLWinByNode' :   function (winNode_OBJ) { winNode_OBJ.style.display  =   'block'; },
	
	'inNodeShowOrHideHTMLWinByClass'    :   function(inNode_OBJ, className_STR) {
		
		let result_ARR	=	this.retFindNodesByClass(inNode_OBJ, 'HTMLFieldWin', 0);
		
		if (result_ARR[0]) {
			
			if (result_ARR[1].style.display === 'none') {
				
				result_ARR[1].style.display	=	'block';
				
			} else { result_ARR[1].style.display	=	'none'; }
			
		}
		
	},
	
	'hideHTMLFormWins'  :   function (formId_STR) {
		
		let result_ARR  =   this.retElemNodeById(formId_STR);
		
		if (result_ARR[0]) {
			
			result_ARR	=	this.retFindNodesByClass(result_ARR[1], 'HTMLFieldWin', 1);
			
			for (let i = 0; i < result_ARR[3]; i++) { result_ARR[2][i].style.display	=	'none'; }
			
		}
		
	},//App.hideHTMLFormWins
	
	'retHTMLFieldValueById'	:	function (HTMLFieldId_STR, isEncodeReq_INT) {
		
		let call_result_ARR	=	this.retElemNodeById(HTMLFieldId_STR);
		let valsSet_ARR	=	[];
		
		//если узел поля получен
		if (call_result_ARR[0]) {
			
			HTMLFieldNode_OBJ	=	call_result_ARR[1];
			
			if (App.retHasNodeClass(HTMLFieldNode_OBJ, 'multiboxHTMLField')) {
				
				let result_ARR	=	this.retFindNodesByClass(HTMLFieldNode_OBJ, 'HTMLInput', 1);
				//console.log(result_ARR);//!!!//
				
				if (result_ARR[0]) {
					
					for (let i = 0; i < result_ARR[3]; i++) {
						
						let inputNode_OBJ	=	result_ARR[2][i];
						
						//вытащить значение нужно только если флажок поставлен
						if (inputNode_OBJ.title == '1') {
							
							valsSet_ARR[valsSet_ARR.length]	=	result_ARR[2][i].value;
							
						}
						
					}
					
					if (isEncodeReq_INT == 1) {
					
						return json_encode(valsSet_ARR);
						
					} else { return valsSet_ARR; }
					
				}//иначе HTMLInputs not found
				
			} else {
				
				return this.retHTMLFieldValueByInputNode(
					this.retFindNodesByClass(HTMLFieldNode_OBJ, 'HTMLInput', 0)[1],
					isEncodeReq_INT
				);
				
			}
			
		} else { console.log('всё плохо в App.retHTMLFieldValueById'); }
		
		return '';
		
	},//App.retHTMLFieldValueById
	
	'onFiltersAndSortFormButtonClick'	:	function(filtersFormId_STR, sortFormId_STR, currPath_STR) {
		
		let filtersFormNode_OBJ	=	this.retElemNodeById(filtersFormId_STR)[1];
		let result_ARR	=	this.retFindNodesByClass(filtersFormNode_OBJ, 'multiboxHTMLField', 1);
		
		if (!result_ARR[0]) { return; }
		
		let formFieldsVals_ARR	=	[];
		
		for (let i = 0; i < result_ARR[3]; i++) {
			
			fieldNode_OBJ	=	result_ARR[2][i];
			
			formFieldsVals_ARR[formFieldsVals_ARR.length]	=	[
				
				fieldNode_OBJ.id,//0
				fieldNode_OBJ.title,//1
				App.retHTMLFieldValueById(fieldNode_OBJ.id, 0)//2
				
			];
			
		}
		
		let HTTPQuery_STR	=	'';
		
		let filterColsSet_STR	=	'';
		let filterColsCount_INT	=	0;
		
		let filterRulesSet_STR	=	'';
		let filterRulesCount_INT=	0;
		
		for (let i = 0; i < formFieldsVals_ARR.length; i++) {
			
			let colName_STR	=	formFieldsVals_ARR[i][1];
			let colValsSet_STR	=	'';
			let valsCount_INT	=	formFieldsVals_ARR[i][2].length;
			
			for (let x = 0; x < valsCount_INT; x++) {
				
				if (x > 0) { colValsSet_STR	+=	','; }
				
				colValsSet_STR	+=	formFieldsVals_ARR[i][2][x];
				
			}
			
			//поле в котором нет ни одной галочки - не добавляется
			if (valsCount_INT > 0) {
				
				if (filterColsCount_INT > 0) {
					
					filterColsSet_STR	+=	',';
					filterRulesSet_STR	+=	',';
					HTTPQuery_STR	+=	'&';
					
				}
				
				filterColsSet_STR	+=	colName_STR;
				filterRulesSet_STR	+=	'only';//!!!//
				HTTPQuery_STR	+=	'filter_' + colName_STR + '=' + colValsSet_STR
				
				filterColsCount_INT++; filterRulesCount_INT++;
				
			}
			
		}
		
		if (filterColsSet_STR.length > 0) {
			
			HTTPQuery_STR	+=
				  '&' + 'filter-cols=' + filterColsSet_STR
				+ '&' + 'filter-rules=' + filterRulesSet_STR;
			
		}
		
		//сортировка
		let sortResult_ARR	=	App.handleSortForm(sortFormId_STR);
		
		if (sortResult_ARR[0] > 0) {
			
			if (HTTPQuery_STR.length > 0) { HTTPQuery_STR	+=	'&'; }
			
			HTTPQuery_STR	+=	sortResult_ARR[1];
			
		}
		
		//console.log(currPath_STR + '?' + HTTPQuery_STR);
		document.location	=	currPath_STR + '?' + HTTPQuery_STR;
		
		//console.log(HTTPQuery_STR);
		//console.log(filterColsSet_STR);
		//console.log(filterRulesSet_STR);
		//console.log(formFieldsVals_ARR);
		
	},//App.onFiltersAndSortFormButtonClick
	
	'handleSortForm'	:	function(sortFormId_STR) {
		
		let sortFormNode_OBJ	=	this.retElemNodeById(sortFormId_STR)[1];
		//console.log(sortFormNode_OBJ);
		
		result_ARR	=	this.retFindNodesByClass(sortFormNode_OBJ, 'HTMLField', 1);
		//console.log(result_ARR);
		
		let formFieldsVals_ARR	=	[];
		
		for (let i = 0; i < result_ARR[3]; i++) {
			
			fieldNode_OBJ	=	result_ARR[2][i];
			
			formFieldsVals_ARR[formFieldsVals_ARR.length]	=	[
				
				fieldNode_OBJ.title,//0
				App.retHTMLFieldValueById(fieldNode_OBJ.id, 0)//1
				
			];
			
		}
		
		let addedCount_INT	=	0;
		let sortColsSet_STR	=	'sort-cols=';
		let sortRulesSet_STR=	'sort-rules=';
		
		for (let x = 0; x < formFieldsVals_ARR.length; x++) {
			
			let sortRule_STR	=	formFieldsVals_ARR[x][1];
			if (sortRule_STR == 'default') { continue; }
			
			let colName_STR	=	formFieldsVals_ARR[x][0];
			
			if (addedCount_INT > 0) {
				
				sortColsSet_STR	+=	',';
				sortRulesSet_STR+=	',';
				
			}
			
			sortColsSet_STR	+=	colName_STR;
			sortRulesSet_STR+=	sortRule_STR;
			addedCount_INT++;
			
		}
		
		//console.log(sortColsSet_STR);
		//console.log(sortRulesSet_STR);
		//console.log(formFieldsVals_ARR)
		
		return [addedCount_INT, sortColsSet_STR + '&' +sortRulesSet_STR]
		
	},//App.handleSortForm
	
	'testVar_1'	:	'',//App.testVar_1
	
	'retHTMLFieldValueByInputNode'	:	function(HTMLInputNode_OBJ, isEncodeReq_INT) {
		
		let HTMLInputValue_ANY	=	'';
		let tagName_STR	=	HTMLInputNode_OBJ.tagName;
		
		if (tagName_STR == 'DIV' && App.retHasNodeClass(HTMLInputNode_OBJ, 'flagHTMLInput')) {
			
			//console.log(elemNode_OBJ);
			HTMLInputValue_ANY	=	HTMLInputNode_OBJ.title;
			
		} else {
		if (tagName_STR == 'TEXTAREA') {
			
			HTMLInputValue_ANY	=	HTMLInputNode_OBJ.value;
			
		} else {
		if (tagName_STR == 'INPUT') {
			
			HTMLInputValue_ANY	=	HTMLInputNode_OBJ.value;
			
		} else {
		if (tagName_STR == 'SELECT') {
			
			HTMLInputValue_ANY	=	HTMLInputNode_OBJ.childNodes[HTMLInputNode_OBJ.selectedIndex].value;
			//console.log(HTMLInputNode_OBJ.childNodes);
			
		} } } } return isEncodeReq_INT == 1 ? encodeURIComponent(HTMLInputValue_ANY) : HTMLInputValue_ANY;
		
	},//App.retHTMLFieldValueByInputNode
	
	'showOrHideBlockById'	:	function (elemId_STR) {
		
		let result_ARR	=	this.retElemNodeById(elemId_STR);
		
		if (result_ARR[0]) {
			
			let blockNode_OBJ	=	result_ARR[1];
			
			if (blockNode_OBJ.style.display == 'block') {
				
				blockNode_OBJ.style.display	=	'none';
				
			} else { blockNode_OBJ.style.display	=	'block'; }
			
		}
		
	},//App.showOrHideBlockById
	
	'onMultiboxFormClick'	:	function(event) {
		
		if (!App.retHasNodeClass(event.target, 'HTMLInput')) { return; }
		
		if (event.target.title == '1') {
			
			event.target.title	=	'0';
			
		} else { event.target.title	=	'1'; }
		
	},//App.onMultiboxFormClick
	
	'AJAX'  :   {
		
		'buttonsStatesAst_OBJ'  :   {},
		
		'unlockButtonById'	:	function(buttonId_STR) {
			
			if (this.buttonsStatesAst_OBJ.hasOwnProperty(buttonId_STR)) {
				
				this.buttonsStatesAst_OBJ[buttonId_STR]	=	'';
				
			}
			
		},//App.AJAX.unlockButtonById
		
		'onButtonClick' :   function(formId_STR, fieldsIdsSet_ARR, fieldsNamesSet_ARR, requestAddr_STR, hideWins_INT, buttonNode_OBJ, handleFuncName_STR, CSRFToken_STR) {
			
			let buttonId_STR    =   buttonNode_OBJ.id;
			let isButtonStatePass_BOO =   false;
			let formFields_ARR	=	[];
			
			if (this.buttonsStatesAst_OBJ.hasOwnProperty(buttonId_STR)) {
				
				if (this.buttonsStatesAst_OBJ[buttonId_STR] !== 'locked') {
					
					isButtonStatePass_BOO    =   true;
					
				} else { /*?*/ }
				
			} else { isButtonStatePass_BOO    =   true; }
			
			//если проверка состояния кнопки пройдена
			//это нужно чтобы не дать пользователю нажать на кнопку пока сервер обрабатывает ajax запрос
			if (isButtonStatePass_BOO) {
				
				//заблокировать кнопку отправки формы
				this.buttonsStatesAst_OBJ[buttonId_STR] =   'in-action';
				
				//спрятать открытые окна в форме если требуется
				if (hideWins_INT === 1) { App.hideHTMLFormWins(formId_STR); }
				
				//вытащить значения полей формы
				for (let i = 0; i < fieldsIdsSet_ARR.length; i++) {
					
					let cycle_elemId_STR    =   fieldsIdsSet_ARR[i];
					let cycle_fieldVal_ANY	=	App.retHTMLFieldValueById(cycle_elemId_STR, 0);
					formFields_ARR[i]	=	[cycle_elemId_STR, fieldsNamesSet_ARR[i], cycle_fieldVal_ANY];
					
				}
				
				/*
				console.log(formFields_ARR);
				console.log(fieldsIdsSet_ARR);
				console.log(fieldsNamesSet_ARR);
				*/
				POSTBody_OBJ	=	{
					
					'_token'	:	CSRFToken_STR,
					'a'	:	formId_STR,
					'b'	:	buttonId_STR,
					'c'	:	formFields_ARR
					
				};
				
				if (handleFuncName_STR === 'default') { handleFuncName_STR	=	App.AJAX['handleResponse']; }
				
				//console.log(POSTBody_OBJ);
				App.AJAX.sendRequest(requestAddr_STR, POSTBody_OBJ, handleFuncName_STR, formId_STR, buttonId_STR);
				
			}//если проверка состояни кнопки пройдена
			
		},//App.AJAX.onButtonClick
		
		'sendRequest'	:	function (requestAddr_STR, POSTBody_OBJ, handleFuncName_STR, buttonId_STR) {
			
			fetch(
				
				requestAddr_STR,
				{
					'method' : 'POST',
					'body' : JSON.stringify(POSTBody_OBJ),
					'headers'	:	{
						
						'Content-Type'	:	'application/json; charset=utf-8;'
						
					}
					
				}
				
			)
			.then(function (fetchResp_OBJ) { return fetchResp_OBJ.json() })
			.then(function (AJAXResult_OBJ) { handleFuncName_STR(AJAXResult_OBJ, buttonId_STR) });
			
		},//App.AJAX.sendRequest
		
		//обработать ответ сервера на ajax запрос
		'handleResponse'	:	function(AJAXResult_OBJ, formId_STR, buttonId_STR) {
			
			//console.log(AJAXResult_OBJ);
			
			//снять блокировку с кнопки отправки формы
			App.AJAX.unlockButtonById(buttonId_STR);
			
			//получить текст сообщения
			let resultMessage_STR	=	AJAXResult_OBJ[RESULT_DEFS_MESSAGE];
			
			//пустой массив(здесь это объект) с доп данными бесполезен
			if (AJAXResult_OBJ[RESULT_DEFS_ADD_DATA][ADD_DATA_DEFS_IS_EMPTY] == 1) {
				
				if (resultMessage_STR != '') {
					
					alert(resultMessage_STR);
					
				}
				
			} else {
				
				//объект с доп данными не пуст
				
				let paramsAction_STR	=	AJAXResult_OBJ[RESULT_DEFS_ADD_DATA][PARAMS_DEFS_ACTION];
				
				if (paramsAction_STR == PARAMS_ACTION_SHOW_ERROR_IN_FIELD_WIN) {
					
					//получить узел поля в котором произошла ошибка
					let result_ARR	=	App.retElemNodeById(AJAXResult_OBJ[RESULT_DEFS_ADD_DATA][ADD_DATA_DEFS_ERROR_FIELD_ID]);
					
					//если узел найден
					if (result_ARR[0]) {
						
						//получить узел окна для ошибок
						result_ARR	=	App.retFindNodesByClass(result_ARR[1], 'errorHTMLWin', 0);
						
						if (result_ARR[0]) {
							
							let errorHTMLWinNode_OBJ	=	result_ARR[1];
							
							//получить узел текста окна для ошибок
							result_ARR	=	App.retFindNodesByClass(errorHTMLWinNode_OBJ, 'HTMLWinText', 0);
							
							if (result_ARR[0]) {
								
								result_ARR[1].innerHTML	=	AJAXResult_OBJ[RESULT_DEFS_MESSAGE];
								App.showHTMLWinByNode(errorHTMLWinNode_OBJ);
								
							}
							
						}
						
					}
				} else {
				if (paramsAction_STR == PARAMS_ACTION_SUBMIT_FORM_TO_PATH) {
					
					let result_ARR	=	App.retElemNodeById(formId_STR);
					
					if (result_ARR[0]) {
						
						let formNode_OBJ	=	result_ARR[1];
						formNode_OBJ.action	=	AJAXResult_OBJ[RESULT_DEFS_ADD_DATA][PARAMS_DEFS_SUBMIT_PATH];
						formNode_OBJ.submit();
						
					}
					
				}
				}
				
			}
			
		},//App.AJAX.handleResponse
		
	},//App.AJAX
	
};//App