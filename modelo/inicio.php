<?php
session_start();
$idr = $_SESSION['id'];
// Si el usuario no está logeado:
if (!isset($_SESSION['loggedin'])) {
	header('Location: ./index.php');
	exit;
}
if (isset($_POST['accion']) && isset($_POST['descripcion'])) {
  $act = $_POST['accion'];
  $desc = $_POST['descripcion'];
  // Llamar a la función registrarAuditoria con los datos recibidos
  registrarAuditoria($act, $desc, $idr);
}

function registrarAuditoria($accion, $descripcion, $id) {
  $DATABASE_HOST = 'localhost';
  $DATABASE_USER = 'root';
  $DATABASE_PASS = '';
  $DATABASE_NAME = 'Colectivo';

  // Crear la conexión a la base de datos
  $con = new mysqli($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

  if ($con->connect_errno) {
    exit('No se pudo conectar al servidor: ' . $con->connect_error);
  }

  if ($stmt = $con->prepare('SELECT nombreUsuario FROM usuarios WHERE idUsuario = ?')) {
    $stmt->bind_param('i', $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($nombreUsuario);
    $stmt->fetch();
    $stmt->close();
  }

  // Modificar la descripción según la página abierta
  if ($descripcion == 'Telecomunicaciones y Circuitos') {
    $descripcion = ', entró a la página de Telecomunicaciones y Circuitos.';
  } elseif ($descripcion == 'Programación y Bases de datos') {
    $descripcion = ', entró a la página de Programación y Bases de datos.';
  } elseif ($descripcion == 'Referencias') {
    $descripcion = ', entró a la página de Referencias.';
  }
  $descAbre = ", ".$descripcion;

  $test = ucfirst($nombreUsuario . $descripcion);
  // Insertar el registro de auditoría
  $stmt = $con->prepare('INSERT INTO logs_auditoria (fechaLogs_auditoria, horaLogs_auditoria, accionLogs_auditoria, descripcionLogs_auditoria, idUsuario, nombreUsuario) VALUES (CURDATE(), CURTIME(), ?, ?, ?, ?)');
  $stmt->bind_param('ssis', $accion, $test, $id, $nombreUsuario);
  $stmt->execute();
  $stmt->close();
  $con->close();
}
?>
<script>
function cargarIframe(url, descripcion) {
  let contenidoIframe = '<iframe src="' + url + '" width="100%" height="100%" frameborder="0" allowfullscreen></iframe>';
  let seccionPrincipal = document.querySelector('.home-section');
  seccionPrincipal.innerHTML = contenidoIframe;
  
  //test cambiar de accion
  let iframe = document.querySelector('.home-section iframe');
  console.log('iframe:', iframe);
  iframe.addEventListener('load', function() {
  let iframeURL = iframe.contentWindow.location.href;
  console.log('iframeURL:', iframeURL);
  
  let accion;
  if (iframeURL.includes('/vista/modulacion.php')) {
    accion = 'Se abre la página de Modulación';
  } else if (iframeURL.includes('/vista/progra.html')) {
    accion = 'Se abre Marco Teórico de la página Programación y Bases de datos';
  } else if (iframeURL.includes('/vista/espectro.html')){
    accion = 'Se abre la página de la Gráfica del espectro';
  } else if (iframeURL.includes('/vista/teleco.html')){
    accion = 'Se abre Marco Teórico de la página Telecomunicaciones y Circuitos';
  } else if (iframeURL.includes('/vista/referencias.html')){
    accion = 'Se abre Marco Teórico de la página Referencias';
  } else if (iframeURL.includes('ajustes.php')){
    accion = 'Se abre la página de ajustes de Usuario (Admin)';
  } else if (iframeURL.includes('perfil.php')){
    accion = 'Se abre la página del perfil de usuario (Admin)';

  } else if (iframeURL.includes('ajustes.php')){
    accion = 'Se abre la página de Perfiles de Usuarios (Admin)';
    
  } else if (iframeURL.includes('ganancia.php')){
    accion = 'Se abre la página para calcular Ganancia';
  } else if (iframeURL.includes('historial.php')){
    accion = 'Se abre la página para consultar el Historial (Admin)';
  } else if (iframeURL.includes('/controlador/consultarRango.php')){
    accion = 'Se abre la página para consultar Rangos de Frecuencias';
  } else {
    accion = 'Se abre Marco Teórico';
  }

  // Realizar una solicitud AJAX para enviar los datos a PHP
  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'inicio.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('accion=' + encodeURIComponent(accion) + '&descripcion=' + encodeURIComponent(descripcion));
});

}
  //registrarAuditoria('Se abre Marco Teórico', descripcion, id);
</script>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="../css/dashboard.css" type="text/css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    body {
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    table {
      margin: auto;
    }
    td {
      padding: 10px;
      border: 2px solid black;
      border-color: #5A062F;
    }
  </style>
</head>
<body>
  <div class="sidebar close">
    <div class="logo-details">
      <i class='bx bxl-xing'></i>
      <span class="logo_name">
        Trabajo Colectivo
      </span>
    </div>
    <ul class="nav-links">
      <li>
        <a href="inicio.php">
          <i class='bx bx-grid-alt'></i>
          <span class="link_name">Dashboard</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="inicio.php">Inicio</a></li>
        </ul>
      </li>
      <li>
        <div class="iocn-link">
          <a href="#">
            <i class='bx bx-book-alt'></i>
            <span class="link_name">Marco⠀teórico</span>
          </a>
          <i class='bx bxs-chevron-down arrow'></i>
        </div>
        <ul class="sub-menu">
          <li><a class="link_name" href="#">Marco teórico</a></li>
          <li><a href="#" onclick="cargarIframe('../vista/teleco.html', 'Telecomunicaciones y Circuitos')">Telecomunicaciones y Circuitos</a></li>
          <li><a href="#" onclick="cargarIframe('../vista/progra.html', 'Programación y Bases de datos')">Programación y Bases de datos</a></li>
          <li><a href="#" onclick="cargarIframe('../vista/referencias.html', 'Referencias')">Referencias</a></li>
        </ul>
      </li>
      <li>
        <a href="#" onclick="cargarIframe('../vista/modulacion.php', ', entró a la página de Modulación.')">
          <i class='bx bx-pie-chart-alt-2'></i>
          <span class="link_name">Modulación</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="#" onclick="cargarIframe('../vista/modulacion.php', ', entró a la página de Modulación.')">Modulación</a></li>
        </ul>
      </li>
      <li>
        <a href="#" onclick="cargarIframe('../modelo/ganancia.php', ', entró a la página de Ganancia.')">
          <i class='bx bx-bar-chart-alt-2'></i>
          <span class="link_name">Ganancia</span>
        </a>
        <ul class="sub-menu blank">
          <li><a href="#" class="link_name" onclick="cargarIframe('../modelo/ganancia.php', ', entró a la página de Ganancia.')">Calcular Ganancia</a></li>
        </ul>
      </li>
      <li>
        <a href="#" onclick="cargarIframe('../controlador/consultarRango.php', ', entró a la página de Consultas de Rango.')">
          <i class='bx bx-line-chart'></i>
          <span class="link_name">Rango de Frecuencias</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="#" onclick="cargarIframe('../controlador/consultarRango.php', ', entró a la página de Consultas de Rango.')">Rango de Frecuencias</a></li>
        </ul>
      </li>
      <li>
        <a href="#" onclick="cargarIframe('historial.php', ', entró a la página de Historial.')">
          <i class='bx bx-history'></i>
          <span class="link_name">Historial</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="#" onclick="cargarIframe('historial.php', ', entró a la página de Historial.')">Historial</a></li>
        </ul>
      </li>
      <li>
        <a href="#" onclick="cargarIframe('./perfil.php', ', entró a la página de Perfil.')">
          <i class='bx bx-cog'></i>
          <span class="link_name">Ajustes</span>
        </a>
        <ul class="sub-menu blank">
          <li><a class="link_name" href="#" onclick="cargarIframe('./ajustes.php', ', entró a la página de ajustes para ver Usuarios.')">Ajustes</a></li>
        </ul>
      </li>
      <li>
        <div class="profile-details">
          <div class="profile-content">
            <img src="http://localhost/php/Colectivo/images/blandonferxxo.png" alt="profileImg">
          </div>
          <div class="name-job">
            <div class="profile_name"><?=$_SESSION['name']?></div>
            <div class="job">Admin</div>
          </div>
          <a href="salir.php"><i class='bx bx-log-out'></i></a>
        </div>
      </li>
    </ul>
  </div>
  <section class="home-section">
    <div class="home-content">
      <i class='bx bx-menu'></i>
      <span class="text">Dashboard</span>
    </div>
    <section class="content">
      <h2 style="text-align: center;">Implementación de un transmisor FM y otras generalidades del espectro electromagnético</h2>
      <h3 style="text-align: center;">FM transmitter implementation and other electromagnetic spectrum generalities</h3>
      <p style="text-align: center;">C.Castañeda, N. Ceballos, J. Garcia <br>
      Universidad Católica de Pereira
      </p>
      <table>
        <tr>
          <td>
            <img src="../images/Foto2.jpeg" alt="Imagen 1" style="width: 478px; height: 378px;">
          </td>
          <td>
            <img src="../images/Foto3.jpeg" alt="Imagen 2" style="width: 478px; height: 378px;">
          </td>
        </tr>
        <tr>
          <td>
            <img src="../images/Foto4.jpeg" alt="Imagen 3" style="width: 478px; height: 358px;">
          </td>
          <td>
            <img src="../images/Foto5.jpeg" alt="Imagen 4" style="width: 478px; height: 358px;">
          </td>
        </tr>
      </table>
    </section>
  </section>
  <script>
    let n = '<?php echo $_SESSION['name']?>';
    alert('Bienvenido ' + n);
  </script>
  <script>
    function salir() {
      let n = '<?php echo $_SESSION['name']?>';
      alert('Adiós ' + n);
      window.location.href = 'salir.php';
    }
  </script>
  <script src="../vista/inicio.js"></script>
</body>
</html>
