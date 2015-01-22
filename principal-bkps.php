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
	//echo 'aqui'.$_SESSION['usuarioid'];	
?>
<!DOCTYPE HTML>
<html>
	<head>
        <meta charset="utf-8" />
        <title>VXDL / TOSCA converter</title>
        <link href="css/geral.css" rel="stylesheet" type="text/css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script type="text/javascript" src="scripts/notify.min.js"></script>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
		<script>
		
		function validaArqVXDL(){
			//Valida o arquivo enviado
			$.ajax({
				url: 'conversor/valida.php?arq=../upload/'+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name+'&xsd=VXDL.xsd',
				type: 'POST',
				success: function (msg) {
					if (msg == "Arquivo validado com sucesso"){
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': ' + msg;
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Iniciando conversão...';
						//Se o arquivo foi validado com sucesso,
						//Chama o conversor
						$.ajax({
							url: 'conversor/converte.php?arq='+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name+'&arqOrig='+document.getElementById('arqUpload').files[0].name,
							type: 'POST',
							success: function (msg) {
								if (msg == "Arquivo convertido com sucesso."){
									document.getElementById('log').innerHTML = $("#log").text() + '\n'+ pegaDataAtual() + ': ' + msg;
									document.getElementById('log').innerHTML = $("#log").text() + '\n'+ pegaDataAtual() + ': ' + 'Validando o arquivo...';
									//Valida o arquivo convertido
									$.ajax({
										url: 'conversor/valida.php?arq=../convertidos/TOSCA_'+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name+'&xsd=TOSCA-v1.0.xsd',
										type: 'POST',
										success: function (msg) {
											if (msg == "Arquivo validado com sucesso"){
												document.getElementById('log').innerHTML = $("#log").text() + '\n'+ pegaDataAtual() + ': ' + msg;
												document.getElementById("download").style.display = "block";
										 		$("#arquivos").html();
										 	}else{
												document.getElementById('log').innerHTML = $("#log").text() + '\n '+ pegaDataAtual() + ': Erro: ' + msg;
												removeArquivo();									
											}	
									 	}		
									});
								} else{
									document.getElementById('log').innerHTML = $("#log").text() + '\n '+ pegaDataAtual() + ': Erro: ' + msg;
									removeArquivo();									
								}					
							}		
						});					
						
					} else{
						//Caso ocorra algum erro ao validar o arquivo
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Erro: ' + msg;
						removeArquivo();
					}					
				}		
			});						
		}
		function validaArqTOSCA(){
				//Valida o arquivo enviado
			$.ajax({
				url: 'conversor/valida.php?arq=../upload/'+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name+'&xsd=TOSCA-v1.0.xsd',
				type: 'POST',
				success: function (msg) {
					if (msg == "Arquivo validado com sucesso"){
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': ' + msg;
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Iniciando conversão...';
						//Se o arquivo foi validado com sucesso,
						//Chama o conversor
						$.ajax({
							url: 'conversor/converteTV.php?arq='+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name+'&arqOrig='+document.getElementById('arqUpload').files[0].name,
							type: 'POST',
							success: function (msg) {
								if (msg == "Arquivo convertido com sucesso."){
									document.getElementById('log').innerHTML = $("#log").text() + '\n'+ pegaDataAtual() + ': ' + msg;
									document.getElementById('log').innerHTML = $("#log").text() + '\n'+ pegaDataAtual() + ': ' + 'Validando o arquivo...';
									//Valida o arquivo convertido
									$.ajax({
										url: 'conversor/valida.php?arq=../convertidos/VXDL_'+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name+'&xsd=VXDL.xsd',
										type: 'POST',
										success: function (msg) {
											if (msg == "Arquivo validado com sucesso"){
												document.getElementById('log').innerHTML = $("#log").text() + '\n'+ pegaDataAtual() + ': ' + msg;
												document.getElementById("download").style.display = "block";
										 		$("#arquivos").html();
										 	}else{
												document.getElementById('log').innerHTML = $("#log").text() + '\n '+ pegaDataAtual() + ': Erro: ' + msg;
												removeArquivo();									
											}	
									 	}		
									});
								} else{
									document.getElementById('log').innerHTML = $("#log").text() + '\n '+ pegaDataAtual() + ': Erro: ' + msg;
									removeArquivo();									
								}					
							}		
						});					
						
					} else{
						//Caso ocorra algum erro ao validar o arquivo
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ':--- Erro: ' + msg;
						removeArquivo();
					}					
				}		
			});	
		}
		function removeArquivo(){
			//Remove do servidor e do BD
			//document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Erro: ' + msg;
			document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Removendo arquivo enviado ...';
			$.ajax({
				url: 'conversor/remove.php?arq='+document.getElementById('arqUpload').files[0].name,
				type: 'POST',
				success: function(msg){
					if (msg == 'Arquivo removido com sucesso') {
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': ' + msg;
					} else{
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Erro: ' + msg;			
					}								
				}
			});
		}
		
		function remove_historico(arq_orig, arq_conv){
			//Remove do servidor e do BD
			//document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Erro: ' + msg;
			var r = confirm("Confirmar remoção do arquivo"+arq_orig+"?");
			if (r == true) {
				document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Removendo arquivo selecionado ...';
				$.ajax({
					url: 'conversor/remove_historico.php?arq_orig='+arq_orig+'&arq_conv='+arq_conv,
					type: 'POST',
					success: function(msg){
						if (msg == 'Arquivo removido com sucesso') {
							//location.reload();
							document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': ' + msg;
						} else{
							document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Erro: ' + msg;			
						}								
					}
				});
			}			
		}
		
		function carregaHistorico(){
			$.ajax({
				url: 'principal_carrega_historico.php',
				type: 'POST',
				success: function(data){
					//if (msg == '1' || msg == 1) {
						//document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': ' + msg;
						$('#arquivos').html(data);
					//} else{
					//	document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Erro: ' + msg;			
					//}								
				}
			});
		}
		
		function pegaDataAtual(){
			var data = new Date();

			// Guarda cada pedaço em uma variável
			var dia     = data.getDate();           // 1-31
			var dia_sem = data.getDay();            // 0-6 (zero=domingo)
			var mes     = data.getMonth();          // 0-11 (zero=janeiro)
			var ano2    = data.getYear();           // 2 dígitos
			var ano4    = data.getFullYear();       // 4 dígitos
			var hora    = data.getHours();          // 0-23
			var min     = data.getMinutes();        // 0-59
			var seg     = data.getSeconds();        // 0-59
			var mseg    = data.getMilliseconds();   // 0-999
			var tz      = data.getTimezoneOffset(); // em minutos
			
			var str_data = dia + '/' + (mes+1) + '/' + ano4;
			var str_hora = hora + ':' + min + ':' + seg;
			
			return str_data+' - '+str_hora;
		}
		
		function arquivoSelecionado() {
			var file = document.getElementById('arqUpload').files[0];
			if (file) {
				var arqTamanho = 0;
				if (file.size > 1024 * 1024)
					arqTamanho = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
				else
					arqTamanho = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
				
				document.getElementById('arqNome1').innerHTML = file.name;
				document.getElementById('arqTamanho1').innerHTML = arqTamanho;
				document.getElementById('arqTipo1').innerHTML = file.type;
			}
		}				
		
		function iniciaDownload()
		{
			if(document.getElementById("VXDL").checked){
				var url='download.php?arq=TOSCA_'+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name; 
				window.open(url);
			}else if(document.getElementById("TOSCA").checked){
				var url='download.php?arq=VXDL_'+<?php echo $_SESSION['usuarioid'];?>+'_'+document.getElementById('arqUpload').files[0].name; 
			window.open(url);
			}			
		}
		
		function iniciaDownloadArq(arq)
		{
			//alert(arq);
			var url='download.php?arq='+arq; 
			window.open(url);
		}
        </script>
    </head>
	<body>
		<header class="topo">
            <h2 style="margin-left: 25px;">
            	<font style="font-family: 'Terminator Real NFI';font-size: 30px; color: white;">VT</font>
				<font style="font-family: 'Terminator Real NFI';font-size: 30px; color:#d0d000;">CONVERSOR</font>
			</h2>
		</header>
		<br clear="all"/>
		<div class="containerEsq">
			<div class="bRedondaEsq">
				<b style="margin-left: 10px;color: white;"><?php echo $titulo1;?></b>
			<!--</div>
			<div class="container2">
				<!--<form id="upload" enctype="multipart/form-data" method="post" action="upload.php">-->
				<form id="upload">
					<div style="margin-left:10px;">
						<br/>
						<input type="radio" name="linguagem" id="VXDL" checked=""/> VXDL 
						<input type="radio" name="linguagem" id="TOSCA" /> TOSCA<br/>
						<!--<label for="arqUpload" style="color: #75755f;"><?php echo $textoUploaArq;?></label><br />-->
						<div class="bRedonda2" style="margin-top:5px;">
							<input type="file" name="arqUpload" id="arqUpload" onchange="arquivoSelecionado();" style="margin-left:8px;margin-top:5px;margin-bottom:5px;color: #75755f;"/>
						</div>
					</div>
					
					<div style="max-width: 180px">
						<!-- <input type="button" onclick="uploadArquivo()" value="<?php echo $btnUpload;?>" /> -->
						<input class="btn1" id="upload" name="upload" style="margin-left:8px;margin-top:10px; float: left" type="submit" value="<?php echo $btnUpload;?>" />
						<button class="btn1" id="download" name="download" style="margin-left:8px;margin-top:10px; display: none; float: right;"  
								onclick="iniciaDownload();" type="button">
							<?php echo $btnDownload;?> <!--onclick="download.php?arq=+document.getElementById(arqUpload).files[0].name"-->
						</button>
					</div>	
					<br/>
					<div style="margin-top:20px;">
						<div id="arqNome"  style="margin-left:8px; float: left; font-family: Orbitron Black;">Nome:</div>	  <div id="arqNome1" style="color: #75755f;"> - </div>
						<div id="arqTamanho" style="margin-left:8px; float: left;font-family: Orbitron Black;">Tamanho:</div> <div id="arqTamanho1" style="color: #75755f;"> - </div>
						<div id="arqTipo" style="margin-left:8px; float: left;font-family: Orbitron Black;">Tipo:</div>  <div id="arqTipo1" style="color: #75755f;"> - </div>
					</div>
				</form>
			</div>
		</div>
		<div class="containerDir">	
			<div class="bRedondaDir">
				<b style="margin-left: 15px;color: white;"><?php echo $textoTitLog;?></b>
				<br />
				<br />				
			<!--</div>
			<div class="container3">
				<div id="log" style="margin-left:8px;max-height:200px; overflow:scroll;overflow-x:auto;"></div>-->
				<textarea id="log" rows="9" style="margin-left: 10px; width: 90%;"></textarea>
			</div>
		</div>
		<br clear="all" />
		<div class="containerCenter"> 
			<div class="bRedondaCenter" id="arquivos">				
			</div>
		</div>
	</body>
	<script>
		//submit personalizado para o form
		$("form#upload").submit(function(event){
			
			event.preventDefault();
			//Seleciona todos os dados
			var formData = new FormData($(this)[0]);
			$.ajax({
				url: 'upload.php',
				type: 'POST',
				data: formData,
				async: false,
				cache: false,
				contentType: false,
				processData: false,
				success: function (returndata) {
					if(returndata=='Arquivo enviado com sucesso.' )
					{
						if(document.getElementById("VXDL").checked){
							//alert("VXDL");
							document.getElementById('log').innerHTML = $("#log").text() + '\n'+pegaDataAtual() + ': Linguagem VXDL selecionada.';
							document.getElementById('log').innerHTML = $("#log").text() + '\n'+pegaDataAtual() + ': ' + returndata;
							validaArqVXDL();
						}else if(document.getElementById("TOSCA").checked){
							//alert("TOSCA");
							document.getElementById('log').innerHTML = $("#log").text() + '\n'+pegaDataAtual() + ': Linguagem TOSCA selecionada.';
							document.getElementById('log').innerHTML = $("#log").text() + '\n'+pegaDataAtual() + ': ' + returndata;
							validaArqTOSCA();
						}
						//document.getElementById('log').innerHTML = $("#log").text() + '\n'+pegaDataAtual() + ': ' + returndata;
						//validaArq();						
					}else{
						document.getElementById('log').innerHTML = $("#log").text() + '\n' + pegaDataAtual() + ': Erro: ' + returndata;
					}
					
				}
			});			
			return false;
		});
	</script>	
</html>
