<?php 
session_start();
include 'conexion.php';

// Verificar si ya hay sesión iniciada
if(isset($_SESSION['id_usuario'])){

    // Redirigir según el rol
    if($_SESSION['rol'] == 'interprete'){
        header("Location: pages/interprete/dashboard.php");
        exit();
    } else if($_SESSION['rol'] == 'solicitante') {
        header("Location: pages/solicitante/dashboard.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }

}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INTERVOZ - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card">
    <h2>Intervoz</h2>
    <p style="text-align:center; font-size: 0.9em; color: #666;">Plataforma de Intérpretes</p>
    
    <form id="loginForm" method="POST">
        <input type="email" name="correo" placeholder="Correo electrónico" required>
        <input type="password" name="pass" placeholder="Contraseña" required>
        <button type="submit" name="ingresar">Entrar</button>
    </form>
    <div class="iniciar-grid">
        <p>¿Aún no tienes una cuenta?</p>
        <a href="registroOpciones.html" class="btn btn-registro">Crear cuenta</a>
    </div>

    <?php
    if(isset($_POST['ingresar'])){
        $correo = $_POST['correo'];
        $pass = $_POST['pass'];

        $query = "
                    SELECT Usuario.*, Perfil.rol
                    FROM Usuario
                    INNER JOIN Perfil
                    ON Usuario.id_usuario = Perfil.id_usuario
                    WHERE correo='$correo'
                    AND contraseña='$pass'
                ";
        $resultado = mysqli_query($conn, $query);

        if(mysqli_num_rows($resultado) > 0){
            $usuario = mysqli_fetch_assoc($resultado);

            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['rol'] = $usuario['rol'];

            if($usuario['rol'] == 'interprete'){
                echo "<script>
                            alert('¡Bienvenido interprete!');
                            window.location='pages/interprete/dashboard.php';
                    </script>";
            } else {
                echo "<script>
                            alert('¡Bienvenido solicitante!');
                            window.location='pages/solicitante/dashboard.php';
                    </script>";
            }
        } else {
            echo "<p style='color:red; text-align:center;'>Datos incorrectos</p>";
        }
    }
    ?>
</div>

<script>
    // Un toque de JS para validar en el cliente antes de enviar
    document.getElementById('loginForm').onsubmit = function() {
        console.log("Validando credenciales...");
    };
</script>

</body>
</html>