<!DOCTYPE html>
<html>
<head>
  <title>Calculadora de Modulación FM</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
    }
    
    h1 {
      color: #333;
    }
    
    form {
      margin-top: 20px;
    }
    
    label {
      display: block;
      margin-bottom: 10px;
    }
    
    input[type="text"] {
      width: 200px;
    }
    
    input[type="submit"] { 
        border:none; 
        display: inline-block;
        padding: 10px 20px;
        background-color: #AC094C; 
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s;
        cursor: pointer;
    }
    
    #result {
      margin-top: 20px;
      font-weight: bold;
    }
  </style>
  <script>
    function calculateModulation() {
      let deltaf = parseFloat(document.getElementById('carrier-frequency').value);
      let fsubm  = parseFloat(document.getElementById('modulating-frequency').value);

      if(deltaf > 0 && fsubm > 0 ){
        let m = (deltaf / fsubm);
        let mporcent = (deltaf / fsubm)*100;
        document.getElementById('modulation').textContent = mporcent.toFixed(2) + "%";
        document.getElementById('modulation-index').textContent = m.toFixed(2);
      }
      else alert("Ingrese un valor valido.")
    }
  </script>

<?php
 function registrarAuditoria($descripcion) {
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

  $descAbre = "El usuario ".$nombreUsuario.", ".$descripcion;

  // Insertar el registro de auditoría
  $stmt = $con->prepare("INSERT INTO logs (?) values (?)");
  $stmt->bind_param('s', $descAbre);
  $stmt->execute();
  $stmt->close();
  $con->close();
}

  if(isset( $_POST['deltaf']) && isset($_POST['fsubm'])){  
    
  $deltaf = $_POST['deltaf'];
  $fsubm = $_POST['fsubm'];
  if($deltaf>0 && $fsubm>0){
  $m = $deltaf / $fsubm;
  $mporcent = ($deltaf / $fsubm)*100;

    $descripcion="ha realizado una consulta de modulacion con los siguientes datos de entrada:\n". 
     "deltaF= $deltaf".", F_m= .$fsubm."."Los cuales han dado los siguientes resultados:\n".
     "m= $m"."M= $mporcent";

      registrarAuditoria($descripcion); 
    }
  }
?>

</head>
<body>
  <h1>Calculadora de Modulación FM</h1>
  
  <form id="fm-form" onsubmit="event.preventDefault();" method="post" action="modulacion.php">
    <label for="carrier-frequency">Frecuencia del portador (en Hz):</label>
    <input type="text" id="carrier-frequency"  name="deltaf" required>
    
    <label for="modulating-frequency">Frecuencia de modulación (en Hz):</label>
    <input type="text" id="modulating-frequency" name="fsubm"required>
    
    <input type="submit" value="Calcular" onclick="calculateModulation()">
  </form>
  
  <div id="result">
    <p>Índice de Modulación: <span id="modulation-index"></span></p>
    <p>Porcentaje de modulación: <span id="modulation"></span></p>
  </div>
</body>
</html>
