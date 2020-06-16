const newClientData=document.getElementById('signIn');

newClientData.addEventListener('submit', function(e) {
    e.preventDefault();
    var form=new FormData(newClientData);
    userID=localStorage.getItem('ID_User');
    var bank=null;
    var tarjeta=null;
    var ready=true;
    if(form.get('card')==null && form.get('paypal')==null) {
        alert("Debes escoger al menos una forma de pago");
    }
    else {
        if(form.get('card')=='on') {
            bank=form.get('banco');
            if(form.get('credit')==="") {
                alert("Numero de tarjeta de credito/debito requerido");
                ready=false;
            }
            else {
                tarjeta=form.get('credit');
            }
        }
        var user=null;
        if(form.get('paypal')=='on') {
            if(form.get('user')==="") {
                alert("Usuario de PayPal requerido");
                ready=false;
            }
            else {
                user=form.get('user');
                if(form.get('cont')==="") {
                    alert("Contraseña de PayPal requerida");
                    ready=false;
                }
            }
        }
        if(ready===true){
            var json={ 
                "usuario_id": localStorage.getItem('ID_User'), 
                "cuenta_paypal": user, 
                "cuenta_tarjeta": tarjeta
            };  
            console.log(json);
            var xhr=new XMLHttpRequest();
            xhr.withCredentials=true;
            xhr.open("POST", "../clientes");
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(JSON.stringify(json));
            xhr.addEventListener("readystatechange", function() {
                var mes=JSON.parse(this.responseText);
                if (mes.success===false){
                    alert(mes.messages);
                }
                else {
                    if(this.readyState===4) {
                        if(form.get('card')=='on') {
                            json={ 
                                "usuario_id": userID, 
                                "banco": bank, 
                                "numero_cuenta": tarjeta
                            };
                            var xhrT=new XMLHttpRequest();
                            xhrT.withCredentials=true;
                            xhrT.open("POST", "../tarjetas");
                            xhrT.setRequestHeader("Content-Type", "application/json");
                            xhrT.send(JSON.stringify(json));
                            xhrT.addEventListener("readystatechange", function() {
                                mes=JSON.parse(this.responseText);
                                if (mes.success===false){
                                    alert(mes.messages);
                                    ready=false;
                                }
                            });
                        }
                        if(ready===true) {
                            var rol=localStorage.getItem('Rol');
                            if(rol==2 || rol==4) {
                                var xhrR=new XMLHttpRequest();
                                xhrR.withCredentials=true;
                                json={ 
                                    "usuario_id": userID, 
                                    "compras_id": null
                                };
                                xhrR.open("POST", "../compradores");
                                xhrR.setRequestHeader("Content-Type", "application/json");
                                xhrR.send(JSON.stringify(json));
                                xhrR.addEventListener("readystatechange", function() {
                                    mes=JSON.parse(this.responseText);
                                    if (mes.success===false){
                                        alert(mes.messages);
                                        ready=false;
                                    }
                                });
                            }
                            if((rol==3 || rol==4) && ready===true) {
                                xhr=new XMLHttpRequest();
                                xhr.withCredentials=true;
                                json={ 
                                    "usuario_id": userID, 
                                    "publicaciones_id": null
                                };
                                xhr.open("POST", "../vendedores");
                                xhr.setRequestHeader("Content-Type", "application/json");
                                xhr.send(JSON.stringify(json));
                                xhr.addEventListener("readystatechange", function() {
                                    mes=JSON.parse(this.responseText);
                                    if (mes.success===false){
                                        alert(mes.messages);
                                        ready=false;
                                    }
                                });
                            }
                            if(ready===true) {
                                var xhttp=new XMLHttpRequest();
                                xhttp.withCredentials=true;
                                xhttp.open("POST", "../sesiones", true);
                                xhttp.setRequestHeader("Content-Type", "application/json");
                                xhttp.onload=function() {
                                    if (this.status == 201) {
                                        var data=JSON.parse(this.responseText);
                                        if (data.success === true){
                                            localStorage.setItem('l_sesion', JSON.stringify(data.data));
                                            //location.href="index.html";
                                        }
                                    }
                                    else {
                                        var data=JSON.parse(this.responseText);
                                        alert(data.messages);
                                        ready=false;
                                    }
                                };
                                if(ready===true) {
                                    var nombre_usuario=localStorage.getItem('User_Name');
                                    var contrasena=localStorage.getItem('Password');
                                    json={ 
                                        "nombre_usuario": nombre_usuario, 
                                        "contrasena": contrasena 
                                    };
                                    xhttp.send(JSON.stringify(json));
                                    xhttp.addEventListener("readystatechange", function() {
                                        mes=JSON.parse(this.responseText);
                                        if (mes.success===true){
                                            //alert(mes.messages);
                                            localStorage.removeItem('Password');
                                            location.href="index.html";
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }
    }
});