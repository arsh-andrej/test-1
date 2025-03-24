<?php

namespace App\Models;

use App\Auxiliary\AuxDB;
use App\Auxiliary\AuxMain;
use App\Auxiliary\AuxValid;
use App\Auxiliary\RequestHandler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;

class VisitorModel extends Model {
	
	private function __construct() {}
	
	public static function retInst() : VisitorModel {
		
		static $thisInst_OBJ	=	null;
		
		if (!is_object($thisInst_OBJ)) {
			
			$thisInst_OBJ	=	new self();
			
		} return $thisInst_OBJ;
		
	}//retInst
	
	public static int $currentVisitorId_INT	=	0;
	
	const VISITORS_LIST	=	'visitors_list';
	
	const VISITOR_DEFS_ID	=	'visitor_id';
	const VISITOR_DEFS_SESS	=	'visitor_sess';
	const VISITOR_DEFS_USER_ID	=	'user_id';
	const VISITOR_DEFS_SALT		=	'visitor_salt';
	const VISITOR_DEFS_CREATE_DTM	=	'create_dtm';
	
	/*но здесь timestamp*/
	const VISITOR_DEFS_UPDATE_DTM	=	'update_dtm';
	
	public function getVisitUserData(&$isLogIn_BOO_REF, &$userData_ARR_REF, $idAndLoginOnly_BOO = false) {
		
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInt_OBJ	=	RequestHandler::retInst();
		$userModelInst_OBJ	=	UserModel::retInst();
		
		$isLogIn_BOO_REF	=	false;
		$userData_ARR_REF	=	array();
		
		$visitorSess_STR	=	'';
		$isSessCorrect_BOO	=	false;
		$regNewVisitor_BOO	=	false;
		
		if (key_exists(self::VISITOR_DEFS_SESS, $_COOKIE)) {
			
			$visitorSess_STR	=	$_COOKIE[self::VISITOR_DEFS_SESS];
			
			if ($visitorSess_STR == '') {
				
				//ключ есть, значения нет - что делать?
				//зарегать нового посетителя
				$regNewVisitor_BOO	=	true;
				
			} else {
			if (strlen($visitorSess_STR) == 128) {
				
				//длина верная, значит то что передали возможно является сессией
				//проверить сессию
				$auxValidInst_OBJ->checkFieldVal(self::VISITOR_DEFS_SESS, $visitorSess_STR);
				
				if ($requestHandlerInt_OBJ->retIsSuccess()) {
					
					//сессия прошла проверку, такую можно пихать в SQL запрос
					$isSessCorrect_BOO	=	true;
					
				} else {
					
					//сессия не прошла проверку, в ней есть левые символы, а значит кто-то что-то сюда напихал
					$isSessCorrect_BOO	=	false;
					
					var_dump(__FUNCTION__.' lol?');
					
				}
				
			} }
			
		} else {
			
			//значит посетитель почистил куки или никогда здесь не был
			//зарегать нового посетителя
			$regNewVisitor_BOO	=	true;
			
		}
		
		//если требуется зарегать нового посетителя
		if ($regNewVisitor_BOO) {
			
			$visitorId_INT	=	0;
			$setNewSess_STR	=	'';
			
			if ($this->getRegVisitor($visitorId_INT, $setNewSess_STR)) {
				
				self::$currentVisitorId_INT	=	$visitorId_INT;
				
				//успешно зарегали нового посетителя, нужно запихать ему в куки сессию
				setcookie(self::VISITOR_DEFS_SESS, $setNewSess_STR, '2147483000', '/', 'test-1.test');
				
			}
			
		}//если требуется зарегать нового посетителя
		
		//если требуется проверить есть ли такая сессия в базе
		if ($isSessCorrect_BOO) {
			
			$isVisitorFound_BOO	=	false; $visitorData_ARR	=	array();
			
			$this->unsafe_getVisitorDataBySess($isVisitorFound_BOO, $visitorData_ARR, $visitorSess_STR);
			
			if ($isVisitorFound_BOO) {
				
				//есть посетитель с такой сессией
				$visitorUserId_INT	=	$visitorData_ARR[self::VISITOR_DEFS_USER_ID];
				$isUpdatePass_BOO	=	true;
				self::$currentVisitorId_INT	=	$visitorData_ARR[self::VISITOR_DEFS_ID];
				
				//сессия в базе обновляется раз в 30 секунд
				if (time() - $visitorData_ARR[self::VISITOR_DEFS_UPDATE_DTM] >= 30) {
					
					//обновить сессию тек посетителя
					$isUpdatePass_BOO	=	$this->unsafe_retUpdateVisitorById($visitorData_ARR[self::VISITOR_DEFS_ID]);
					
				}
				
				if ($isUpdatePass_BOO) {
					
					//если чел залогинен
					if ($visitorUserId_INT > 0) {
						
						$isUserFound_BOO	=	false;
						
						//получить мд пользователя
						$userModelInst_OBJ->unsafe_getUserDataById($isUserFound_BOO, $userData_ARR_REF, $visitorUserId_INT, $idAndLoginOnly_BOO);
						
						if ($isUserFound_BOO) {
							
							//мд пользователя получен, значит ID пользователя указан и чел залогинен
							$isLogIn_BOO_REF	=	true;
							
						}
						
					}//чел не вошёл
					
				}
				
			}//а что делать иначе?
			
		}//если требуется проверить есть ли такая сессия в базе
		
	}//getVisitUserData
	
