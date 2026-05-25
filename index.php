<?php 
session_start();

if(isset($_SESSION['id_usuario'])){

    if($_SESSION['rol'] == 'interprete'){
        header("Location: pages/interprete/dashboard.php");
        exit();
    } else {
        header("Location: pages/solicitante/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INTERVOZ</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="card">
    <h2>Intervoz</h2>

    <form id="loginForm">
        <input type="email" id="correo" placeholder="Correo" required>
        <input type="password" id="pass" placeholder="Contraseña" required>
        <button type="submit">Entrar</button>
    </form>

    <div id="msg"></div>
</div>

<script>
document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const res = await fetch("https://intervoz-api.onrender.com/conexion.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            correo: document.getElementById("correo").value,
            pass: document.getElementById("pass").value
        })
    });

    const data = await res.json();

    if(data.ok){
        window.location.href = data.redirect;
    } else {
        document.getElementById("msg").innerHTML = "Error login";
    }
});
</script>

</body>
</html>