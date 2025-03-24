<?php

namespace App\Auxiliary;

use App\Models\BookModel;
class AuxMain {
	
	private function __construct() {}
	
	public static function retInst() : AuxMain {
		
		static $thisInst_OBJ	=	null;
		
		if (!is_object($thisInst_OBJ)) {
			
			$thisInst_OBJ	=	new self();
			
		}
		
		return $thisInst_OBJ;
		
	}//retInst
	
	public function retGenHTMLElemId() { static $fieldsCount_INT	=	0; return 'HTMLElem_'.(++$fieldsCount_INT); }
	
	public function retBuildSelectOptionsByAst($elemsAst_ARR) {
		
		$result_HTML	=	'';
		
		foreach ($elemsAst_ARR as $optionValue_STR => $optionCaption_STR) {
			
			$result_HTML	.=	'<option value="'.$optionValue_STR.'">'.$optionCaption_STR.'</option>';
			
		}
		
		return $result_HTML;
		
	}//retBuildSelectOptionsByAst
	
	public function getHandleSort(&$sortAst_ARR_REF, $allowColsSet_ARR) {
		
		$bookModelInst_OBJ	=	BookModel::retInst();
		
		$cacheFileName_STR	=	md5($_SERVER['QUERY_STRING']);
		$cacheFilePath_STR	=	'./cache/'.__FUNCTION__.'_'.$cacheFileName_STR.'.json';
		
		if (file_exists($cacheFilePath_STR) && false) {//!!!//
			
			$sortAst_ARR_REF	=	json_decode(file_get_contents($cacheFilePath_STR), true);
			//print_r($filtersAst_ARR_REF);
			return count($sortAst_ARR_REF) > 0;
			
		}
		
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$isSortReq_BOO_REF	=	false;
		$isPrevStepPass_BOO	=	false;
		
		//получить набор возможных правил сортировки
		$allowRulesSet_ARR	=	$bookModelInst_OBJ->retPossibleRulesSet();
		
		//набор колонок по которым ХОЧЕТ отсортировать посетитель
		$sortColsSet_STR	=	(string) ($_GET['sort-cols'] ?? '');
		$sortColsSet_STR	=	preg_replace('#[^0-9a-z\_\,]#', '', $sortColsSet_STR);
		
		//набор правил по которым ХОЧЕТ отсортировать посетитель
		$sortRulesSet_STR	=	(string) ($_GET['sort-rules'] ?? '');
		$sortRulesSet_STR	=	preg_replace('#[^a-z\_\,]#', '', $sortRulesSet_STR);
		
		//ассоц массив возможной сортировки
		$maybeSortAst_ARR	=	array();
		
		foreach ($allowColsSet_ARR as $x => $colName_STR) {
			
			$maybeSortAst_ARR[$colName_STR]	=	'default';
			
		}
		
		$pre_colsSet_ARR	=	explode(',', $sortColsSet_STR);
		$pre_rulesSet_ARR	=	explode(',', $sortRulesSet_STR);
		
		foreach ($pre_colsSet_ARR as $i => $colName_STR) {
			
			$isCyclePass_BOO	=	false;
			
			if ($colName_STR !== '' && key_exists($colName_STR, $maybeSortAst_ARR)) {
				
				if (key_exists($i, $pre_rulesSet_ARR)) {
					
					$mb_rule_STR	=	$pre_rulesSet_ARR[$i];
					
					if (in_array($mb_rule_STR, $allowRulesSet_ARR)) {
						
						$maybeSortAst_ARR[$colName_STR]	=	$mb_rule_STR;
						
					}
					
				}
				
			}
			
		}//foreach
		
		if (count($maybeSortAst_ARR) > 0) {
			
			$sortAst_ARR_REF	=	$maybeSortAst_ARR;
			file_put_contents($cacheFilePath_STR, json_encode($maybeSortAst_ARR));
			return true;
			
		} return false;
		
	}//getHandleSort
	
	public function retSortSQLByAst($sortAst_ARR, $quoteColsSet_ARR) : string {
		
		$result_STR	=	'';
		
		foreach ($sortAst_ARR as $colName_STR => $rule_STR) {
			
			if ($rule_STR == 'default') { continue; }
			
			$result_STR	.=	$colName_STR.' '.$rule_STR.',';
			
		}
		
		if (strlen($result_STR) > 0) {
			
			$result_STR	=	substr($result_STR, 0, -1);
			
		} return $result_STR;
		
	}//retSortSQLByAst
	
