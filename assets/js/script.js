// ============================================================
// LOGIN PAGE
// ============================================================
function selectRole(role) {
    if (role === 'interprete'){
        window.location.href = "registro.php?rol=interprete";
    } else {
        window.location.href = "registro.php?rol=solicitante";
    }
}

async function iniciarLlamadaSolicitante(){

    const idLengua =
    document.getElementById("lenguas").value;

    const idMunicipio =
    document.getElementById("municipios").value;

    console.log("Lengua:", idLengua);
    console.log("Municipio:", idMunicipio);

    /*
    ===================================
    OBTENER VARIANTE REAL
    ===================================
    */

    const responseVariante =
    await fetch(
        `../../api/obtenerVariantes.php?id_lengua=${idLengua}&id_municipio=${idMunicipio}`
    );

    const varianteData =
    await responseVariante.json();

    console.log("VARIANTE:");
    console.log(varianteData);

    if(!varianteData.ok){

        alert("No existe variante");

        return;
    }

    const idVariante = varianteData.variantes[0].id_variante;

    /*
    ===================================
    CREAR SOLICITUD
    ===================================
    */

    const response =
    await fetch("../../api/crearSolicitud.php", {
        method: "POST",
        headers:{
            "Content-Type":"application/json"
        },
        body: JSON.stringify({
            id_variante: idVariante
        })
    });

    const data = await response.json();

    console.log("SOLICITUD:");
    console.log(data);

    if(data.ok){

        socket.emit('buscarInterpretes', {
            id_solicitud: data.id_solicitud,
            id_lengua: parseInt(idLengua),
            id_municipio: parseInt(idMunicipio)
        });

        localStorage.setItem(
            'id_solicitud',
            data.id_solicitud
        );

        window.location.href =
        "../call/esperaAceptacion.html";

    }

    

}

/*
    ===================================
    CANCELAR LLAMADA
    ===================================
    */

async function cancelarLlamada(){

    const idSolicitud =
    localStorage.getItem('id_solicitud');

    await fetch(
        '../../api/cancelarSolicitud.php',
        {
            method:'POST',

            headers:{
                'Content-Type':'application/json'
            },

            body:JSON.stringify({
                id_solicitud:idSolicitud
            })
        }
    );

    socket.emit(
        'cancelarLlamada',
        {
            id_solicitud:idSolicitud
        }
    );

    localStorage.removeItem('id_solicitud');
    localStorage.removeItem('roomName');

    window.location.href =
    '../solicitante/dashboard.php';

}