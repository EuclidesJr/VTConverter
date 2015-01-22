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
	session_start(); // Inicia a sessão
	require("valida_sessao.php");
	require("localizacao/stringsPT-BR.php");
	require 'dbconfig.php';
	$msg = conecta();
		
	$sqlArqs = "select * from arquivo where arqusu = ".$_SESSION['usuarioid']." order by dataupload desc";
	$result = mysql_query($sqlArqs);

	// Verifica o resultado.
	
	
	$html = '
	<table border="1" cellspacing=0 cellpadding=2 bordercolor="666633" style="text-align: center;margin-left:auto; margin-right: auto;color: white;font-family: Orbitron Black;">
		<tr>
			
			<td colspan="6"><font style="color: #d0d000;">Histórico de arquivos</font></td>
		</tr>
		<tr>
			<td style="color: #959500;">Nome original</td>
			<td style="color: #959500;">Tamanho</td>
			<td style="color: #959500;">Dt. upload</td>
			<td style="color: #959500;">Nome convertido</td>
			<td style="color: #959500;">Download</td>
			<td style="color: #959500;">Remover</td>
		</tr>';
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if(mysql_num_rows($result) < 1){
				$html .= '
				<div style="margin-left: auto; 	margin-right: auto; text-align: center;color: white;font-family: Orbitron Black;">
					<b>Nenhum arquivo enviado.</b>
				</div>';
			} else{
				$html .= '
					<tr>
						<td>'. $row["arqnome"] .'</td>';
							$arqTam = $row["arqtam"];
							if ($arqTam > 1024 * 1024)
								$arqTamanho = round(round($arqTam * 100 / (1024 * 1024),2) / 100,2).'MB';
							else
								$arqTamanho = round(round($arqTam * 100 / 1024, 2) / 100,2).'KB';
			 
				$html.='<td>'.$arqTamanho.'</td>
						<td>'. date('d/m/Y', strtotime($row['dataupload'])).'</td>
						<td>'.$row["arqconvertido"].'</td>
						<td>
							<i class="fa fa-download"  style="cursor:pointer;" onclick="iniciaDownloadArq('.$row['arqconvertido'].');" title="Download"></i>
						</td>
						<td>
							<i class="fa fa-times"  style="cursor:pointer;" onclick="remove_historico('.$row['arqnome'].' , '.$row['arqconvertido'].');" title="Remover arquivo"></i>
						</td>
					</tr>
				</table>';		
			}
		}
	
	echo $html;
?>
