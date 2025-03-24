<?php

namespace App\Auxiliary;

use Illuminate\Support\Facades\DB;


class AuxDB {
	
	private function __construct() {}
	
	public static function retInst() : AuxDB {
		
		static $thisInst_OBJ	=	null;
		
		if (!is_object($thisInst_OBJ)) {
			
			$thisInst_OBJ	=	new self();
			$thisInst_OBJ->DBInst_OBJ	=	DB::connection()->getPdo();
			
		} return $thisInst_OBJ;
		
	}//retInst
	
	public $DBInst_OBJ	=	null;
	
	public function retBuildSQLInsert(string $DBTableName_STR, array $colsAndValsAst_ARR) : string {
		
		$colsSet_STR	=	'';
		$valsSet_STR	=	'';
		
		foreach ($colsAndValsAst_ARR as $colName_STR => $params_ARR) {
			
			$colsSet_STR	.=	$colName_STR.', ';
			
			if ($params_ARR[1]) {
				
				$valsSet_STR	.=	'\''.$params_ARR[0].'\', ';
				
			} else { $valsSet_STR	.=	$params_ARR[0].', '; }
			
		}
		
		if (strlen($colsSet_STR) > 2) {
			
			$colsSet_STR	=	substr($colsSet_STR, 0 , -2);
			$valsSet_STR	=	substr($valsSet_STR, 0 , -2);
		}
		
		return 'INSERT INTO '.$DBTableName_STR.' ('.$colsSet_STR.') VALUES ('.$valsSet_STR.')';
		
	}//retBuildSQLInsert
	
	//WHERE нужно добавить самому
	public function retBuildSQLUpdate(string $DBTableName_STR, array $colsAndValsAst_ARR) : string {
		
		$colsAndVals_STR	=	'';
		
		foreach ($colsAndValsAst_ARR as $colName_STR => $params_ARR) {
			
			$colsAndVals_STR	.=	$colName_STR.' = ';
			
			if ($params_ARR[1]) {
				
				$colsAndVals_STR	.=	'\''.$params_ARR[0].'\'';
				
			} else { $colsAndVals_STR	.=	$params_ARR[0]; }
			
			$colsAndVals_STR	.=	', ';
			
		}
		
		if (strlen($colsAndVals_STR) > 2) {
			
			$colsAndVals_STR	=	substr($colsAndVals_STR, 0 , -2);
			
		}
		
		return 'UPDATE '.$DBTableName_STR.' SET '.$colsAndVals_STR;
		
	}//retBuildSQLUpdate
	
}//AuxDB

?>