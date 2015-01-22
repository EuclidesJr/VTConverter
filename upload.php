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
	session_start();
	require("dbconfig.php");
	
	try {
		// Indefinido | Multiplos arquivos | $_FILES Ataque de corrupção
		// Se a requisição falhar por qualqer um deles, trata como invalida
		if (
			!isset($_FILES['arqUpload']['error']) ||
			is_array($_FILES['arqUpload']['error'])
		) {
			throw new RuntimeException('Parâmetros inválidos.');
		}
		
		// Verifica erros
		switch ($_FILES['arqUpload']['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException('Nenhum arquivo enviado.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException('Tamanho limite excedido.');
			default:
				throw new RuntimeException('Erro desconhecido.');
		}
		
		//Verifica o tipo do arquivo
		if ($_FILES['arqUpload']['type'] != 'text/xml') {
			throw new RuntimeException('Formato do arquivo inválido.');
		}
		
		//Separo a extesão e o nome
		$extn = explode('.',$_FILES['arqUpload']['name']);
		$extensao = $extn[1]; //Salvo a extesão
		
		//Gero o novo nome do arquivo
		$novonome = $_SESSION['usuarioid'].'_'.$extn[0];
		
		$dir = 'upload/'.$novonome.'.'.$extensao ;
		
		// Verifica se já exite
		//if (file_exists("upload/" . $_SESSION['usuarioid'].'_'.$_FILES["file"]["name"])) {
		if (file_exists($dir)) {
			throw new RuntimeException( $_FILES["arqUpload"]["name"] . " já existe no servidor. ");
		} else if (!move_uploaded_file($_FILES['arqUpload']['tmp_name'],$dir)) {
			throw new RuntimeException('Falha ao mover arquivo enviado. '.$dir);
		}
		
		//Se não existir no servidor, insere no BD.
		//É necessário ter o arqivo salvo no BD para poder validar.
		conecta();
		
		$data = date('d/m/Y');
		
		$sql = "insert into arquivo (arqnome,arqtam,arqnometemp,arqusu,dataupload) 
				values ('".$_FILES['arqUpload']['name']."',
				        '".$_FILES['arqUpload']['size']."',
				        '".$novonome.'.'.$extensao."',
				        '".$_SESSION['usuarioid']."', 
				        NOW())";
		
		$res = mysql_query($sql);
	
		// Verifica o resultado.
		if (!$res) {
			//Caso ocorra algum erro ao salvar, remove o arquivo do diretório
			unlink($dir);
			throw new RuntimeException('Falha ao salvar arquivo.');
		}
		
		throw new RuntimeException('Arquivo enviado com sucesso.');
		
	} catch (RuntimeException $e) {
		echo $e->getMessage();
	}

?>
