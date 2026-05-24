document.addEventListener("DOMContentLoaded", ()=>{

    const contenedor =
    document.getElementById("contenedor-lenguas");

    const btnAgregar =
    document.getElementById("btnAgregarLengua");

    async function cargarMunicipios(
        selectLengua,
        selectMunicipio
    ){

        const idLengua =
        selectLengua.value;

        if(idLengua === ""){

            selectMunicipio.innerHTML =
            '<option value="">Municipio</option>';

            return;
        }

        const response =
        await fetch(
            `/intervoz/api/obtenerMunicipios.php?id_lengua=${idLengua}`
        );

        const municipios =
        await response.json();

        selectMunicipio.innerHTML =
        '<option value="">Municipio</option>';

        municipios.forEach(m=>{

            const option =
            document.createElement("option");

            option.value =
            m.id_municipio;

            option.textContent =
            m.nombre;

            selectMunicipio.appendChild(option);

        });

    }

    function asignarEventos(bloque){

        const lengua =
        bloque.querySelector('.lengua-select');

        const municipio =
        bloque.querySelector('.municipio-select');

        lengua.addEventListener(
            'change',
            ()=> cargarMunicipios(
                lengua,
                municipio
            )
        );

    }

    asignarEventos(
        document.querySelector('.bloque-lengua')
    );

    btnAgregar.addEventListener('click', ()=>{

        const nuevo =
        document.querySelector('.bloque-lengua')
        .cloneNode(true);

        nuevo.querySelector('.lengua-select').value = '';
        nuevo.querySelector('.municipio-select').innerHTML =
        '<option value="">Municipio</option>';

        contenedor.appendChild(nuevo);

        asignarEventos(nuevo);

    });

});