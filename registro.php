<?php include 'conexion.php'; 

$rol = $_GET['rol'] ?? 'solicitante';

// Obtener lenguas
$sqlLenguas = "SELECT * FROM lengua ORDER BY nombre ASC";
$resultLenguas = mysqli_query($conn, $sqlLenguas);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>INTERVOZ - Crear Cuenta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card" style="width: 420px;">
    <h2>Crear Cuenta</h2>
    <p style="margin: 5px 0; text-align:center; font-size: 0.9em; color: #666;">
        <?=
            $rol == 'interprete'
            ? 'Registro de interprete'
            : 'Registro de solicitante'
        ?>
    </p>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nombre" placeholder="Nombre(s)" required>
        <div style="display: flex; gap: 5px;">
            <input type="text" name="ape_pat" placeholder="Apellido Pat." required>
            <input type="text" name="ape_mat" placeholder="Apellido Mat." required>
        </div>
        <input type="email" name="correo" placeholder="Correo electrónico" required>
        <input type="password" name="pass" placeholder="Contraseña (max. 16 caracteres)"minlength="8" maxlength="16" required>

        <?php if($rol == 'interprete'){ ?>
            <input type="tel" name="telefono" placeholder="Teléfono" required>

            <div id="contenedor-lenguas">

                <div class="bloque-lengua">

                    <select name="lenguas[]" class="lengua-select" required>
                        <option value="">Lengua</option>
                        <?php
                        mysqli_data_seek($resultLenguas, 0);
                        while($lengua = mysqli_fetch_assoc($resultLenguas)){
                        ?>
                        <option value="<?= $lengua['id_lengua'] ?>">
                            <?= $lengua['nombre'] ?>
                        </option>
                        <?php } ?>
                    </select>
                    <select
                        name="municipios[]"
                        class="municipio-select"
                        required
                    >
                        <option value="">Municipio</option>
                    </select>
                </div>
            </div>

            <button type="button" id="btnAgregarLengua" class="btn-municipio">✚</button>

            <p style="margin:0 0 10px 0;">- Documentos -</p>
            <div class="documentos-grid">
                <div class="subir-archivo">
                    <input id="ine" type="file" name="INE" accept=".jpg,.jpeg,.png,.pdf" hidden> <!-- PONER REQUIRED -->
                    <label for="ine" class="btn-subir">
                        <span>⬆</span>
                        <span>Subir</span>
                    </label>
                    <p class="documento-desc">INE</p>
                </div>

                <div class="subir-archivo">
                    <input id="certificado" type="file" name="documento" accept=".jpg,.png,.pdf" hidden> <!-- PONER REQUIRED -->
                    <label for="certificado" class="btn-subir">
                        <span>⬆</span>
                        <span>Subir</span>
                    </label>
                    <p class="documento-desc">Certificado de interprete</p>
                </div>
            </div>

        <?php }?>

        <div class="aviso">
            <input type="checkbox" name="avisoPrivacidad" style="width: 20px; cursor:pointer;" required>
            <a href="avisoPrivacidad.php" target="_blank">Aviso de privacidad</a>
        </div>

        <button type="submit" name="registrar">Registrarse</button>
    </form>

    <div class="btn-volver">
        <a href="registroOpciones.html" style="color: #666; text-decoration: none;">← Volver</a>
    </div>

    <?php
    if(isset($_POST['registrar'])){
        // Limpiamos los datos para evitar errores básicos
        $nombre  = mysqli_real_escape_string($conn, $_POST['nombre']);
        $ape_pat = mysqli_real_escape_string($conn, $_POST['ape_pat']);
        $ape_mat = mysqli_real_escape_string($conn, $_POST['ape_mat']);
        $correo  = mysqli_real_escape_string($conn, $_POST['correo']);
        $pass    = mysqli_real_escape_string($conn, $_POST['pass']);
        
        //$tel     = $_POST['telefono'] ?? '';


        // Query basado exactamente en las columnas de tu tabla 'Usuario'
        $sql = "INSERT INTO Usuario
                (nombre, ape_pat, ape_mat, correo, contraseña) 
                VALUES
                ('$nombre', '$ape_pat', '$ape_mat', '$correo', '$pass')";

        if(mysqli_query($conn, $sql)){
            $id_usuario = mysqli_insert_id($conn);
            $sqlPerfil = "INSERT INTO Perfil
                            (rol, estado, fecha_creacion, id_usuario)
                            VALUES
                            ('$rol', 'activo', NOW(), '$id_usuario')";

            mysqli_query($conn, $sqlPerfil);

            if($rol == 'interprete'){

                $telefono   = mysqli_real_escape_string($conn, $_POST['telefono']);
                $lenguas = $_POST['lenguas'];
                $municipios = $_POST['municipios'];

                // =========================
                // TABLA interprete
                // =========================

                $sqlInterprete = "INSERT INTO interprete
                                    (id_usuario, telefono)
                                    VALUES
                                    ('$id_usuario', '$telefono')";

                if(mysqli_query($conn, $sqlInterprete)){
                    $id_interprete = mysqli_insert_id($conn);
                    for($i = 0; $i < count($lenguas); $i++){
                        $id_lengua =
                        intval($lenguas[$i]);
                        $id_municipio =
                        intval($municipios[$i]);
                        $sqlILM = "
                        INSERT INTO interprete_lengua_municipio(
                            id_interprete,
                            id_lengua,
                            id_municipio
                        )
                        VALUES(
                            '$id_interprete',
                            '$id_lengua',
                            '$id_municipio'
                        )
                        ";
                        mysqli_query($conn, $sqlILM);
                    }
                    

                    // =========================
                    // TABLA disponibilidad
                    // =========================

                    $sqlDisponibilidad = "INSERT INTO disponibilidad
                                            (id_interprete, disponible, fecha)
                                            VALUES
                                            ('$id_interprete', 1, NOW())";
                    
                    mysqli_query($conn, $sqlDisponibilidad);
                }
            }

            echo "<script>
                        alert('Cuenta creada con éxito. Ya puedes iniciar sesión.');
                        window.location='index.php';
                    </script>";
        } else {
            echo "<p style='color:red; text-align:center;'>
                    Error al registrar: " . mysqli_error($conn) . "
                    </p>";
        }
    }
    ?>
</div>
<script src="assets/js/registro.js"></script>
</body>
</html>