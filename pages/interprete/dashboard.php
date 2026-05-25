<?php 
session_start();

if(!isset($_SESSION['id_usuario'])){
    header("Location: ../../index.php");
    exit();
}

if($_SESSION['rol'] != 'interprete'){
    header("Location: index.php");
    exit();
}

$sqlInterprete = "
SELECT id_interprete
FROM interprete
WHERE id_usuario = '".$_SESSION['id_usuario']."'
";

$resultInterprete = mysqli_query($conn, $sqlInterprete);

if(mysqli_num_rows($resultInterprete) <= 0){
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
<!-- ============================================================
    PAGE: INICIO INTÉRPRETE
    ============================================================ -->

  <div class="card" style="width:600px; height:450px;">
    <!-- Header usuario -->
    <div class="user-header">
      <div class="avatar" id="avatar-interp">👤</div> <!--IMPLEMENTAR INICIAL DEL USUARIO-->
      <div class="user-info">
        <div class="user-name" id="name-interp"><?= $_SESSION['nombre'] ?></div> <!--IMPLEMENTAR NOMBRE DEL USUARIO-->
        <div class="user-role">Voluntario Intérprete</div>
      </div>
      <button class="logout-btn" onclick="window.location.href = '../../logout.php'" title="Cerrar sesión">⊘</button>
    </div>

    <!-- Toggle disponibilidad -->
    <div class="availability-toggle" onclick="toggleAvailability()">
      <div>
        <h3>Disponibilidad</h3>
        <p style="font-size:13px;color:var(--text2);margin-top:3px" id="avail-label">Estoy disponible para recibir llamadas</p>
      </div>
      <div class="toggle-track on" id="avail-toggle">
        <div class="toggle-thumb"></div>
      </div>
    </div>

    <!-- Estado -->
    <div class="status-panel">
      <div class="status-pulse waiting" id="status-pulse">
        <span class="status-icon">👂</span>
      </div>
      <div class="status-text" id="status-text">En espera</div>
      <div class="status-sub" id="status-sub">para recibir llamadas</div>
    </div>

    <!-- Lenguas -->
    <div>
      <p style="font-size:12px;color:var(--text2);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.06em;font-weight:600">Tus lenguas</p>
      <div class="langs-display" id="interp-langs">
        
      </div>
    </div>
  </div>
<script src="../../assets/js/script.js"></script>
<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script>
const socket = io('http://localhost:3000');

// VARIABLES
const ID_INTERPRETE =
<?= $id_interprete ?>;

localStorage.setItem('rol', 'interprete');

/*
========================================
REGISTRAR INTERPRETE
========================================
*/
async function registrarInterprete(){
  try{
    const response =
    await fetch('../../api/obtenerVariantesInterprete.php');

    const data =
    await response.json();

    console.log(data);

    localStorage.setItem(
        'id_interprete',
        ID_INTERPRETE
    );

    localStorage.setItem(
        'lenguas_interprete',
        JSON.stringify(data.lenguas)
    );

    socket.emit('registrarInterprete', {
        id_interprete: ID_INTERPRETE,
        lenguas: data.lenguas
    });
  } catch(error){
    console.error(error);
  }
}

registrarInterprete();

socket.on('llamadaEntrante', (data) => {

    localStorage.setItem(
        'solicitud_actual',
        JSON.stringify(data)
    );

    window.location.href =
    '../call/llamadaEntrante.html';

});

async function toggleAvailability(){

    const toggle =
    document.getElementById('avail-toggle');

    const activo =
    toggle.classList.contains('on');

    const nuevoEstado =
    !activo;

    toggle.classList.toggle('on');

    /*
    ===============================
    API PHP
    ===============================
    */
    try{
      await fetch('../../api/disponibilidad.php', {

          method:'POST',

          headers:{
              'Content-Type':'application/json'
          },

          body: JSON.stringify({
              disponible: nuevoEstado ? 1 : 0
          })
        }
      );

      /*
      ===============================
      SOCKET
      ===============================
      */

      socket.emit('cambiarDisponibilidad', {
          id_interprete: ID_INTERPRETE,
          disponible: nuevoEstado
        }
      );
    }catch(error){
      console.error(error);
    }
}

</script>
</body>
</html>