	public function getHandleFilter(&$filtersAst_ARR_REF, $allowColsSet_ARR) {
		
		$cacheFileName_STR	=	md5($_SERVER['QUERY_STRING']);
		$cacheFilePath_STR	=	'./cache/'.__FUNCTION__.'_'.$cacheFileName_STR.'.json';
		
		if (file_exists($cacheFilePath_STR)) {//!!!// && false
			
			$filtersAst_ARR_REF	=	json_decode(file_get_contents($cacheFilePath_STR), true);
			//print_r($filtersAst_ARR_REF);
			return count($filtersAst_ARR_REF) > 0;
			
		}
		
		$auxValidInst_OBJ	=	AuxValid::retInst();
		$requestHandlerInst_OBJ	=	RequestHandler::retInst();
		
		$isFilterReq_BOO_REF=	false;
		$isPrevStepPass_BOO	=	false;
		
		//набор возможных правил фильтрации
		$allowRulesSet_ARR	=	array('only', 'not');
		
		//набор колонок по которым ХОЧЕТ отфильтровать посетитель
		$filterColsSet_STR	=	(string) ($_GET['filter-cols'] ?? '');
		$filterColsSet_STR	=	preg_replace('#[^0-9a-z\_\,]#', '', $filterColsSet_STR);
		
		//ассоц массив возможной фильтрации
		$maybeFiltersAst_ARR	=	array();
		
		//если после чистки что-то осталось в строке
		if (strlen($filterColsSet_STR) > 0) {
			
			$pre_colsSet_ARR	=	explode(',', $filterColsSet_STR);
			
			foreach ($pre_colsSet_ARR as $i => $colName_STR) {
				
				$isCyclePass_BOO	=	false;
				
				if ($colName_STR !== '' && in_array($colName_STR, $allowColsSet_ARR)) {
					
					$unsafeValsSet_ARR	=	array();
					$colValKey_STR	=	'filter_'.$colName_STR;
					
					if (key_exists($colValKey_STR, $_GET)) {
						
						$unsafeValsSet_STR	=	preg_replace('#(?:\,)+$#', '', $_GET[$colValKey_STR]);
						
						if (strlen($unsafeValsSet_STR) > 0) {
							
							$unsafeValsSet_ARR	=	explode(',', $unsafeValsSet_STR);
							$isCyclePass_BOO	=	true;
							
						}
						
					}
					
					$maybeFiltersAst_ARR[$colName_STR]	=	array('', array(), 'unsafe' => $unsafeValsSet_ARR);
					
				}
				
				if (!$isCyclePass_BOO) { break; }
				
			} $isPrevStepPass_BOO	=	$isCyclePass_BOO;
			
		}
		
		//если хотя бы одна колонка прошла проверку
		if (count($maybeFiltersAst_ARR) > 0 && $isPrevStepPass_BOO) {
			
			$isPrevStepPass_BOO	=	false;
			
			//получить набор правил сортировки
			$filterRulesSet_STR	=	(string) ($_GET['filter-rules'] ?? '');
			$filterRulesSet_STR	=	preg_replace('#[^0-9a-z\_\,]#', '', $filterRulesSet_STR);
			
			//если после чистки что-то осталось в строке
			if (strlen($filterRulesSet_STR) > 0) {
				
				$pre_rulesSet_ARR	=	explode(',', $filterRulesSet_STR);
				
				foreach ($pre_rulesSet_ARR as $i => $rule_STR) {
					
					$isCyclePass_BOO	=	false;
					
					//если правило сортировки верное
					if (in_array($rule_STR, $allowRulesSet_ARR)) {
						
						if (key_exists($i, $pre_colsSet_ARR)) {
							
							$maybeFiltersAst_ARR[$pre_colsSet_ARR[$i]][0]	=	$rule_STR;
							$isCyclePass_BOO	=	true;
							
						}
						
					}
					
					if (!$isCyclePass_BOO) { break; }
					
				} $isPrevStepPass_BOO	=	$isCyclePass_BOO;
				
			}//если после чистки что-то осталось в строке
			
		}//если хотя бы одна колонка прошла проверку
		
		if ($isPrevStepPass_BOO) {
			
			$isPrevStepPass_BOO	=	false;
			//print_r($maybeFiltersAst_ARR);
			
			foreach ($maybeFiltersAst_ARR as $colName_STR => &$params_ARR_REF) {
				
				$unsafeValsSet_ARR	=	$params_ARR_REF['unsafe'];
				$safeValsSet_ARR_REF=	&$params_ARR_REF[1];
				
				foreach ($unsafeValsSet_ARR as $i => $colVal_ANY) {
					
					$isCyclePass_BOO	=	true;
					
					$auxValidInst_OBJ->checkFieldVal($colName_STR, $colVal_ANY);
					$isCyclePass_BOO	=	$requestHandlerInst_OBJ->retIsSuccess();
					
					if ($isCyclePass_BOO) {
						
						$safeValsSet_ARR_REF[]	=	$colVal_ANY;
						
					}
					
				}
				
			}
			
			$filtersAst_ARR_REF	=	$maybeFiltersAst_ARR;
			file_put_contents($cacheFilePath_STR, json_encode($maybeFiltersAst_ARR));
			return true;
			
		} return false;
		
	}//getHandleFilter
	
