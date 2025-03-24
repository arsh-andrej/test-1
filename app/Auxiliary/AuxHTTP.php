<?php

namespace App\Auxiliary;

class AuxHTTP {
	
	private function __construct() {}
	
	public static function retInst() : AuxHTTP {
		
		static $thisInst_OBJ	=	null;
		return is_object($thisInst_OBJ) ? $thisInst_OBJ : $thisInst_OBJ = new self();
		
	}//retInst
	

	
	
	
}//AuxHTTP

?>