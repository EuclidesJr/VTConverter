<?php
	session_start(); // Inicia a sessão
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
	require("../dbconfig.php");
	
	conecta();

	$sql = "delete from arquivo where arqnome='".$_REQUEST['arq_orig']."' and arqusu=".$_SESSION['usuarioid'];
	//echo "----- ".$sql;
	$res = mysql_query($sql);
	
	// Verifica o resultado.
	if (!$res) {
		//Caso ocorra algum erro 
		echo 'Falha ao remover o arquivo.';
	}else{
		unlink('../upload/'.$_SESSION['usuarioid'].'_'.$_REQUEST['arq_orig']);
		unlink('../convertidos/'.$_SESSION['usuarioid'].'_'.$_REQUEST['arq_conv']);
		echo 'Arquivo removido com sucesso';
	}	 
?>
