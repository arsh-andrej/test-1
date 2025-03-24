<?php

namespace App\Auxiliary;

class RequestHandler {
	
	private function __construct() {}
	
	public static function retInst() : RequestHandler {
		
		static $thisInst_OBJ	=	null;
		
		if (!is_object($thisInst_OBJ)) {
			
			$thisInst_OBJ	=	new self();
			$thisInst_OBJ->out_fillDefaultProps();
			$thisInst_OBJ->parseAJAXRequestData();
			
		}
		
		return $thisInst_OBJ;
		
	}//retInst
	
	//в качестве ключей messageNum_INT
	public array $errorMessages_ARR	=	array(
		
		'0'	=>	'Системная ошибка',
		'1'	=>	'Неизвестная ошибка',//<- текст ошибки по умолчанию
		
		'10'=>	'Слишком мало символов',
		'11'=>	'Слишком много символов',
		'12'=>	'Содержит неразрешённый символ',
		'13'=>	'Не соответствует формату',
		
		'50'=>	'ID книги не передан или передан с ошибкой',
		'51'=>	'Для этого действия нужно войти',
		'52'=>	'Жанра не существует',
		'53'=>	'Не может быть меньше 1',
		'54'=>	'Книга с таким названием уже существует',
		'55'=>	'Книга не найдена',
		'56'=>	'Этот логин уже кем-то зарегистрирован',
		'57'=>	'Такой логин не зарегистрирован',
		'58'=>	'Логин существует, но пароли не совпадают',
		'59'=>	'Эта книга уже удалена',
		'60'=>	'Удаление выполнено успешно',
		'61'=>	'Эта книга удалена',
		
	);
	
	//номера и сообщения об успехе
	public array $successMessages_ARR	=	array(
		
		'1'	=>	'Выполнено успешно',
		
	);
	
	//была ли простая ошибка
	//например : "Пользователь с таким логином уже зареган"
	const RESULT_DEFS_IS_SOFT_ERR	=	'ab';
	
	//была ли серьёзная ошибка
	//например : "Системная ошибка"
	const RESULT_DEFS_IS_HARD_ERR	=	'aa';
	
	//1 если нет ошибок(два верхних элемента == '0')
	const RESULT_DEFS_IS_SUCCESS	=	'ac';
	
	//текст ошибки или ответа
	//результат работы который увидит пользователь
	//например : "Регистрация успешно завершена"
	//например : "Пользователь с таким логином уже зареган"
	const RESULT_DEFS_MESSAGE	=	'ae';
	
	//любые доп данные для работы приложения
	const RESULT_DEFS_PARAMS	=	'af';
		
		//пуст ли массив с доп данными
		const PARAMS_DEFS_IS_EMPTY	=	'ba';
		
		//ID HTMLField в котором произошла ошибки
		const PARAMS_DEFS_ERROR_FIELD_ID	=	'bb';
		
		//действие которое должен выполнить обработчик в app.js
		const PARAMS_DEFS_ACTION	=	'bc';
			
			//
			const PARAMS_ACTION_SHOW_ERROR_IN_FIELD_WIN	=	'1';
			
			//
			const PARAMS_ACTION_SUBMIT_FORM_TO_PATH	=	'2';
			
		//путь по которому нужно отправить пользователя
		const PARAMS_DEFS_SUBMIT_PATH	=	'bd';
			
	//CSRF токен laravel
	public $inc_laravelCSRFToken_STR	=	'';
	
	//ID HTML формы в поля которой ввели значения текущего ajax-запроса
	public $inc_HTMLFormId_STR	=	'';
	
	//ID HTML кнопки которую нажали чтобы пришёл текущий ajax-запрос
	public $inc_HTMLButtonId_STR	=	'';
	
	
	//fieldName_STR => array(HTMLFieldId_STR, HTMLFieldVal_STR)
	public $inc_HTMLFieldsIdsAndData_ARR	=	array();
	
	
	//получить тек режим запроса
	public function retRequestMode() : String {
		
		$mode_STR	=	(string) ($_GET['mode'] ?? 'get');
		return in_array($mode_STR, array('get', 'ajax')) ? $mode_STR : 'get';
		
	}//retRequestMode
	