	public function retFilterSQLByAst($filtersAst_ARR, $quoteColsSet_ARR, $hashColsAst_ARR) : string {
		
		$result_STR	=	'';
		
		foreach ($filtersAst_ARR as $colName_STR => $params_ARR) {
			
			$valsSet_STR	=	'';
			$quoteVals_BOO	=	in_array($colName_STR, $quoteColsSet_ARR);
			
			foreach ($params_ARR[1] as $i => $val_ANY) {
				
				$valsSet_STR	.=	$quoteVals_BOO ? '\''.$val_ANY.'\'' : $val_ANY;
				$valsSet_STR	.=	',';
				
			}
			
			if (count($params_ARR[1]) > 0 && $valsSet_STR[-1] == ',') { $valsSet_STR	=	substr($valsSet_STR, 0, -1); } 
			
			$SQLRule_STR	=	'';
			
			if ($params_ARR[0] == 'only') {
				
				$SQLRule_STR	=	'IN';
				
			} else {
				
				//not
				$SQLRule_STR	=	'NOT IN';
				
			}
			
			$result_STR	.=	$colName_STR.' '.$SQLRule_STR.' ('.$valsSet_STR.')'.' AND ';
			
		}
		
		if (strlen($result_STR) > 5) { $result_STR	=	substr($result_STR, 0, -5); }
		
		return $result_STR;
		
	}//retFilterSQLByAst
	
	public function getCalcPagination(
		
		&$skipCount_INT_REF, &$pagesNumsSet_ARR_REF,
		&$currPageNum_INT_REF, $elemsPerPage_INT, $totalCount_INT
		
	) {
		
		$currPageNum_INT_REF	=	(int) $currPageNum_INT_REF;
		$currPageNum_INT_REF	=	$currPageNum_INT_REF < 1 ? 1 : $currPageNum_INT_REF;
		
		$divRest_INT	=	$totalCount_INT % $elemsPerPage_INT;
		$maxPagesCount_INT	=	($totalCount_INT - $divRest_INT) / $elemsPerPage_INT;
		
		//остаток от деления это элементы которых недостаточно для полной страницы
		if ($divRest_INT > 0) { $maxPagesCount_INT++; }
		
		$currPageNum_INT_REF	=	$currPageNum_INT_REF > $maxPagesCount_INT ? $maxPagesCount_INT : $currPageNum_INT_REF;
		
		if ($totalCount_INT > 0) {
			
			$skipCount_INT_REF	=	($currPageNum_INT_REF - 1) * $elemsPerPage_INT;
			
		} else { $skipCount_INT_REF	=	0; }
		
		for ($i = 0; $i < $maxPagesCount_INT; $i++) {
			
			$pagesNumsSet_ARR_REF[]	=	$i + 1;
			
		}
		
		/*
		var_dump($totalCount_INT);
		var_dump($elemsPerPage_INT);
		var_dump($divRest_INT);
		var_dump($maxPagesCount_INT);
		var_dump($skipCount_INT_REF);
		var_dump($currPageNum_INT);
		*/
		
	}//getCalcPagination
	
	public function retBuildHTMLPageTitle($pageTitle_STR) {
		
		return '<did class="HTMLPageTitle_out">'.$pageTitle_STR.'</did><br><br>';
		
	}//retBuildHTMLPageTitle
	
}//AuxMain

?>