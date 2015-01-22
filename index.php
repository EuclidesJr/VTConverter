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
	require("localizacao/stringsPT-BR.php");
		
?>
<!DOCTYPE html>
	<head>
		<meta charset="UTF-8">
		<title>Login</title>
		<link href="css/geral.css" rel="stylesheet" type="text/css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script type="text/javascript" src="scripts/notify.min.js"></script>
		<script>
			function cadastrar(){
				//alert('aqui');
				//top.alert('message'); 
				<a href="http://www.w3schools.com">Visit W3Schools</a>
			}
		</script>
	</head>
	<body>
		<div class="container4">
			<p style="float:left;margin-left: 80px;margin-top:50px; ">
				<font style="font-family: 'Terminator Real NFI';font-size: 30px; color: white;">VT</font>
				<font style="font-family: 'Terminator Real NFI';font-size: 30px;color:#d0d000;">CONVERSOR</font>
				<!--<br />
				<font style="font-family: 'Terminator Real NFI';font-size: 20px;color:#959500;">Login</font>-->
			</p>
			<br clear="all">
			<form method="post" action="login.php">
				<table class="center">
					<tr>
						<td style="color: #75755f;text-align: right;"><?php echo $textoLogin; ?></td>
						<td style="text-align: left;"><input type="email" name="email" id="email" required="required"/> <br/></td>
					</tr>
					<tr>
						<td style="color: #75755f; text-align: right;"><?php echo $textoSenha; ?></td>
						<td style="text-align: left;"><input type="password" name="psw" id="psw" required="required"/></td>
					</tr>
				</table>			
				<br clear="all" />
				<p>
					<input class="btn1" type="submit" style="width: 100px;" value="<?php echo $btnLogin;?>" />
					<input class="btn1" type="button" style="width: 100px;" onclick="location.href='cadastrar.php';" value="<?php echo $btnCadastrar;?>" />	
				</p>
			</form>		
		</div>
	</body>
</html> 
<?php
	if ($_REQUEST['erro'] == '1') {
		?>
		<script>
			document.getElementById('email').focus();
			$.notify("Erro, favor tentar novamente mais tarde",   
					{
						// Esconder a notificação com clique
					  	clickToHide: true,
					  	// Automaticamente esconder a notificação
					  	autoHide: true,
					  	// Se automatico, esconde apos 4 segundos
					  	autoHideDelay: 4000,
					  	// Posição 
					  	globalPosition: 'top left',
					  	// Estilo
					  	style: 'bootstrap',
					  	// Classe erro, aviso ...
					  	className: 'error',
					  	showAnimation: 'slideDown',
					  	showDuration: 400
					});
		</script>
	<?php
	} else if ($_REQUEST['erro'] == '2') {	
	?>
		<script>
			document.getElementById('email').focus();
			$.notify("Login ou senha incorretos",   
					{  	clickToHide: true,
					  	autoHide: true,
					  	autoHideDelay: 4000,
					  	globalPosition: 'top left',
					  	style: 'bootstrap',
					  	className: 'error',
					  	showAnimation: 'slideDown',
					  	showDuration: 400
					});
		</script>
		<?php
	} else if ($_REQUEST['erro'] == '3') {	
	?>
		<script>
			document.getElementById('email').focus();
			$.notify("Erro ao acessar a página, usuário não conectado",   
					{  	clickToHide: true,
					  	autoHide: true,
					  	autoHideDelay: 4000,
					  	globalPosition: 'top left',
					  	style: 'bootstrap',
					  	className: 'error',
					  	showAnimation: 'slideDown',
					  	showDuration: 400
					});
		</script>
		<?php
	} else if ($_REQUEST['erro'] == '4') {	
	?>
		<script>
			document.getElementById('email').focus();
			$.notify("Usuário cadastrado com sucesso",   
					{  	clickToHide: true,
					  	autoHide: true,
					  	autoHideDelay: 4000,
					  	globalPosition: 'top left',
					  	style: 'bootstrap',
					  	className: 'sucess',
					  	showAnimation: 'slideDown',
					  	showDuration: 400
					});
		</script>
		<?php
	}
?>
