<?php
	session_start();
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
	require('dbconfig.php');
	require("localizacao/stringsPT-BR.php");
		
	$msg = conecta();
	
	if($msg != 'Conectado'){
		echo "Erro ".$msg ;
		exit;
	}else{
		$sql = "select * from usuario where usuarioemail like('".$_POST['email']."') and usuariosenha like('".$_POST['psw']."')";
		$result = mysql_query($sql);
	
		// Verifica o resultado.
		if (!$result) {
			//$menssagem  = 'Query inválida: ' . mysql_error() . "\n";
			//$menssagem .= 'Query inteira: ' . $sql;
			//die($menssagem);
			?>			
			<meta http-equiv="refresh" content="0;URL=index.php?erro=1">
			<?php
		}
	
		$num = mysql_num_rows($result);
		$row = mysql_fetch_row($result);
		if($num == 1){
			$_SESSION['nome'] = $row[1]; 
			$_SESSION['usuarioid'] = $row[0];
		?>
			<meta http-equiv="refresh" content="0;URL=principal.php">
		<?php		
		}else{
			
		?>			
			<meta http-equiv="refresh" content="0;URL=index.php?erro=2">
		<?php
			 
		}		
		mysql_free_result($result);
	}	
?>
