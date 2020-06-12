const logIn=document.getElementById('Access');

logIn.addEventListener('submit', function(e) {
    e.preventDefault();
    var form=new FormData(logIn);
    var xhttp=new XMLHttpRequest();
    xhttp.withCredentials=true;
    xhttp.open("POST", "http://localhost:80/ejercicios-php/art_ss-master/sesiones", true);
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.onload = function() {
        if (this.status == 201) {
            var data = JSON.parse(this.responseText);
            if (data.success === true){
                localStorage.setItem('l_sesion', JSON.stringify(data.data));
            }
        }
        else {
            var data = JSON.parse(this.responseText);
            alert(data.messages);
        }
    };
    var nombre_usuario=form.get('User_Name');
    var contrasena=form.get('Password');
    var json={ 
        "nombre_usuario": nombre_usuario, 
        "contrasena": contrasena 
    };  
    xhttp.send(JSON.stringify(json));
    xhttp.addEventListener("readystatechange", function() {
        var mes=JSON.parse(this.responseText);
        if (mes.success===false){
            alert(mes.messages);
        }
        else {
            location.href="index.html";
        }
    });
});