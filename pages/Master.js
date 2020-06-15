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
            <a href="index.html"><span class="item-menu-text"><i class="fa fa-sign-in" aria-hidden="true"></i></span></a>
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
            var mes=JSON.parse(this.responseText);
            if (mes.success===false){
                alert(mes.messages);
            }
        });
        localStorage.removeItem('l_sesion');
    }
});

sesion2.addEventListener('click', function(e) {
    //e.preventDefault();
    if(localStorage.getItem('l_sesion')!==null) {
        var tokens=JSON.parse(localStorage.getItem('l_sesion'));
        var xhttp=new XMLHttpRequest();
        xhttp.withCredentials=true;
        xhttp.open("DELETE", "./sesiones/"+tokens.id_sesion, true);
        xhttp.setRequestHeader("Authorization", tokens.token);
        xhttp.send();
        xhttp.addEventListener("readystatechange", function() {
            var mes=JSON.parse(this.responseText);
            if (mes.success===false){
                alert(mes.messages);
            }
        });
        localStorage.removeItem('l_sesion');
    }
});

//Functions
function fnAgregarAlCarrito (e) {
    let idPublicacion = parseInt(e.name);
    var items;
    if(localStorage.getItem('carrito') !== null) {
        items = localStorage.getItem('carrito');
        if(items !== "") {
            items = items.split(',');
            var copyItems = items;
            items = copyItems.map(function(x) {
                return parseInt(x);
             });
        }
        else
            items = [];
        if(!items.includes(idPublicacion)) {
            arrCarrito = items;
            arrCarrito.push(idPublicacion);
            localStorage.setItem('carrito', arrCarrito);
            alert("Producto Agregado al carrito de compra!");
        }
        else
            alert("El producto ya existe en tu carrito de compra");
    }
    else {
        arrCarrito.push(idPublicacion);
        localStorage.setItem('carrito', arrCarrito);
    }
}

function bindCarritoCompra() {
    var inyect = document.getElementById('content-user-form-pay');
    arrCarrito.forEach((publicacionId) => {
        getPublicacionById({id: publicacionId}).then((server) => {
            if(data !== undefined) {
                var publicacion = server.data.res;
                inyect += `
                <div class="col-m_6 col_12style ">
                    <img alt="" width="100%" height="100%">
                </div>
            
                <div class="col-m_6 col_12 ">
                    <div class="margin-div">
                        <label for="" style="font-weight: bold; font-size: 40px;" >Dog</label>
                        <br>
                        <label for="" style="font-weight: bold;" >Se enviara a la direccion:</label>
                        <br>
                        <label for="" style="font-weight: bold;" >Cantidad:</label>
                        <br>
                        <label for="" style="font-weight: bold;" >Vendedor/Artista:</label>
                        <br>
                    </div>
                </div>          
                `;
            }
        })
    });
    
}