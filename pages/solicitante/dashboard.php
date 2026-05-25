<?php 
session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../../index.php");
    exit();
}

if($_SESSION['rol'] != 'interprete'){
    header("Location: ../../index.php");
    exit();
}

include "../../conexion.php"; // 🔥 ESTO TE FALTABA

$sqlInterprete = "
SELECT id_interprete
FROM interprete
WHERE id_usuario = '".$_SESSION['id_usuario']."'
";

$resultInterprete = mysqli_query($conn, $sqlInterprete);

if(!$resultInterprete || mysqli_num_rows($resultInterprete) <= 0){
  die("Interprete no encontrado");
}

$interprete = mysqli_fetch_assoc($resultInterprete);
$id_interprete = $interprete['id_interprete'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>Intervoz</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<div class="card" style="width:600px; height:550px; position: relative;">
    
    <div style="position: absolute; top: -25px; right: 10px; font-weight: bold; color: #555;">
        Solicitante
    </div>

    <div class="user-header" style="margin-bottom: 30px;">
        <div class="avatar" id="avatar-solic" style="font-size: 32px;">👤</div>
        <div class="user-info">
            <div class="user-name" id="name-solic" style="font-size: 28px; font-weight: normal;">
                <?= !empty($_SESSION['nombre']) ? $_SESSION['nombre'] : 'User' ?>
            </div>
        </div>
        <button class="logout-btn" onclick="window.location.href = '../../logout.php'" title="Cerrar sesión" style="margin-left: auto;">✕</button>
    </div>

    <div class="quick-action">
        
        <div class="form-group" style="margin-bottom: 20px;">
            <select id="lenguas" name="lenguas" style="width:100%; padding:12px; margin-bottom:12px; cursor:pointer;" required>
                <option value="">Lengua</option>
                <?php
                while($lengua = mysqli_fetch_assoc($resultLenguas)){
                ?>
                    <option value="<?= $lengua['id_lengua'] ?>" >
                        <?= $lengua['nombre'] ?>
                    </option>
                <?php }?>
            </select>
            <div class="lang-list" id="lang-list-solic" style="display:none;"></div>
        </div>

        <div class="form-group" style="display: flex; gap: 15px; margin-bottom: 35px; align-items: center;">
            <select id="municipios" name="municipios" style="width:100%; padding:12px; margin-bottom:12px; cursor:pointer;" required>
                    <option value="">Municipio</option>
            </select>
            <button class="btn-municipio">✚</button>
        </div>

        <div id="selected-lang-section" style="display:none">
            <div class="selected-lang-display">
                <div class="selected-lang-name" id="selected-lang-name">—</div>
                <span id="interp-avail-count" style="display:none">0</span>
                <button class="btn btn-sm btn-ghost" onclick="clearLangSelection()">✕</button>
            </div>
        </div>

        <div style="text-align: center;">
            <button class="btn btn-coral" id="btn-llamar" onclick="iniciarLlamadaSolicitante()" style="padding: 12px 50px; font-size: 18px; min-width: 180px;">
                Llamar
            </button>
        </div>

    </div>

    <div class="status-grid" style="display: none;">
        <span id="available-count">0</span>
    </div>

</div>

<script src="../../assets/js/script.js"></script>
<script>

document
.getElementById('lenguas')
.addEventListener('change', async ()=>{

    const idLengua =
    document.getElementById('lenguas').value;

    const response =
    await fetch(
        `../../api/obtenerMunicipios.php?id_lengua=${idLengua}`
    );

    const municipios =
    await response.json();

    const select =
    document.getElementById('municipios');

    select.innerHTML =
    '<option value="">Municipio</option>';

    municipios.forEach(m=>{

        select.innerHTML += `
            <option value="${m.id_municipio}">
                ${m.nombre}
            </option>
        `;

    });

});

</script>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
const socket = io('http://localhost:3000');
</script>
<script>

socket.emit('registrarSolicitante', {
    id_usuario: <?= $_SESSION['id_usuario'] ?>
});

</script>
<script>

localStorage.setItem(
    'rol',
    'solicitante'
);

socket.emit('registrarSolicitante', {
    id_usuario: <?= $_SESSION['id_usuario'] ?>
});

</script>
</body>
</html>