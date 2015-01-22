<?php
	/*
	 * 	Copyright (c) 2015 Euclides Cardoso Júnior <euclides.c.jr@hotmail.com>
	 * 	Permission is hereby granted, free of charge, to any person
	 * 	obtaining a copy of this software and associated documentation
	 * 	files (the "Software"), to deal in the Software without
	 * 	restriction, including without limitation the rights to use, copy,
	 * 	modify, merge, publish, distribute, sublicense, and/or sell copies
	 * 	of the Software, and to permit persons to whom the Software is
	 * 	furnished to do so, subject to the following conditions:
	 * 
	 * 	The above copyright notice and this permission notice shall be
	 * 	included in all copies or substantial portions of the Software.
	 * 
	 * 	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	 * 	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
	 * 	OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
	 * 	NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
	 * 	HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
	 * 	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	 * 	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	 * 	OTHER DEALINGS IN THE SOFTWARE.
	 *  
	 * */
	$arq = $_REQUEST["arq"];
	$arqxsd = $_REQUEST['xsd'];
	
	function libxml_display_error($error) 
	{ 
		$return = "<br/>\n"; 
		switch ($error->level) { 
			case LIBXML_ERR_WARNING: 
				$return .= "<b>Warning $error->code</b>: "; 
				break; 
			case LIBXML_ERR_ERROR: 
				$return .= "<b>Erro: $error->code</b>: "; 
				break; 
			case LIBXML_ERR_FATAL: 
				$return .= "<b>Erro fatal: $error->code</b>: "; 
				break; 
		} 
		$return .= trim($error->message); 
		if ($error->file) { 
			$return .= " em <b>$error->file</b>"; 
		} 
		$return .= " na linha <b>$error->line</b>\n"; 

		return $return; 
	} 

	function libxml_display_errors() { 
		$errors = libxml_get_errors(); 
		foreach ($errors as $error) { 
			print libxml_display_error($error); 
		} 
		libxml_clear_errors(); 
	} 
 
	libxml_use_internal_errors(true); 
	
	//$arquivo = '../upload/'.$arq;
	//echo $arquivo;
	
	
	//if (file_exists($arquivo)) {
	if (file_exists($arq)) {
		$xml = new DOMDocument(); 
		//$result = $xml->load($arquivo);
		$result = $xml->load($arq); 
	 	
		if($result === TRUE) {
	        //echo "Documento é bem formado\n";
	        if (!$xml->schemaValidate('arquivos/'.$arqxsd)) { 
				print '<b>Erros encontrados!</b>'; 
				libxml_display_errors(); 
			} 
			else { 
				echo "Arquivo validado com sucesso"; 
			}
	        
	    } else {
	        echo "Documento não é bem fomado";
	    }		   
	} else {
	    echo "O arquivo não encontrado no servidor";
	}
?>
