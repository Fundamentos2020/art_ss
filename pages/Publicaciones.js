//General variables
'use strict';

//Handlers
window.addEventListener('DOMContentLoaded', function () {

    document.getElementById('btnProducto').addEventListener('click', fnProducto, false);

});

//Functions
var fnProducto = function(e) {
    //alert("A button of product was clicked!");
    if(commandName === "CREATE")
        save();
    else
        edit();
};

/* Funcion para guardar una publicacion */
function save() {
    var params = {
        nombre: document.getElementById('pubTitle').value,
        descripcion: document.getElementById('pubDescription').value,
        precio: document.getElementById('pubUnitPrice').value,
        stock: document.getElementById('pubStock').value,
        categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value,
        vistas: 0,
        ventas: 0,
        vendedor_id: 0,
        imagen:"C:/Users/Usuario/Downloads/descarga.png"
    }

    // var xhr=new XMLHttpRequest();
    // xhr.withCredentials = true; 
    // xhr.open("POST", `${api}/Controllers/publicacionesController.php/savePublicacion`);
    // xhr.setRequestHeader("Content-Type", "application/json");
    // xhr.send(JSON.stringify(params));
    // xhr.addEventListener("readystatechange", function() {
    //     var response = JSON.parse(this.responseText);
    //     console.log(response);
    // });
    savePublicacion(params).then((data) => {
        if(data != [] && data != undefined) {
            console.log(data);
        }
    });
}

/* Funcion para editar una publicacion */
function edit() {
    let params = {
        title: document.getElementById('pubTitle').value,
        descripcion: document.getElementById('pubDescription').value,
        precioUnitario: document.getElementById('pubUnitPrice').value,
        stock: document.getElementById('pubStock').value,
        categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value
    }
}