	//получить удобный для работы массив пришедших данным через ajax(post)-запрос
	public function parseAJAXRequestData() : void {
		
		if ($this->retRequestMode() != 'ajax') { return; }
		
		$AJAXRequestAst_ARR	=	json_decode(file_get_contents('php://input'), true);
		
		$this->inc_laravelCSRFToken_STR	=	$AJAXRequestAst_ARR['_token'];
		$this->inc_HTMLFormId_STR	=	$AJAXRequestAst_ARR['a'];
		$this->inc_HTMLButtonId_STR	=	$AJAXRequestAst_ARR['b'];
		
		foreach ($AJAXRequestAst_ARR['c'] as $i => $data_ARR) {
			
			$this->inc_HTMLFieldsIdsAndData_ARR[$data_ARR[1]]	=	array($data_ARR[0], $data_ARR[2]);
			
		}
		
		//print_r(file_get_contents('php://input'));
		
	}//parseAJAXRequestData
	
	public function retCheckRequestFieldVal($fieldName_STR, &$fieldVal_ANY){
	    
		$result_BOO	=	false;
		$auxValidInst_OBJ	=	AuxValid::retInst();
		
		$fieldVal_ANY	=	$this->inc_HTMLFieldsIdsAndData_ARR[$fieldName_STR][1];
		$auxValidInst_OBJ->checkFieldVal($fieldName_STR, $fieldVal_ANY);
		$result_BOO	=	$this->retIsSuccess();
		
		if (!$result_BOO) {
			
			$this->setStdErrParams($this->inc_HTMLFieldsIdsAndData_ARR[$fieldName_STR][0]);
			
		}
		
		return $result_BOO;
		
	}//retCheckRequestFieldVal
	
	public function setSoftErrFieldName($fieldName_STR) {
	    
		if (key_exists($fieldName_STR, $this->inc_HTMLFieldsIdsAndData_ARR)) {
			
			$this->setStdErrParams($this->inc_HTMLFieldsIdsAndData_ARR[$fieldName_STR][0]);
			
		}
		
	}//setSoftErrFieldName
	
	public function setStdErrParams($HTMLFieldId_STR) {
    	
		$params_ARR_REF	=	&$this->out_params_ARR;
		
		$params_ARR_REF[self::PARAMS_DEFS_IS_EMPTY]	=	0;
		$params_ARR_REF[self::PARAMS_DEFS_ACTION]	=	self::PARAMS_ACTION_SHOW_ERROR_IN_FIELD_WIN;
		$params_ARR_REF[self::PARAMS_DEFS_ERROR_FIELD_ID]	=	$HTMLFieldId_STR;
		
	}//setStdErrParams
	
	public function setSuccessSubmitParams(string $submitPath_STR) {
		
		$params_ARR_REF	=	&$this->out_params_ARR;
		
		$params_ARR_REF[self::PARAMS_DEFS_IS_EMPTY]	=	0;
		$params_ARR_REF[self::PARAMS_DEFS_ACTION]	=	self::PARAMS_ACTION_SUBMIT_FORM_TO_PATH;
		$params_ARR_REF[self::PARAMS_DEFS_SUBMIT_PATH]	=	$submitPath_STR;
		
		//что-то нужно?
		
	}//setSuccessSubmitParams
	
	private $out_isSoftErr_INT	=	0;
	private $out_isHardErr_INT	=	0;
	private $out_isSucceess_INT	=	0;
	private $out_message_STR	=	'';
	private $out_params_ARR	=	array();
	
	public function out_reset() { $this->out_fillDefaultProps(); }
	public function out_fillDefaultProps() {
		
		$this->out_isSoftErr_INT	=	0;
		$this->out_isHardErr_INT	=	0;
		$this->out_isSucceess_INT	=	0;
		$this->out_message_STR	=	'';
		$this->out_params_ARR	=	array(
			
			self::PARAMS_DEFS_IS_EMPTY	=>	1,
			
		);
		
	}
	
