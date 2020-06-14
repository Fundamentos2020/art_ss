//Variables que conoce todo el proyecto
'use strict';
var arrCarrito = [];
var commandName = "CREATE";
const sesion=document.getElementById('inFin');
const sesion2=document.getElementById('inFin2');

/*try {
    document.getElementById('close').addEventListener('click', cerrarSesion());
}
catch {
    console.log("Sin inicio de sesion");
}*/

//Callbacks
window.addEventListener('DOMContentLoaded', function (e) {
    e.preventDefault();
    //document.getElementsByName('carrito')[0].addEventListener('click', fnAgregarAlCarrito);
    //console.log(localStorage.getItem('l_sesion'));
    if(localStorage.getItem('l_sesion')!==null) {
        sesion.innerHTML=`
            <a href="index.html"><span class="item-menu-text"><i class="fa fa-sign-in" aria-hidden="true"></i>CERRAR SESION</span></a>
        `;
        sesion2.innerHTML=`
            <a href="index.html"><span class="item-menu-text"><i class="fa fa-sign-in" aria-hidden="true"></i>CERRAR SESION</span></a>
        `;
    }
});

sesion.addEventListener('click', function(e) {
    //e.preventDefault();
    if(localStorage.getItem('l_sesion')!==null) {
        var tokens=JSON.parse(localStorage.getItem('l_sesion'));
        var xhttp=new XMLHttpRequest();
        xhttp.withCredentials=true;
        xhttp.open("DELETE", "./sesiones/"+tokens.id_sesion, true);
        xhttp.setRequestHeader("Authorization", tokens.token);
        xhttp.send();
        xhttp.addEventListener("readystatechange", function() {
            mes=JSON.parse(this.responseText);
            if (mes.success===false){
                alert(mes.messages);
            }
        });
        localStorage.removeItem('l_sesion');
    }
});

//Functions
var fnAgregarAlCarrito = function(e) {
    
}