//Variables que conoce todo el proyecto
'use strict';
var arrCarrito = [];
var commandName = "CREATE";
const sesion=document.getElementById('inFin');
const sesion2=document.getElementById('inFin2');
const delet=document.getElementById('disableCount');

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
        delet.innerHTML=`
            <a href="Borrar.html" style="color: red;"><span class="item-menu-text">BORRAR CUENTA</span></a>
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
    var monto_total = 0;
    var publicacionesInCarrito = document.getElementById('dataCarrito');
    var items = localStorage.getItem('carrito');
    var copyItems = items.split(',');
    var fullCarrito = copyItems.map(function(x) {
        return parseInt(x);
    });
    var inyect = "";
    publicacionesInCarrito.innerHTML = "No hay Elementos en el carrito de comrpa!";
    if(fullCarrito.length > 0) {
        fullCarrito.forEach((publicacionId) => {
            getPublicacionById({id: publicacionId}).then((server) => {
                if(server !== undefined) {
                    var publicacion = server.data.res[0];
                    monto_total += parseInt(publicacion.precio);
                    inyect = `
                    <div class="row">
                        <div class="col-m_3 col_12">
                            <img src="${publicacion.imagen}" alt="" width="100%" height="100%">
                        </div>
                    
                        <div class="col-m_8 col_12">
                            <div class="margin-div">
                                <label for="" style="font-weight: bold; font-size: 40px;">${publicacion.nombre}</label>
                                <br>
                                <label for="" style="font-weight: bold;">${publicacion.descripcion}</label>
                                <br>
                                <label for="" style="font-weight: bold;">$${publicacion.precio}</label>
                                <br>
                                <label for="" style="font-weight: bold;">${publicacion.categoria}</label>
                                <br>
                            </div>
                        </div>
                        <div class="col-m_1 col_12">
                            <button name="${publicacion.id}" class="btn-delete"><span><i class="fa fa-trash" aria-hidden="true"></i></span>Eliminar</button>
                        </div>
                    </div>`;
                    publicacionesInCarrito.innerHTML += inyect;
                    setListeners();
                }
                localStorage.setItem('monto_total', monto_total);
            });
        });
        publicacionesInCarrito.innerHTML += `
            <div class="row">
                <button type="submit" class="btn-submit" id="confirmOrden" onclick="confirmOrder()">Save</button>
            </div>`;
    }
    else {
        alert("El carrito de compra esta vacío");
    }
}

function setListeners() {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', fnDescontarCarrito);
    });
}

var fnDescontarCarrito = function(e) {
    var idPublicacion = parseInt(e.target.name);
    var items = localStorage.getItem('carrito');
    var copyItems = items.split(',');
    var fullCarrito = copyItems.map(function(x) {
        return parseInt(x);
    });
    for( var i = 0; i < fullCarrito.length; i++) {
        if ( fullCarrito[i] === idPublicacion) {
            fullCarrito.splice(i, 1);
        }
    }
    localStorage.setItem('carrito', fullCarrito);
    bindCarritoCompra();
    alert("Se ha eliminado el producto del carrito!");
}