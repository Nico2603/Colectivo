<?php
session_start();
// Si el usuario no esta logeado
if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.php');
	exit;
}
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'Colectivo';
$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('No se pudo conectar al servidor: ' . mysqli_connect_error());
}
//Traer los datos del usuario del servidor
$stmt = $conexion->prepare('SELECT tipoUsuario, correoUsuario, contrasenaUsuario FROM usuarios WHERE idUsuario = ?');
// Traer datos con el id del usuario
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($tipoUsuario, $correoUsuario, $contrasenaUsuario);
$stmt->fetch();
$stmt->close();
$user = $tipoUsuario;
$correo = $correoUsuario;
$contra = $contrasenaUsuario;
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Pagina</title>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Titulo</h1>
				<a href="perfil.php"><i class="fas fa-user-circle"></i>Perfil</a>
				<a href="salir.php"><i class="fas fa-sign-out-alt"></i>Salir</a>
			</div>
		</nav>
		<div class="content">
			<h2>Perfil del usuario</h2>
			<div>
				<p>Tu informacion de cuenta:</p>
				<table>
					<tr>
						<td>Nombre de Usuario:</td>
						<td><?=$_SESSION['name']?></td>
					</tr>
					<tr>
						<td>Contrasena:</td>
						<td><?=$contra?></td>
					</tr>
					<tr>
						<td>Correo:</td>
						<td><?=$correo?></td>
					</tr>
					<tr>
						<td>Usuario:</td>
						<td><?=$user?></td>
					</tr>
				</table>
			</div>
			<form action="../controlador/crear_cuentas.php" method="post">

<h2>Crear Cuenta</h2>

<div class="input-box">
	<span class="icon"><i class='bx bxs-user'></i></span>
	<input type="text" required name="nombre">
	<label >Nombre de Usuario</label>
</div>
<div class="input-box">
	<span class="icon"><i class='bx bxs-envelope'></i></span>
	<input type="text" required name="correo">
	<label >Correo</label>
</div>
<div class="input-box">
	<span class="icon"><i class='bx bxs-lock-alt' ></i></span>
	<input type="password" required name="contrasena">
	<label>Contrasena</label>
</div>
<div class="input-box">
	<span class="icon"><i class='bx bxs-lock-alt' ></i></span>
	<input type="text" required name="tipoUsuario">
	<label>Tipo de Usuario</label>
</div>
<button class="btn" name="Crear_Cuenta">Crear Cuenta</button>

</form>
		</div>
	</body>
</html>