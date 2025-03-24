<?php

namespace App\Auxiliary;

use App\Models;
use App\Models\BookModel;
use App\Auxiliary\RequestHandler;
use App\Models\UserModel;
use App\Models\VisitorModel;

class AuxValid {
	
	private function __construct() {}
	
	public static function retInst() : AuxValid {
		
		static $thisInst_OBJ	=	null;
		
		if (!is_object($thisInst_OBJ)) {
			
			$thisInst_OBJ	=	new self();
			$thisInst_OBJ->regAllFields();
			
		}
		
		return $thisInst_OBJ;
		
	}//retInst
	
	const FIELD_DEFS_NAME	=	'fieldName_STR';
	const FIELD_DEFS_MIN_LEN	=	'fieldMinLen_INT';
	const FIELD_DEFS_MAX_LEN	=	'fieldMaxLen_INT';
	const FIELD_DEFS_MASK	=	'fieldMask_STR';
	const FIELD_DEFS_REGEX	=	'fieldRegex_STR';
	
	public function checkFieldVal(string $fieldName_STR, $fieldVal_STR) : void {
		
		$isFieldDataUp_BOO	=	false;
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$fieldMinLen_INT	=	$fieldMaxLen_INT	=	0; $fieldMask_STR	=	$fieldRegex_STR	=	'';
		
		//имя поля особое поле
		if ($fieldName_STR == 'fieldName') {
			
			$fieldMinLen_INT=	3;
			$fieldMaxLen_INT=	20;
			$fieldMask_STR	=	'[a-z]{1}[a-z0-9_]{1,18}[a-z0-9]{1}';
			$fieldRegex_STR	=	'a-z0-9_';
			
			$isFieldDataUp_BOO	=	true;
			
		} else {
			
			//проверить имя пришедшего поля
			$this->checkFieldVal('fieldName', $fieldName_STR);
			
			//узнать всё ли в порядке с именем поля
			if ($requestHandlerInst_OBJ->retIsSuccess()) {
				
				if (key_exists($fieldName_STR, $this->inherAst_ARR)) {
					
					if (!key_exists($fieldName_STR, $this->fieldsList_ARR)) {
						
						$this->fieldsList_ARR[$fieldName_STR]	=	$this->fieldsList_ARR[$this->inherAst_ARR[$fieldName_STR]];
						
					}
					
				}
				
				//если поле с таким именем зарегано в общем списке
				if (key_exists($fieldName_STR, $this->fieldsList_ARR)) {
					
					$fieldData_ARR	=	$this->fieldsList_ARR[$fieldName_STR];
					
					$fieldMinLen_INT=	$fieldData_ARR[AuxValid::FIELD_DEFS_MIN_LEN];
					$fieldMaxLen_INT=	$fieldData_ARR[AuxValid::FIELD_DEFS_MAX_LEN];
					
					$fieldMask_STR	=	$fieldData_ARR[AuxValid::FIELD_DEFS_MASK];
					$fieldRegex_STR	=	$fieldData_ARR[AuxValid::FIELD_DEFS_REGEX];
					
					//массив поля получен, можно продолжить
					$isFieldDataUp_BOO	=	true;
					
				} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
				
			} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
			
		}
		
		//продолжить если массив поля получен
		if ($isFieldDataUp_BOO) {
			
			$fieldVal_STR	=	(string) $fieldVal_STR;
			
			if (mb_strlen($fieldVal_STR) < $fieldMinLen_INT) {
				
				//значение поля слишком короткое
				$requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 10);
				
			} else {
			if (mb_strlen($fieldVal_STR) > $fieldMaxLen_INT) {
				
				//значение поля слишком длинное
				$requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 11);
				
			} else {
			if (strlen($fieldRegex_STR) > 0 && preg_match('#[^'.$fieldRegex_STR.']#u', $fieldVal_STR) == 1) {
				
				/*
				var_dump($fieldVal_STR);
				var_dump($fieldRegex_STR);
				var_dump(preg_match('#[^'.$fieldRegex_STR.']#', $fieldVal_STR));
				
				$setRepHint_STR	=	preg_replace_callback(
					
					'#([^'.$fieldRegex_STR.'])#',
					function($matches_ARR) { return '['.$matches_ARR[0].']'; },
					$fieldVal_STR
					
				); var_dump($setRepHint_STR);*/
				
				//в значении содержится неразрешённый символ
				$requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 12);
				
			} else {
			if (strlen($fieldMask_STR) > 0 && preg_match('#^'.$fieldMask_STR.'$#u', $fieldVal_STR) == 0) {
				
				//значение не соответствует маске(шаблону)
				$requestHandlerInst_OBJ->setSoftErrByNum(__LINE__, 13);
				
			} else {
				
				$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
				
			}
			}
			}
			}
			
		}//продолжить если массив поля получен
		
	}//checkFieldVal
	
	public array $fieldsList_ARR	=	array();
	
	//
	public array $inherAst_ARR	=	array();
	
	public function regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR) : bool {
		
		$this->fieldsList_ARR[$fieldName_STR]	=	array(
			
			AuxValid::FIELD_DEFS_NAME	=>	$fieldName_STR,
			AuxValid::FIELD_DEFS_MIN_LEN	=>	$fieldMinLen_INT,
			AuxValid::FIELD_DEFS_MAX_LEN	=>	$fieldMaxLen_INT,
			AuxValid::FIELD_DEFS_MASK	=>	$fieldMask_STR,
			AuxValid::FIELD_DEFS_REGEX	=>	$fieldRegex_STR
			
		); return true;
		
	}//regField
	
	public function regAllFields() {
		
		$fieldName_STR	=	'any_id';
		$fieldMinLen_INT=	1;
		$fieldMaxLen_INT=	20;
		$fieldMask_STR	=	'[1-9]{1}[0-9]{0,19}';
		$fieldRegex_STR	=	'0-9';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		$fieldName_STR	=	'md5_hash';
		$fieldMinLen_INT=	32;
		$fieldMaxLen_INT=	32;
		$fieldMask_STR	=	'[a-zA-Z0-9]{32}';
		$fieldRegex_STR	=	'a-zA-Z0-9';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		$fieldName_STR	=	'flag';
		$fieldMinLen_INT=	1;
		$fieldMaxLen_INT=	1;
		$fieldMask_STR	=	'';
		$fieldRegex_STR	=	'01';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		//...
		
		//эти поля унаследуют параметры от типового поля
		$this->inherAst_ARR[BookModel::BOOK_DEFS_ID]	=	'any_id';
		$this->inherAst_ARR[UserModel::USER_DEFS_ID]	=	'any_id';
		$this->inherAst_ARR[VisitorModel::VISITOR_DEFS_ID]	=	'any_id';
		
		$this->inherAst_ARR[BookModel::BOOK_DEFS_TITLE_HASH]	=	'md5_hash';
		$this->inherAst_ARR[BookModel::BOOK_DEFS_AUTHOR_HASH]	=	'md5_hash';
		
		$this->inherAst_ARR[BookModel::BOOK_DEFS_IS_DELETE]	=	'flag';
		
		
		$fieldName_STR	=	BookModel::BOOK_DEFS_AUTHOR_FULL_NAME;
		$fieldMinLen_INT=	3;
		$fieldMaxLen_INT=	50;
		$fieldMask_STR	=	'[a-zA-Zа-яёА-ЯЁ]{1}[a-zA-Zа-яёА-ЯЁ\.\- ]{1,48}[a-zA-Zа-яёА-ЯЁ]{1}';
		$fieldRegex_STR	=	'a-zA-Zа-яёА-ЯЁ\.\- ';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		$fieldName_STR	=	BookModel::BOOK_DEFS_TITLE;
		$fieldMinLen_INT=	2;
		$fieldMaxLen_INT=	50;
		$fieldMask_STR	=	'[0-9a-zA-Zа-яёА-ЯЁ]{1}[0-9a-zA-Zа-яёА-ЯЁ\.\,\- \?]{0,48}[0-9a-zA-Zа-яёА-ЯЁ\?\"]{1}';
		$fieldRegex_STR	=	'0-9a-zA-Zа-яёА-ЯЁ\.\,\- \?';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		$fieldName_STR	=	BookModel::BOOK_DEFS_YEAR;
		$fieldMinLen_INT=	4;
		$fieldMaxLen_INT=	4;
		$fieldMask_STR	=	'[1-2]{1}[0-9]{3}';
		$fieldRegex_STR	=	'0-9';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		
		$fieldName_STR	=	BookModel::BOOK_DEFS_GENRE_TITLE;
		$fieldMinLen_INT=	2;
		$fieldMaxLen_INT=	20;
		$fieldMask_STR	=	'';
		$fieldRegex_STR	=	'0-9a-zA-Zа-яёА-ЯЁ \-';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		$fieldName_STR	=	BookModel::BOOK_DEFS_PAGES_COUNT;
		$fieldMinLen_INT=	1;
		$fieldMaxLen_INT=	5;
		$fieldMask_STR	=	'[1-9]{1}[0-9]{0,4}';
		$fieldRegex_STR	=	'0-9';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		$fieldName_STR	=	BookModel::BOOK_DEFS_COVER;
		$fieldMinLen_INT=	32 + 1 + 3;
		$fieldMaxLen_INT=	32 + 1 + 3;
		$fieldMask_STR	=	'[a-zA-Z0-9]{32}[\.]{1}[a-z]{3}';
		$fieldRegex_STR	=	'a-zA-Z0-9\.';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		// : Users
		
		$fieldName_STR	=	UserModel::USER_DEFS_LOGIN;
		$fieldMinLen_INT=	3;
		$fieldMaxLen_INT=	20;
		$fieldMask_STR	=	'[0-9a-zA-Z]{1}[0-9a-zA-Z\.\_\-]{0,18}[0-9a-zA-Z]{1}';
		$fieldRegex_STR	=	'0-9a-zA-Z\.\_\-';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		$fieldName_STR	=	UserModel::USER_DEFS_PASSW;
		$fieldMinLen_INT=	6;
		$fieldMaxLen_INT=	50;
		$fieldMask_STR	=	'';
		$fieldRegex_STR	=	'';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		// : Visitors
		
		$fieldName_STR	=	VisitorModel::VISITOR_DEFS_SESS;
		$fieldMinLen_INT=	128;
		$fieldMaxLen_INT=	128;
		$fieldMask_STR	=	'';
		$fieldRegex_STR	=	'0-9a-zA-Z';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		
		/*
		$fieldName_STR	=	'';
		$fieldMinLen_INT=	1;
		$fieldMaxLen_INT=	2;
		$fieldMask_STR	=	'';
		$fieldRegex_STR	=	'';
		$this->regField($fieldName_STR, $fieldMinLen_INT, $fieldMaxLen_INT, $fieldMask_STR, $fieldRegex_STR);
		*/
		
	}//regAllFields
	
}//AuxValid

?>