	public function retIsError() { return $this->out_isSoftErr_INT > 0 || $this->out_isHardErr_INT > 0; }
	public function retIsHardErr() { return $this->out_isHardErr_INT > 0; }
	public function retIsSuccess() { return $this->out_isSucceess_INT > 0; }
	
	public function retFormAJAXResult() {
		
		return array(
			
			self::RESULT_DEFS_IS_SOFT_ERR	=>	$this->out_isSoftErr_INT,
			self::RESULT_DEFS_IS_HARD_ERR	=>	$this->out_isHardErr_INT,
			self::RESULT_DEFS_IS_SUCCESS	=>	$this->out_isSucceess_INT,
			self::RESULT_DEFS_MESSAGE	=>	$this->out_message_STR,
			self::RESULT_DEFS_PARAMS	=>	$this->out_params_ARR,
			
		);
		
	}//retFormAJAXResult
	
	public function retFormGETResult() {
		
		if ($this->retIsSuccess()) {
			
			$message_STR	=	$this->retDefaultSuccessMessage();
			return $this->retNormalAnswer($message_STR);
			
		} else {
		if ($this->retIsHardErr()) {
			
			return $this->retHardAnswer($this->out_isHardErr_INT);
			
		} else {
			
			$message_STR	=	$this->retDefaultSoftErrMessage();
			return $this->retSoftAnswer(-1, $message_STR);
			
		}
		}
		
	}//retFormGETResult
	
	public function retErrMessage(int $messageNum_INT = 0) : string {
		
		$messageNum_INT	=	key_exists($messageNum_INT, $this->errorMessages_ARR) ? $messageNum_INT : 1;
		return $this->errorMessages_ARR[$messageNum_INT];
		
	}//retErrMessage
	
	public function _rhem() { return $this->retHardErrMessage(); }
	public function retHardErrMessage() { return $this->retErrMessage(0); }
	
	public function retDefaultSoftErrMessage() : string { return $this->retErrMessage(1); }
	
	public function _ssewm(int $lineNum_INT, string $message_STR = 'default') { $this->setSoftErrWithMessage($lineNum_INT, $message_STR); }
	public function setSoftErrWithMessage(int $lineNum_INT, string $message_STR = 'default') {
		
		if (mb_strlen($message_STR) < 2 || $message_STR == 'default') {
			
			$message_STR	=	$this->retDefaultSoftErrMessage();
			
		}
		
		$this->out_isSoftErr_INT	=	$lineNum_INT;
		$this->out_isHardErr_INT	=	0;
		$this->out_isSucceess_INT	=	0;
		$this->out_message_STR	=	$message_STR;
		
	}//setSoftErrWithMessage
	
	public function _ssebn(int $lineNum_INT, int $messageNum_INT) { $this->setSoftErrByNum($lineNum_INT, $messageNum_INT); }
	public function setSoftErrByNum(int $lineNum_INT, int $messageNum_INT) {
		
		if ($messageNum_INT < 2 || !key_exists($messageNum_INT, $this->errorMessages_ARR)) {
			
			$messageNum_INT	=	1;
			
		}
		
		$this->out_isSoftErr_INT	=	$lineNum_INT;
		$this->out_isHardErr_INT	=	0;
		$this->out_isSucceess_INT	=	0;
		$this->out_message_STR	=	$this->errorMessages_ARR[$messageNum_INT];
		
	}//setSoftErrByNum
	
	public function setHardErr(int $lineNum_INT) {
		
		$this->out_isSoftErr_INT	=	0;
		$this->out_isHardErr_INT	=	$lineNum_INT;
		$this->out_isSucceess_INT	=	0;
		$this->out_message_STR	=	$this->errorMessages_ARR[0];
		
	}//setHardErr
	
