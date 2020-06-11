const api="http://localhost:80/pages/";
//Peticion General
async function REQUEST(url, method, params) {
    let options = {
        method: method
    };

    if (method !== 'GET') {
        options['headers'] = { 'Content-Type': 'application/json' };
        options['body'] = JSON.stringify(params);
    }

    try {
        let response = await fetch(url, options);

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
        return JSON.stringify([]);
    }
}

//SAVES-POST
function savePublicacion(jParams) {
    return REQUEST(`${api}Controllers/publicacionesControllers`, 'POST', jParams);
}

//UPDATES-PUT-PATCH

//GETS
function getPublicacionById(jParams) {
    return REQUEST(`Controllers/publicacionesControllers`, 'GET', jParams);
}

function getPublicacionByCategoria(jParams) {
    return REQUEST(`Controllers/publicacionesControllers`, 'GET', jParams);
}

function getPublicacionesByVendedor(jParams) {
    return REQUEST(`Controllers/publicacionesControllers`, 'GET', jParams);
}

function getPublicacionesByComprador(jParams) {
    return REQUEST(`Controllers/publicacionesControllers`, 'GET', jParams);
}
