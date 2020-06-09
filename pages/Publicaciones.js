//General variables
var commandName = "CREATE";

//Callbacks
document.getElementById('btnProducto').addEventListener('click', fnProducto);

//Functions
var fnProducto = function(e) {
    if(commandName === "CREATE")
        save();
    else
        edit();
};

/* Funcion para guardar una publicacion */
function save() {
    let params = {
        title: document.getElementById('pubTitle').value,
        descripcion: document.getElementById('pubDescription').value,
        precioUnitario: document.getElementById('pubUnitPrice').value,
        stock: document.getElementById('pubStock').value,
        categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value
    }

    savePublicacion(params).then((res) => {
        console.log(res+"Trying PHP");
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