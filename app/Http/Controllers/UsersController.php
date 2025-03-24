<?php

namespace App\Http\Controllers;

use App\Auxiliary\AuxMain;
use App\Auxiliary\RequestHandler;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\UserModel;
use App\Models\VisitorModel;

class UsersController extends Controller {
	
	public function main(Request $request_OBJ) : string|View {
		
		$requestHandler_OBJ	=	RequestHandler::retInst();
		$visitorModelInst_OBJ	=	VisitorModel::retInst();
		
		//то что будет "контентом" на странице(внутри #root_content_out шаблона app.blade.php)
		//актуально при requestMode_STR == 'get'
		$rootContent_HTML	=	'';
		
		//узнать режим тек запроса, get - простой переход по ссылке
		//ajax - POST запрос с данными(например формы) с обязательным ответом в ajax формете
		$requestMode_STR	=	$requestHandler_OBJ->retRequestMode();
		
		//мд тек пользователя
		$visitorUserData_ARR	=	array();
		
		$isVisitorLogIn_BOO	=	false;
		$visitorUserId_INT	=	0;
		
		$visitorModelInst_OBJ->getVisitUserData($isVisitorLogIn_BOO, $visitorUserData_ARR, true);
		
		if ($isVisitorLogIn_BOO) {
			
			$visitorUserId_INT	=	$visitorUserData_ARR[UserModel::USER_DEFS_ID];
			
		}
		
		//...
		
		//здесь действие
		//log-in, log-out, reg-user
		$pathElem_2	=	$request_OBJ->segment(2);
		$pathElem_2	=	$pathElem_2 == '' ? 'log-in' : $pathElem_2;
		
		//...
		
		$requestHandler_OBJ->out_reset();
		
		if ($requestMode_STR == 'get') {
			
			if ($isVisitorLogIn_BOO) {
				
				//чел залогинен
				if ($pathElem_2 == 'log-out') {
					
					$visitorModelInst_OBJ->logCurrentVisitorOut();
					$rootContent_HTML	=	$requestHandler_OBJ->retFormGETResult();
					
				}
				
			} else {//чел не залогинен
				
				if ($pathElem_2 == 'reg-user') {
					
					//показать форму регистрации
					$rootContent_HTML	=	$this->retShowRegUserForm();
					
				} else {
					
					//показать форму входа
					$rootContent_HTML	=	$this->retShowLogInForm();
					
				}
				
			}
			
		}//get
		
		if ($requestMode_STR == 'ajax') {
			
			if (!$isVisitorLogIn_BOO) {
				
				if ($pathElem_2 == 'reg-user') {
					
					$this->handleRegUserForm();
					
				} else {
				if ($pathElem_2 == 'log-in') {
					
					$this->handleLogInForm();
					
				}
				}
				
			} else { $requestHandler_OBJ->setSoftErrByNum(__LINE__, 51); }
			
		}//ajax
		
		if ($requestMode_STR == 'ajax') {
			
			return json_encode($requestHandler_OBJ->retFormAJAXResult());
			
		} else {
			
			return view('app',
				[
					'rootContent_HTML'	=>	$rootContent_HTML,
					'isVisitorLogIn_INT'=>	((int) $isVisitorLogIn_BOO)
					
				]
			);
			
		}
		
	}//main
	
