<?php

namespace App\Models;

use App\Auxiliary\AuxDB;
use App\Auxiliary\AuxValid;
use App\Auxiliary\RequestHandler;

use Illuminate\Database\Eloquent\Model;
use PDO;

class BookModel extends Model {
	
	private function __construct() {}
	
	public static function retInst() : BookModel {
		
		static $thisInst_OBJ	=	null;
		
		if (!is_object($thisInst_OBJ)) {
			
			$thisInst_OBJ	=	new self();
			
		} return $thisInst_OBJ;
		
	}//retInst
	
	const BOOKS_LIST	=	'books_list';
	
	const BOOK_DEFS_ID	=	'book_id';
	const BOOK_DEFS_AUTHOR_FULL_NAME	=	'author_full_name';//
	const BOOK_DEFS_TITLE	=	'book_title';
	const BOOK_DEFS_YEAR	=	'book_year';//
	const BOOK_DEFS_GENRE_TITLE	=	'genre_title';//
	const BOOK_DEFS_COVER	=	'book_cover';
	const BOOK_DEFS_PAGES_COUNT	=	'book_pages_count';
	const BOOK_DEFS_TITLE_HASH	=	'book_title_hash';
	const BOOK_DEFS_AUTHOR_HASH	=	'book_author_hash';
	const BOOK_DEFS_IS_DELETE	=	'is_delete';
	
	public function getCreateBook(
		
		//ID только что сделанной записи
		&$newBookId_INT_REF,
		
		$bookAuthorFullName_STR,
		$bookTitle_STR,
		$bookYear_STR,
		$genreTitle_STR,
		$bookPagesCount_STR
		
	) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$requestHandlerInt_OBJ	=	RequestHandler::retInst();
		
		$bookTitle_STR	=	preg_replace('#[ ]{2,}#', '', $bookTitle_STR);
		$bookAuthorFullName_STR	=	preg_replace('#[ ]{2,}#', ' ', $bookAuthorFullName_STR);
		
		$colsAndValsAst_ARR	=	array(
				
			self::BOOK_DEFS_AUTHOR_FULL_NAME	=>	array($bookAuthorFullName_STR, true),
			self::BOOK_DEFS_TITLE	=>	array($bookTitle_STR, true),
			self::BOOK_DEFS_YEAR	=>	array((int) $bookYear_STR, false),
			self::BOOK_DEFS_GENRE_TITLE	=>	array($genreTitle_STR, true),
			self::BOOK_DEFS_COVER	=>	array('default', true),
			self::BOOK_DEFS_PAGES_COUNT	=>	array($bookPagesCount_STR, true),
			self::BOOK_DEFS_TITLE_HASH	=>	array(md5($bookTitle_STR), true),
			self::BOOK_DEFS_AUTHOR_HASH	=>	array(md5(strtolower($bookAuthorFullName_STR)), true),
			
		);
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	$auxDBInst_OBJ->retBuildSQLInsert(self::BOOKS_LIST, $colsAndValsAst_ARR);
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		//var_dump($SQLQuery_STR);
		
		if ($SQLQuery_OBJ->errorCode() == '00000' && $SQLQuery_OBJ->rowCount() == 1) {
			
			//успешно записано
			//получить ID только что сделанной записи
			$newBookId_INT_REF	=	$DBInst_OBJ->lastInsertId();
			$requestHandlerInt_OBJ->setSilentSuccess(__LINE__);
			
		} else { $requestHandlerInt_OBJ->setHardErr(__LINE__); }
		
