<?php

namespace App\Models;

use App\Auxiliary\AuxDB;
use App\Auxiliary\AuxMain;
use App\Auxiliary\AuxValid;
use App\Auxiliary\RequestHandler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;

class UserModel extends Model {
	
	private function __construct() {}
	
	public static function retInst() : UserModel {
		
		static $thisInst_OBJ	=	null;
		
		if (!is_object($thisInst_OBJ)) {
			
			$thisInst_OBJ	=	new self();
			
		} return $thisInst_OBJ;
		
	}//retInst
	
	const USERS_LIST	=	'users_list';
	
	const USER_DEFS_ID	=	'user_id';
	const USER_DEFS_LOGIN	=	'user_login';
	const USER_DEFS_PASSH	=	'user_passh';
	const USER_DEFS_PASSW	=	'user_passw';//НЕ записывается в бд
	const USER_DEFS_SALT	=	'user_salt';
	const USER_DEFS_CREATE_DTM	=	'create_datetime';
	const USER_DEFS_LOG_IN_DTM	=	'log_in_datetime';
    
	//userId_INT уже должен быть проверен!
	public function unsafe_getUserDataById(&$isFound_BOO_REF, &$userData_ARR_REF, $userId_INT, $idAndLoginOnly_BOO = false) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$DBInst_OBJ	=	$auxDBInst_OBJ->DBInst_OBJ;
		
		$SQLQuery_STR	=	'SELECT * FROM '.self::USERS_LIST.' WHERE '.self::USER_DEFS_ID.' = '.$userId_INT.' LIMIT 1';
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		$isFound_BOO_REF	=	$SQLQuery_OBJ->rowCount() > 0;
		
		if ($SQLQuery_OBJ->errorCode() == '00000' && $isFound_BOO_REF) {
			
			$userData_ARR_REF	=	$SQLQuery_OBJ->fetch(PDO::FETCH_ASSOC);
			
		} $SQLQuery_OBJ->closeCursor();
		
	}//unsafe_getUserDataById
	
	//userLogin_STR уже должен быть проверен!
	public function unsafe_getUserDataByLogin(&$isFound_BOO_REF, &$userData_ARR_REF, $userLogin_STR, $idOnly_BOO = false) {
		
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$DBInst_OBJ	=	$auxDBInst_OBJ->DBInst_OBJ;
		
		$SQLQuery_STR	=	'SELECT * FROM '.self::USERS_LIST.' WHERE '.self::USER_DEFS_LOGIN.' = \''.$userLogin_STR.'\' LIMIT 1';
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		$isFound_BOO_REF	=	$SQLQuery_OBJ->rowCount() > 0;
		
		if ($SQLQuery_OBJ->errorCode() == '00000') {
			
			$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
			
			if ($isFound_BOO_REF) { $userData_ARR_REF	=	$SQLQuery_OBJ->fetch(PDO::FETCH_ASSOC); }
			
		} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
		
		$SQLQuery_OBJ->closeCursor();
		
	}//unsafe_getUserDataByLogin
	
	public function retHashUserPassw($userPassw_STR, $userSalt_STR) {
		
		return substr(crypt($userPassw_STR, '$6$rounds=5000$IPjpOaU$0ACEYGg/aKCY3'.$userSalt_STR.'v8O8AfyiO7CTfZQ8$'), 32);
		
	}
	
	public function unsafe_getRegUser(&$newUserId_INT_REF, $userLogin_STR, $userPassw_STR) {
		
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		$auxDBInst_OBJ	=	AuxDB::retInst();
		
		$userSalt_STR	=
			md5(microtime().$_SERVER['REMOTE_ADDR']).
			md5(bin2hex(random_bytes(32)));
		
		$colsAndValsAst_ARR	=	array(
			
			self::USER_DEFS_LOGIN	=>	array($userLogin_STR, true),
			self::USER_DEFS_PASSH	=>	array($this->retHashUserPassw($userPassw_STR, $userSalt_STR), true),
			self::USER_DEFS_SALT	=>	array($userSalt_STR, true),
			self::USER_DEFS_CREATE_DTM	=>	array('NOW()', false),
			
		);
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	$auxDBInst_OBJ->retBuildSQLInsert(self::USERS_LIST, $colsAndValsAst_ARR);
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		if ($SQLQuery_OBJ->errorCode() == '00000' && $SQLQuery_OBJ->rowCount() == 1) {
			
			$newUserId_INT_REF	=	$DBInst_OBJ->lastInsertId();
			$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
			
		} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
		
	}//unsafe_getRegUser
	
	
	
	
	
	
	
	
	
	
	
	
	
}//UserModel