	public function handleRegUserForm() : void {
		
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		$userModelInst_OBJ	=	UserModel::retInst();
		$visitorModelInst_OBJ	=	VisitorModel::retInst();
		
		$currentFieldName_STR	=	UserModel::USER_DEFS_LOGIN; $currentFieldVal_ANY	=	'';
		$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
		$userLogin_STR	=	$currentFieldVal_ANY;
		
		if ($isPrevStepPass_BOO) {
			
			//логин проверку прошёл, нужно узнать НЕ зареган ли такой логин
			$isUserFound_BOO	=	false; $userData_ARR	=	array();
			$userModelInst_OBJ->unsafe_getUserDataByLogin($isUserFound_BOO, $userData_ARR, $userLogin_STR, true);
			
			if ($requestHandlerInst_OBJ->retIsSuccess()) {
				
				if (!$isUserFound_BOO) {
					
					//проверить пароль
					$currentFieldName_STR	=	UserModel::USER_DEFS_PASSW; $currentFieldVal_ANY	=	'';
					$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
					$userPassw_STR	=	$currentFieldVal_ANY;
					
					if ($isPrevStepPass_BOO) {
						
						$newUserId_INT	=	0;
						
						//можно зарегать пользователя
						$userModelInst_OBJ->unsafe_getRegUser($newUserId_INT, $userLogin_STR, $userPassw_STR);
						
						if ($requestHandlerInst_OBJ->retIsSuccess()) {
							
							//пользователь зареган успешно, можно автоматом залогинить его
							$visitorModelInst_OBJ->logCurrentVisitorIn($newUserId_INT);
							
							if ($requestHandlerInst_OBJ->retIsSuccess()) {
								
								$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
								$requestHandlerInst_OBJ->setSuccessSubmitParams('/books');
								
							}
							
						}
						
					}
					
				} else { $requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 56); }
				
			}
		}
		
	}//handleRegUserForm
	
	public function retShowRegUserForm() : string {
		
		$auxMainInst_OBJ	=	AuxMain::retInst();
		
		//ID формы, нужно для работы сборщика значений полей
		$formId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
		
		//адрес обработчика формы
		$requestAddr_STR	=	'/users/reg-user?mode=ajax';
		
		//в наборе сгенерированные ID полей
		$fieldsIdsSet_ARR	=	array();
		
		//в наборе имена полей в порядке как в $fieldsIdsSet_ARR
		$fieldsNamesSet_ARR	=	array(
			
			UserModel::USER_DEFS_LOGIN,
			UserModel::USER_DEFS_PASSW
			
		);
		
		$view	=	view('users_reg_user_form', [
			
			UserModel::USER_DEFS_LOGIN	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Логин',
					'HTMLFieldDescr_HTML'	=>	'Английские буквы, цифры, точка и подчёркивание.<br>Начинаться и заканчиваться должен только на цифру или букву.',
					
				]
				
			),
			
			UserModel::USER_DEFS_PASSW	=>	view('app_std_passw_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Пароль',
					'HTMLFieldDescr_HTML'	=>	'6-50 любых символов',
					
				]
				
			),
			
			'formId_STR'	=>	$formId_STR,
			'fieldsIdsSet_ARR'	=>	json_encode($fieldsIdsSet_ARR),
			'fieldsNamesSet_ARR'=>	json_encode($fieldsNamesSet_ARR),
			'requestAddr_STR'	=>	$requestAddr_STR,
			'AJAXButtonId_STR'	=>	$auxMainInst_OBJ->retGenHTMLElemId(),
			
		]);
		
		return $auxMainInst_OBJ->retBuildHTMLPageTitle('Регистрация').$view;
		
	}//retShowRegUserForm
	
	public function retShowLogInForm() : string {
	    
		$auxMainInst_OBJ	=	AuxMain::retInst();
		
		//ID формы, нужно для работы сборщика значений полей
		$formId_STR	=	$auxMainInst_OBJ->retGenHTMLElemId();
		
		//адрес обработчика формы
		$requestAddr_STR	=	'/users/log-in?mode=ajax';
		
		//в наборе сгенерированные ID полей
		$fieldsIdsSet_ARR	=	array();
		
		//в наборе имена полей в порядке как в $fieldsIdsSet_ARR
		$fieldsNamesSet_ARR	=	array(
			
			UserModel::USER_DEFS_LOGIN,
			UserModel::USER_DEFS_PASSW
			
		);
		
		$view	=	view('users_log_in_form', [
			
			UserModel::USER_DEFS_LOGIN	=>	view('app_std_string_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Логин',
					'HTMLFieldDescr_HTML'	=>	'Английские буквы, цифры, точка и подчёркивание.<br>Начинаться и заканчиваться должен только на цифру или букву.',
					
				]
				
			),
			
			UserModel::USER_DEFS_PASSW	=>	view('app_std_passw_field',
				
				[
					'HTMLFieldId_STR'	=>	$fieldsIdsSet_ARR[]	=	$auxMainInst_OBJ->retGenHTMLElemId(),
					'HTMLFieldTitle_STR'	=>	'Пароль',
					'HTMLFieldDescr_HTML'	=>	'6-50 любых символов',
					
				]
				
			),
			
			'formId_STR'	=>	$formId_STR,
			'fieldsIdsSet_ARR'	=>	json_encode($fieldsIdsSet_ARR),
			'fieldsNamesSet_ARR'=>	json_encode($fieldsNamesSet_ARR),
			'requestAddr_STR'	=>	$requestAddr_STR,
			'AJAXButtonId_STR'	=>	$auxMainInst_OBJ->retGenHTMLElemId(),
			
		]);
		
		return $auxMainInst_OBJ->retBuildHTMLPageTitle('Вход').$view;
		
	}//retShowLogInForm
	
	public function handleLogInForm() : void {
		
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		$userModelInst_OBJ	=	UserModel::retInst();
		$visitorModelInst_OBJ	=	VisitorModel::retInst();
		
		$currentFieldName_STR	=	UserModel::USER_DEFS_LOGIN; $currentFieldVal_ANY	=	'';
		$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
		$userLogin_STR	=	$currentFieldVal_ANY;
		
		if ($isPrevStepPass_BOO) {
			
			//логин проверку прошёл, нужно узнать зареган ли такой логин
			$isUserFound_BOO	=	false; $userData_ARR	=	array();
			$userModelInst_OBJ->unsafe_getUserDataByLogin($isUserFound_BOO, $userData_ARR, $userLogin_STR);
			
			if ($requestHandlerInst_OBJ->retIsSuccess()) {
				
				if ($isUserFound_BOO) {
					
					//проверить пароль
					$currentFieldName_STR	=	UserModel::USER_DEFS_PASSW; $currentFieldVal_ANY	=	'';
					$isPrevStepPass_BOO	=	$requestHandlerInst_OBJ->retCheckRequestFieldVal($currentFieldName_STR, $currentFieldVal_ANY);
					$userPassw_STR	=	$currentFieldVal_ANY;
					
					if ($isPrevStepPass_BOO) {
						
						$inDB_passHash_STR	=	$userData_ARR[UserModel::USER_DEFS_PASSH];
						
						$inc_passhHash_STR	=	$userModelInst_OBJ->retHashUserPassw($userPassw_STR, $userData_ARR[UserModel::USER_DEFS_SALT]);
						
						if ($inDB_passHash_STR === $inc_passhHash_STR) {
							
							$visitorModelInst_OBJ->logCurrentVisitorIn($userData_ARR[UserModel::USER_DEFS_ID]);
							
							if ($requestHandlerInst_OBJ->retIsSuccess()) {
								
								$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
								$requestHandlerInst_OBJ->setSuccessSubmitParams('/books');
								
							}
							
						} else { $requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 58); }
						
					}
					
				} else { $requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 57); }
				
			}
		}
		
	}//handleLogInForm
	
}//UsersController