	public function retSuccessMessage(int $messageNum_INT = 0) : string {
		
		$messageNum_INT	=	key_exists($messageNum_INT, $this->successMessages_ARR) ? $messageNum_INT : 1;
		return $this->successMessages_ARR[$messageNum_INT];
		
	}//retSuccessMessage
	
	public function _rdsm() { return $this->retDefaultSuccessMessage(); }
	public function retDefaultSuccessMessage() { return $this->retSuccessMessage(1); }
	
	public function _sswm(int $lineNum_INT, string $message_STR = 'default') { $this->setSuccessWithMessage($lineNum_INT, $message_STR); }
	public function setSuccessWithMessage(int $lineNum_INT, string $message_STR = 'default') {
		
		if (mb_strlen($message_STR) < 2 || $message_STR == 'default') {
			
			$message_STR	=	$this->retDefaultSuccessMessage();
			
		}
		
		$this->out_isSoftErr_INT	=	0;
		$this->out_isHardErr_INT	=	0;
		$this->out_isSucceess_INT	=	$lineNum_INT;
		$this->out_message_STR	=	$message_STR;
		
	}//setSuccessWithMessage
	
	public function _ssbn(int $lineNum_INT, int $messageNum_INT) { $this->setSuccessByNum($lineNum_INT, $messageNum_INT); }
	public function setSuccessByNum(int $lineNum_INT, int $messageNum_INT) {
		
		if ($messageNum_INT < 1 || !key_exists($messageNum_INT, $this->successMessages_ARR)) {
			
			$messageNum_INT	=	1;
			
		}
		
		$this->out_isSoftErr_INT	=	0;
		$this->out_isHardErr_INT	=	0;
		$this->out_isSucceess_INT	=	$lineNum_INT;
		$this->out_message_STR	=	$this->retSuccessMessage($messageNum_INT);
		
	}//setSuccessByNum
	
	public function setSilentSuccess(int $lineNum_INT) { $this->setSuccessWithMessage($lineNum_INT); }
	
/*
-------------------
Сообщения
-------------------
*/
    
	//например "Действие выполнено успешно"
	//нет ошибок
	const MESSAGE_TYPE_NORMAL	=	'normal';
	
	//например "Эта книга недоступна"
	const MESSAGE_TYPE_SOFT_ERROR	=	'soft';
	
	//например "Системная ошибка"
	const MESSAGE_TYPE_HARD_ERROR	=	'hard';
	
	//заворачивает текст сообщения в шаблон
	public function retHTMLMessage($message_STR, $messageType_STR = self::MESSAGE_TYPE_NORMAL) : string {
		
		return view('aux_errors_message', ['message_STR' => $message_STR, 'messageType_STR' => $messageType_STR]);
		
	}//retHTMLMessage
	
	public function retSoftAnswer(int $messageNum_INT = -1, string $message_STR = '') : string {
		
		$isErrorNumPass_BOO	=	$messageNum_INT > 0;
		$isMessagePass_BOO	=	strlen($message_STR) > 0;
		
		if ($isErrorNumPass_BOO) {
			
			$message_STR	=	$this->retErrMessage($messageNum_INT);
			
		} else {
		if (!$isMessagePass_BOO) {
			
			$message_STR	=	$this->retDefaultSoftErrMessage();
			
		} }
		
		return $this->retHTMLMessage($message_STR, self::MESSAGE_TYPE_SOFT_ERROR);
		
	}//retSoftAnswer
	
	public function retHardAnswer(int $lineNum_INT = -1) : string {
		
		$addLineNum_STR	=	$lineNum_INT > 0 ? ' ('.$lineNum_INT.')' : '';
		
		return $this->retHTMLMessage(
			$this->retErrMessage(0).$addLineNum_STR,
			self::MESSAGE_TYPE_HARD_ERROR
		);
		
	}//retHardAnswer
	
	public function retNormalAnswer(string $message_STR) : string {
		
		return $this->retHTMLMessage($message_STR, self::MESSAGE_TYPE_NORMAL);
		
	}//retNormalAnswer
    
    
    
}//RequestHandler

?>