	public function getRegVisitor(&$visitorId_INT_REF, &$newSess_STR_REF) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		
		$newSess_STR_REF	=	'';
		$visitorSalt_STR	=
			md5(microtime().$_SERVER['REMOTE_ADDR']).
			md5(bin2hex(random_bytes(32)));
		
		$newSess_STR_REF	=
			md5(microtime().$visitorSalt_STR.bin2hex(random_bytes(32))).
			md5(bin2hex(random_bytes(32)).$visitorSalt_STR).
			md5($visitorSalt_STR.bin2hex(random_bytes(32))).
			md5(bin2hex(random_bytes(32)).$visitorSalt_STR.bin2hex(random_bytes(32)));
		
		
		$colsAndValsAst_ARR	=	array(
			
			self::VISITOR_DEFS_SESS	=>	array($newSess_STR_REF, true),
			self::VISITOR_DEFS_SALT	=>	array($visitorSalt_STR, true),
			self::VISITOR_DEFS_CREATE_DTM	=>	array('NOW()', false),
			self::VISITOR_DEFS_UPDATE_DTM	=>	array('NOW()', false),
			
		);
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	$auxDBInst_OBJ->retBuildSQLInsert(self::VISITORS_LIST, $colsAndValsAst_ARR);
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		if ($SQLQuery_OBJ->errorCode() == '00000' && $SQLQuery_OBJ->rowCount() == 1) {
			
			$visitorId_INT_REF	=	$SQLQuery_OBJ->lastInsertId();
			
			//успешно записано
			return true;
			
		} else { return false; }
		
	}//getRegVisitor
	
	//visitorSess_STR должна быть уже проверена!
	public function unsafe_getVisitorDataBySess(&$isFound_BOO_REF, &$visitorData_ARR_REF, $visitorSess_STR) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	'SELECT '.
			self::VISITOR_DEFS_ID.', '.
			self::VISITOR_DEFS_USER_ID.', '.
			'UNIX_TIMESTAMP('.self::VISITOR_DEFS_UPDATE_DTM.')'.
			' FROM '.self::VISITORS_LIST.' WHERE '.self::VISITOR_DEFS_SESS.' = \''.$visitorSess_STR.'\' LIMIT 1';
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		$isFound_BOO_REF	=	$SQLQuery_OBJ->rowCount() > 0;
		
		if ($SQLQuery_OBJ->errorCode() == '00000' && $isFound_BOO_REF) {
			
			$visitorData_ARR_REF	=	$SQLQuery_OBJ->fetch(PDO::FETCH_ASSOC);
			$visitorData_ARR_REF[self::VISITOR_DEFS_UPDATE_DTM]	=
				$visitorData_ARR_REF['UNIX_TIMESTAMP('.self::VISITOR_DEFS_UPDATE_DTM.')'];
			
		} $SQLQuery_OBJ->closeCursor();
		
	}//unsafe_getVisitorDataBySess
	
	//visitorId_INT должен быть уже проверен!
	public function unsafe_retUpdateVisitorById($visitorId_INT) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$auxValidInst_OBJ	=	AuxValid::retInst();
		
		$change_ARR	=	array(
			
			self::VISITOR_DEFS_UPDATE_DTM	=>	array('NOW()', false)
			
		);
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	$auxDBInst_OBJ->retBuildSQLUpdate(self::VISITORS_LIST, $change_ARR);
		$SQLQuery_STR	.=	' WHERE '.self::VISITOR_DEFS_ID.' = '.$visitorId_INT;
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		$result_BOO	=	$SQLQuery_OBJ->errorCode() == '00000' && $SQLQuery_OBJ->rowCount() == 1;
		$SQLQuery_OBJ->closeCursor();
		
		return $result_BOO;
		
	}//unsafe_retUpdateVisitorById
	
	public function logCurrentVisitorIn($userId_INT) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$auxValidInst_OBJ->checkFieldVal(self::VISITOR_DEFS_USER_ID, $userId_INT);
		
		if ($requestHandlerInst_OBJ->retIsSuccess()) {
			
			$change_ARR	=	array(
				
				self::VISITOR_DEFS_USER_ID	=>	array($userId_INT, false)
				
			);
			
			$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
			$SQLQuery_STR	=	$auxDBInst_OBJ->retBuildSQLUpdate(self::VISITORS_LIST, $change_ARR);
			$SQLQuery_STR	.=	' WHERE '.self::VISITOR_DEFS_ID.' = '.self::$currentVisitorId_INT;
			$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
			$SQLQuery_OBJ->execute();
			
			if ($SQLQuery_OBJ->errorCode() == '00000' && $SQLQuery_OBJ->rowCount() == 1) {
				
				$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
				
			} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
			
			$SQLQuery_OBJ->closeCursor();
			
		}
		
	}//logCurrentVisitorIn
	
	public function logCurrentVisitorOut() {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$change_ARR	=	array(
			
			self::VISITOR_DEFS_USER_ID	=>	array(0, false)
			
		);
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	$auxDBInst_OBJ->retBuildSQLUpdate(self::VISITORS_LIST, $change_ARR);
		$SQLQuery_STR	.=	' WHERE '.self::VISITOR_DEFS_ID.' = '.self::$currentVisitorId_INT;
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		if ($SQLQuery_OBJ->errorCode() == '00000' && $SQLQuery_OBJ->rowCount() == 1) {
			
			$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
			
		} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
		
		$SQLQuery_OBJ->closeCursor();
		
	}//logCurrentVisitorOut
	
	
	
	
	
	
}//VisitorModel