		$SQLQuery_OBJ->closeCursor();
		
	}//getCreateBook
	
	public function deleteBookById($bookId_INT) {
	    
		$change_ARR	=	array(
			
			self::BOOK_DEFS_IS_DELETE	=>	array(1, false)
			
		);
		
		$this->changeBookFieldsById($bookId_INT, $change_ARR);
		
	}//deleteBookById
	
	public function getIsBookTitleExist(&$isExist_BOO_REF, $bookTitle_STR, $notBookId_INT = 0) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$auxValidInst_OBJ->checkFieldVal(BookModel::BOOK_DEFS_TITLE, $bookTitle_STR);
		
		if ($requestHandlerInst_OBJ->retIsSuccess()) {
			
			$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
			$SQLQuery_STR	=	'SELECT COUNT(*) FROM '.self::BOOKS_LIST.' WHERE '.self::BOOK_DEFS_TITLE_HASH.' = \''.md5($bookTitle_STR).'\'';
			
			if ($notBookId_INT > 0) {
				
				$SQLQuery_STR	.=	' AND '.self::BOOK_DEFS_ID.' != '.$notBookId_INT;
				
			}
			
			$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
			$SQLQuery_OBJ->execute();
			
			if ($SQLQuery_OBJ->errorCode() == '00000') {
				
				$isExist_BOO_REF	=	$SQLQuery_OBJ->fetch(PDO::FETCH_COLUMN) > 0;
				$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
				
			} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
			
			$SQLQuery_OBJ->closeCursor();
			
		}
		
	}//getIsBookTitleExist
	
	public function getBookDataById(array &$bookData_ARR_REF, bool &$isFound_BOO_REF, int $bookId_INT) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$auxValidInst_OBJ->checkFieldVal(BookModel::BOOK_DEFS_ID, $bookId_INT);
		
		if ($requestHandlerInst_OBJ->retIsSuccess()) {
			
			$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
			$SQLQuery_STR	=	'SELECT * FROM '.self::BOOKS_LIST.' WHERE '.self::BOOK_DEFS_ID.' = '.$bookId_INT;
			$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
			$SQLQuery_OBJ->execute();
			
			$isFound_BOO_REF	=	$SQLQuery_OBJ->rowCount() > 0;
			
			if ($SQLQuery_OBJ->errorCode() == '00000') {
				
				if ($isFound_BOO_REF) {
					
					$bookData_ARR_REF	=	$SQLQuery_OBJ->fetch(PDO::FETCH_ASSOC);
					
					//BOOK_DEFS_GENRE_TITLE
					//$genreId_INT	=	$bookData_ARR_REF[self::BOOK_DEFS_GENRE_ID];
					
				} $requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
				
			} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
			
			$SQLQuery_OBJ->closeCursor();
			
		}
		
	}//getBookDataById
	
	public function retAddHashCols() {
		
		return array(
			
			self::BOOK_DEFS_AUTHOR_FULL_NAME	=>	self::BOOK_DEFS_AUTHOR_HASH,
			self::BOOK_DEFS_TITLE	=>	self::BOOK_DEFS_TITLE_HASH,
			
		);
		
	}//retAddHashCols
	
	public function changeBookFieldsById(int $bookId_INT, array $change_ARR) {
		
		$auxDBInst_OBJ	=	AuxDB::retInst();
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$addHashCols_ARR	=	$this->retAddHashCols();
		
		$auxValidInst_OBJ->checkFieldVal(self::BOOK_DEFS_ID, $bookId_INT);
		
		if ($requestHandlerInst_OBJ->retIsSuccess()) {
			
			if (count($change_ARR) > 0) {
				
				$isCheckPass_BOO	=	true;
				
				foreach ($change_ARR as $fieldName_STR => &$params_ARR_REF) {
					
					$auxValidInst_OBJ->checkFieldVal($fieldName_STR, $params_ARR_REF[0]);
					//var_dump($fieldName_STR); var_dump($params_ARR_REF[0]);
					
					if ($requestHandlerInst_OBJ->retIsSuccess()) {
						
						if (key_exists($fieldName_STR, $addHashCols_ARR)) {
							
							$change_ARR[$addHashCols_ARR[$fieldName_STR]]	=
								array(md5(strtolower($params_ARR_REF[0])), true);
							
						}
						
					} else { $isCheckPass_BOO	=	false; break; }
					
				}
				
				if ($isCheckPass_BOO) {
					
					$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
					$SQLQuery_STR	=	$auxDBInst_OBJ->retBuildSQLUpdate(self::BOOKS_LIST, $change_ARR);
					$SQLQuery_STR	.=	' WHERE '.self::BOOK_DEFS_ID.' = '.$bookId_INT;
					$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
					
					$SQLQuery_OBJ->execute();
					
					if ($SQLQuery_OBJ->errorCode() == '00000' && $SQLQuery_OBJ->rowCount() == 1) {
						
						$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
						
					} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
					
					$SQLQuery_OBJ->closeCursor();
					
				}
				
			} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
			
		}
		
	}//changeBookFieldsById
	
	public function getBooksListBySortFilterLimit(
		
		&$rowsList_ARR_REF, &$rowsCount_INT_REF,
		
		$whatToSelect_STR, $skipCount_INT, $retrCount_INT,
		$isSortReq_BOO, $sortSQL_STR,
		$isFilterReq_BOO, $filterSQL_STR
		
	) {
		
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		$auxDBInst_OBJ	=	AuxDB::retInst();
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	'FROM '.self::BOOKS_LIST;
		
		if ($isFilterReq_BOO) {
			
			$SQLQuery_STR	.=	' WHERE '.$filterSQL_STR;
			
		}
		
		if ($isSortReq_BOO) {
			
			$SQLQuery_STR	.=	' ORDER BY '.$sortSQL_STR;
			
		}
		
		if ($retrCount_INT > 0) {
			
			$SQLQuery_STR	.=	' LIMIT '.$skipCount_INT.', '.$retrCount_INT;
			
		}
		
		$SQLQuery_STR	=	'SELECT '.$whatToSelect_STR.' '.$SQLQuery_STR;
		//var_dump($SQLQuery_STR);//!!!//
		
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		if ($SQLQuery_OBJ->errorCode() == '00000') {
			
			$rowsCount_INT_REF	=	$SQLQuery_OBJ->rowCount();
			
			if ($rowsCount_INT_REF > 0) {
				
				$rowsList_ARR_REF	=	$SQLQuery_OBJ->fetchAll(PDO::FETCH_ASSOC);
				
			}
			
			$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
			
		} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
		
		$SQLQuery_OBJ->closeCursor();
		
	}//getBooksListBySortFilterLimit
	
	//набор имён колонок по которым можно сортировать
	public function retAllowSortColsSet() {
		
		return array(BookModel::BOOK_DEFS_AUTHOR_FULL_NAME, BookModel::BOOK_DEFS_YEAR, BookModel::BOOK_DEFS_GENRE_TITLE);
		
	}
	
	public function retPossibleRulesSet() {
	    
		return array('asc', 'desc','default');
		
	}
	
	//набор имён колонок по которым можно фильтровать
	public function retAllowFilterColsSet() {
		
		return array(BookModel::BOOK_DEFS_AUTHOR_FULL_NAME, BookModel::BOOK_DEFS_YEAR, BookModel::BOOK_DEFS_GENRE_TITLE);
		
	}
	
	public function getFilterAssocs(&$filterAssocs_ARR_REF) {
		
		$cacheFileName_STR	=	md5($_SERVER['QUERY_STRING']);
		$cacheFilePath_STR	=	'./cache/'.__FUNCTION__.'_'.$cacheFileName_STR.'.json';
		
		if (file_exists($cacheFilePath_STR)) {// && false
			
			$filterAssocs_ARR_REF	=	json_decode(file_get_contents($cacheFilePath_STR), true);
			//print_r(filterAssocs_ARR_REF);
			return count($filterAssocs_ARR_REF) > 0;
			
		}
		
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		$auxDBInst_OBJ	=	AuxDB::retInst();
		
		//получить набор колонок по которым МОЖНО фильтровать
		$allowColsSet_ARR	=	$this->retAllowFilterColsSet();
		
		//сформировать набор колонок
		$reqColsSet_STR	=	self::BOOK_DEFS_ID.','.implode(',', $allowColsSet_ARR);
		
		$DBInst_OBJ		=	$auxDBInst_OBJ->DBInst_OBJ;
		$SQLQuery_STR	=	'SELECT '.$reqColsSet_STR.' FROM '.self::BOOKS_LIST;
		$SQLQuery_OBJ	=	$DBInst_OBJ->prepare($SQLQuery_STR);
		$SQLQuery_OBJ->execute();
		
		if ($SQLQuery_OBJ->errorCode() == '00000') {
			
			if ($SQLQuery_OBJ->rowCount() > 0) {
				
				$rowsList_ARR	=	$SQLQuery_OBJ->fetchAll(PDO::FETCH_ASSOC);
				
				//
				$filterAssocs_ARR	=	array();
				
				foreach ($allowColsSet_ARR as $i => $colName_STR) {
					
					$filterAssocs_ARR[$colName_STR]	=	array();
					
				}
				
				$idColName_STR	=	self::BOOK_DEFS_ID;
				
				foreach ($rowsList_ARR as $i => $rowData_ARR) {
					
					//print_r($rowData_ARR);
					
					$bookId_INT	=	$rowData_ARR[$idColName_STR];
					unset($rowData_ARR[$idColName_STR]);
					
					foreach ($rowData_ARR as $colName_STR => $colVal_ANY) {
						
						if (!key_exists($colVal_ANY, $filterAssocs_ARR[$colName_STR])) {
							
							$filterAssocs_ARR[$colName_STR][$colVal_ANY]	=	array();
							
						}
						
						$filterAssocs_ARR[$colName_STR][$colVal_ANY][]	=	$bookId_INT;
						
					}
					
				}
				
				$filterAssocs_ARR_REF	=	$filterAssocs_ARR;
				file_put_contents($cacheFilePath_STR, json_encode($filterAssocs_ARR));
				$requestHandlerInst_OBJ->setSilentSuccess(__LINE__);
				
			} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
			
		} else { $requestHandlerInst_OBJ->setHardErr(__LINE__); }
		
		$SQLQuery_OBJ->closeCursor();
		
	}//getFilterAssocs
	
	public function retFieldTitleByColName($colName_STR) : string {
		
		static $colsTitlesAst_ARR	=	array(
			
			self::BOOK_DEFS_AUTHOR_FULL_NAME	=>	'Автор',
			self::BOOK_DEFS_YEAR	=>	'Год выпуска',
			self::BOOK_DEFS_GENRE_TITLE	=>	'Жанр',
			
		);
		
		return key_exists($colName_STR, $colsTitlesAst_ARR) ? $colsTitlesAst_ARR[$colName_STR] : '';
		
	}//retFieldTitleByColName
    
}//BookModel