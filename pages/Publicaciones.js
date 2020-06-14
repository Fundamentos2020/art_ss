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
    //console.log("Paso");
    let img=form.files[0];
    let send=new FormData();
    send.append('imagen', img);
    //console.log(send);
    var param = {
        headers: {
            'IMAGEN': 'Arrival'
        },
        method: 'POST',
        body: send
    };
    let image_name=await fetch("./Controllers/imageController.php", param)
        .then( response => response.json() )
        .then( data => { return data ; } )
        .catch( error => localStorage.setItem('error', error));
    localStorage.setItem('image_name', image_name.data);
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
    var tokens=JSON.parse(localStorage.getItem('l_sesion'));
    //console.log(tokens);
    if(localStorage.getItem('l_sesion')!==null) {
        if(tokens.rol_usuario==='COMPRADOR') {
            alert("Usted no esta autorizado para publicar obras");
        }
        else {
            var params = {
                nombre: document.getElementById('pubTitle').value,
                descripcion: document.getElementById('pubDescription').value,
                precio: document.getElementById('pubUnitPrice').value,
                stock: document.getElementById('pubStock').value,
                categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value,
                vistas: 0,
                ventas: 0,
                vendedor_id: tokens.id_usuario,
                fecha_alta: fechaA,
                imagen: localStorage.getItem('image_name')
            }
            //console.log(params);
            var xhr=new XMLHttpRequest();
            xhr.withCredentials = true; 
            xhr.open("POST", "./publicaciones", true);
            xhr.setRequestHeader("Authorization", tokens.token);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.send(JSON.stringify(params));
            xhr.addEventListener("readystatechange", function() {
                var mes=JSON.parse(this.responseText);
                if (mes.success===true){
                    location.href="index.html";
                }
                else {
                    alert(mes.messages);
                }
            });
        }
    }
    else {
        alert("No hay ninguna sesion iniciada");
    }
    
    // savePublicacion(params).then((data) => {
    //     if(data != [] && data != undefined) {
    //         console.log(data);
    //     }
    // });
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