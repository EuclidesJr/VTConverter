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
	$con;
	$servidor = "localhost";
	$senha = "mzvt8582";
	$usuario = "root";
	$esquema = "dc_conversor";
	
	function conecta(){
		$con = mysql_connect("localhost",  "root", "mzvt8582"); // Conecta ao banco
		
		if ($con) {
			$db = mysql_select_db("dc_conversor"); // Seleciona o banco
			if(!$db){
				return 'Não foi possivel selecionar o banco:';
			}else{
				return 'Conectado';
			}
		} else{
			return 'Não foi possível conectar: ' . mysql_error();
		}		
	}
	
	function desconecta(){
		mysql_close($con);
	}
?>
