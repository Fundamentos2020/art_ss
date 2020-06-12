//General variables
//const Form=document.getElementById('Form');
const form=document.getElementById('productImage');

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
async function save() {
    //var form=new FormData(Form)
    var img=form.files[0];
    let data=new FormData();
    var vendedor_id = parseInt(localStorage.getItem('ID_User'));
    data.append('imagen', img);
    var param = {
        headers: {
            'IMAGEN': 'Arrival'
        },
        method: 'POST',
        body: data
    };

    var xhr=new XMLHttpRequest();
    xhr.withCredentials = true; 
    let image_name = await fetch("./Controllers/imageController.php", param)
        .then( response => {
            response.json()
             
        })
        .catch( error => localStorage.setItem('error', error ) );
    console.log(img.name);
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
    var params = {
        nombre: document.getElementById('pubTitle').value,
        descripcion: document.getElementById('pubDescription').value,
        precio: document.getElementById('pubUnitPrice').value,
        stock: document.getElementById('pubStock').value,
        categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value,
        vistas: 0,
        ventas: 0,
        vendedor_id: vendedor_id,
        fecha_alta: fechaA,
        imagen: img.name
    }

    // xhr.open("POST", "./publicaciones", true);
    //             xhr.setRequestHeader("Content-Type", "application/json");
    //             xhr.send(JSON.stringify(params));
    //             xhr.addEventListener("readystatechange", function() {
    //                 var response = JSON.parse(this.responseText);
    //                 console.log(response);
    //             });
    //localStorage.setItem('image_name', image_name.data);
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