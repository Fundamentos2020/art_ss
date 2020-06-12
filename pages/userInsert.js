const newUserData=document.getElementById('signIn');
const cred=document.getElementById('cred');
const pay=document.getElementById('deb');

newUserData.addEventListener('submit', function(e) {
    e.preventDefault();
    localStorage.setItem('Password', document.getElementById('Contrasena').value);
    var f=new Date();
    var fechaA=String(f.getFullYear())+"-";
    if((f.getMonth()+1)<10) {
        fechaA+="0";
    }
    fechaA+=String(f.getMonth()+1)+"-";
    if(f.getDate()<10) {
        fechaA+="0";
    }
    fechaA+=String(f.getDate())+" "+String(f.getHours())+":"+String(f.getMinutes());
    var form=new FormData(newUserData);
    var r=0;
    switch(form.get('user-type')) {
        case "user-buyer": 
            r=2;
            break;
        case "user-seller":
            r=3;
            break;
        case "user-buyandsell":
            r=4;
            break;
    }
    var idRol=0;
    var xhrP=new XMLHttpRequest();
    xhrP.withCredentials=true;
    xhrP.open("POST", "http://localhost:80/ejercicios-php/art_ss-master/roles/"+r);
    xhrP.addEventListener("readystatechange", function() {
        if(this.readyState===4) {
            var rol=JSON.parse(this.responseText);
            idRol=rol.data.id;
            //console.log(idRol);
            var json={ 
                "nombre": form.get('nombre'), 
                "rol_id": idRol, 
                "pais": form.get('pais'), 
                "fecha_alta": fechaA, 
                "calle": form.get('calle'), 
                "colonia": form.get('colonia'), 
                "numero_exterior": form.get('numero'), 
                "codigo_postal": form.get('CP'), 
                "estado": form.get('estado'), 
                "status": "ACTIVO", 
                "email": form.get('email'), 
                "contrasena": form.get('contraseña'), 
                "foto_perfil": null
            };
            var xhr=new XMLHttpRequest();
            xhr.withCredentials=true;
            xhr.open("POST", "http://localhost:80/ejercicios-php/art_ss-master/usuarios");
            xhr.setRequestHeader("Content-Type", "application/json");
            //console.log(json);
            xhr.send(JSON.stringify(json));
            xhr.addEventListener("readystatechange", function() {
                var mes=JSON.parse(this.responseText);
                console.log(mes);
                if (mes.success===false){
                    alert(mes.messages);
                }
                else {
                    localStorage.setItem('ID_User', mes.data.id);
                    localStorage.setItem('Rol', r);
                    localStorage.setItem('User_Name', mes.data.nombre);
                    location.href="SignInVendedorAdvanced.html";
                }
            });
        }
    });
    xhrP.send();
})