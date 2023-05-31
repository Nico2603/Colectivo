<?php
session_start();
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'Colectivo';

$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	//Si hay un error mostrar:
	exit('No se pudo conectar al servidor: ' . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['Crear_Cuenta'])){
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $user = trim($_POST['tipoUsuario']);
    $contraHashed = password_hash($contrasena, PASSWORD_DEFAULT);
    if(!$user=='user'){
        $user = 'Admin';
    }
    if ($query = $conexion->prepare("SELECT * FROM usuarios WHERE correoUsuario = ?")){
        $query->bind_param('s',$correo);
        $query->execute();
        $query->store_result();
        if ($query->num_rows > 0){
            ?> <script>alert('El correo ya ha sido registrado!'); </script> <?php
        }
        else if (strlen($contrasena) < 6){
            ?> <script>alert('La contrasena debe tener mas de 7 Caracteres!';); </script> <?php
        }
        else {
            $stmt = $conexion->prepare("INSERT INTO usuarios(nombreUsuario, tipoUsuario, correoUsuario, contrasenaUsuario) VALUES ('$nombre', '$user', '$correo', '$contraHashed');");
            $exec = $stmt->execute();
            if ($exec){
                //De ser posible arreglar este sistema, linea 34-53
                //Aun no tiene logs
                $id = $conexion->insert_id;
                $rangos = array(
                array('rango' => 'Ondas de radio: de 3 kHz a 300 GHz', 'idUsuario' => $id),
                array('rango' => 'Microondas: de 300 MHz a 300 GHz', 'idUsuario' => $id),
                array('rango' => 'Infrarrojo: de 300 GHz a 400 THz', 'idUsuario' => $id),
                array('rango' => 'Luz visible: de 400 THz a 800 THz', 'idUsuario' => $id),
                array('rango' => 'Ultravioleta: de 800 THz a 30 PHz', 'idUsuario' => $id),
                array('rango' => 'Rayos X: de 30 PHz a 30 EHz', 'idUsuario' => $id),
                array('rango' => 'Rayos gamma: más de 30 EHz', 'idUsuario' => $id)
                );

                foreach ($rangos as $rango) {
                    $rango_frecuencias = $rango['rango'];
                    $idUsuario = $rango['idUsuario'];

                    $stmt = $conexion->prepare("INSERT INTO rango_de_frecuencias (rango_de_frecuencias, idUsuario) VALUES (?, ?)");
                    $stmt->bind_param('si', $rango_frecuencias, $idUsuario);
                    $stmt->execute();
                }
                ?> <script>alert('Te has registrado!';); </script> <?php
            }
            else {
                ?> <script>alert('Algo ha salido mal!';); </script> <?php
            }
        }
    }
    ?> <script>alert('Se ha creado la cuenta!'); </script> <?php
    $query->close();
    mysqli_close($conexion);
}
?>