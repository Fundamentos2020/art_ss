const api="http://localhost:80/art_ss";
//Peticion General

async function REQUEST(url, method, params) {
    'use strict';
    let options = {
        method: method
    };

    if (method !== 'GET') {
        options['headers'] = {
            'Content-Type': 'application/json', 
            'Authorization': params.token ? params.token : 'Basic'
        };
        options['body'] = JSON.stringify(params);
    }

    try {
        let response = await fetch(url, options).catch(console.error);

        if (response.ok) {
            let data = await response.json();
            return data;
        }
        else if (response.status === 404) {
            console.log("Error: No se encontro la ruta especificada en el servidor.");

            return [];
        }
    }
    catch (e) {
        console.log(e);
        return JSON.stringify([]);
    }
}

//SAVES-POST
function savePublicacion(jParams) {
    return REQUEST(`../Controllers/publicacionesController.php`, 'POST', jParams);
}

function savePedido(jParams) {
    return REQUEST(`../Controllers/pedidosController.php`, 'POST', jParams);
}

//UPDATES-PUT-PATCH
function updatePublicacion(jParams) {
    return REQUEST(`../Controllers/publicacionesController.php`, 'PATCH', jParams);
}

//GETS
function getPublicacionById(jParams) {
    return REQUEST(`../Controllers/publicacionesController.php?id=${jParams.id}`, 'GET');
}

function getPublicacionesByCategoria(jParams) {
    return REQUEST(`../Controllers/publicacionesController.php?categoria=${jParams.categoria}`, 'GET');
}

function getPublicacionesByVendedor(jParams) {
    return REQUEST(`../Controllers/publicacionesController.php?vendedor_id=${jParams.vendedor_id}`, 'GET', jParams);
}

function getPublicacionesByComprador(jParams) {
    return REQUEST(`../Controllers/publicacionesController.php?comprador_id=${jParams.comprador_id}`, 'GET', jParams);
}

function getPedidosByComprador(jParams) {
    return REQUEST(`${api}/Controllers/pedidosController.php`, 'GET', jParams);
}

function getPedidosByID (jParams) {
    return REQUEST(`${api}/Controllers/pedidosController.php?&`, 'GET', jParams);
}
