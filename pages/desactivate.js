function errase() {
    var tokens=JSON.parse(localStorage.getItem('l_sesion'));
    var xhr=new XMLHttpRequest();
    xhr.withCredentials = true;
    xhr.open("PATCH", "../usuarios", true);
    //console.log(tokens.token);
    xhr.setRequestHeader("Authorization", tokens.token);
    xhr.setRequestHeader("Content-Type", "application/json");
    var json={ 
        "id_usuario": tokens.id_usuario, 
        "id_sesion": tokens.id_sesion 
    };
    xhr.send(JSON.stringify(json));
    xhr.addEventListener("readystatechange", function() {
        var mes=JSON.parse(this.responseText);
        alert(mes.messages);
        if (mes.success===true){
            localStorage.removeItem('l_sesion');
            location.href="index.html";
        }
    });
}