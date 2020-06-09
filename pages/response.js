//Peticion General
function REQUEST(url, method, params) {

    if (method !== 'GET') {
        options['headers'] = { 'Content-Type': 'application/json' };
        options['body'] = JSON.stringify(jParams);
    }

    var req = new XMLHttpRequest();
    req.open(method, url, true);

    var json = {};
    req.onload = function() {
        if(req.status == 200) {
            var response = req.responseText;
            json = JSON.parse(response);
        }
        return json;
    }

    req.send();
}

//SAVES-POST
function savePublicacion(jParams) {
    return REQUEST(`Controllers/publicacionesControllers`, 'POST', jParams);
}

//UPDATES-PUT

//GETS