//General variables

//Handlers
window.addEventListener('DOMContentLoaded', function () {

    document.getElementById('btnProducto').addEventListener('click', fnProducto, false);

    document.getElementById('productImage').addEventListener('change', fnChangeImage);

});

var fnChangeImage = function(e) {
    var targetFile = e.target;
    files = targetFile.files;

    if (FileReader && files && files.length) {
        var fileRead = new FileReader();
        fileRead.onload = function () {
            document.getElementById('imgElement').src = fileRead.result;

        }
        fileRead.readAsDataURL(files[0]);
    }
}
//Functions
var fnProducto = function(e) {
    if(commandName === "CREATE")
        save();
    else
        edit();
};

/* Funcion para guardar una publicacion */
async function save() {
    var img = document.getElementById('productImage');
    var tokens=JSON.parse(localStorage.getItem('l_sesion'));
    if(localStorage.getItem('l_sesion')!==null) {
        if(img.files[0] !== undefined) {
            if(tokens.rol_usuario==='COMPRADOR') {
                alert("Usted no esta autorizado para publicar obras");
            }
            else {
                var fecha = new Date();
                var fechaA=String(fecha.getFullYear())+"-";
                if((fecha.getMonth()+1)<10) {
                    fechaA+="0";
                }
                fechaA+=String(fecha.getMonth()+1)+"-";
                if(fecha.getDate()<10) {
                    fechaA+="0";
                }
                fechaA+=String(fecha.getDate())+" "+String(fecha.getHours())+":"+String(fecha.getMinutes());
    
                if(document.getElementById('pubTitle').value !== '' && document.getElementById('pubDescription').value !== ''
                && document.getElementById('pubUnitPrice').value !== '' && document.getElementById('pubStock').value !== ''
                && document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value !== '') {
                    var params = {
                        nombre: document.getElementById('pubTitle').value,
                        descripcion: document.getElementById('pubDescription').value,
                        precio: document.getElementById('pubUnitPrice').value,
                        stock: document.getElementById('pubStock').value,
                        categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value,
                        vistas: 0,
                        ventas: 0,
                        vendedor_id: 57,
                        fecha_alta: fechaA,
                        imagen: form.files[0].name
                    }
    
                    savePublicacion(params).then((data) => {
                        if(data !== undefined) {
                            alert("NUEVO PRODUCTO AGREGADO CORRECTAMENTE");
                            document.getElementById('pubTitle').value = '';
                            document.getElementById('pubDescription').value = '';
                            document.getElementById('pubUnitPrice').value = '';
                            document.getElementById('pubStock').value = '';
                            document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value = '';
                            document.getElementById('inputImage').defaultValue;
                            document.getElementById('inputImage').value = "";
                        }
                    });
                }
                else {
                    alert("Hay campos faltantes!");
                }
            }
        }
        else {
            alert("Es necesario agregar una imagen en la publicacion");
        }
    }
    else {
        alert("No hay ninguna sesion iniciada");
    }
    
    //var form=new FormData(Form)
    
    // let data = new FormData();
    // data.append('imagen', img);
    // var param = {
    //     headers: {
    //         'IMAGEN': 'Arrival'
    //     },
    //     method: 'POST',
    //     body: data
    // };
    // let image_name = await fetch("./Controllers/imageController.php", param)
    //     .then( response => response.json() )
    //     .then( data => { return data ; } )
    //     .catch( error => localStorage.setItem('error', error ) );
    // //console.log(image_name.data);
    // localStorage.setItem('image_name', image_name.data);
    
    //console.log(tokens);
    //if(localStorage.getItem('l_sesion')!==null) {
        // var xhttp=new XMLHttpRequest();
        // xhttp.withCredentials=true;
        // xhttp.open("GET", "./usuarios/"+tokens.id_usuario, true);
        // var user;
        // xhttp.onload=function() {
        //     if (this.status!==200) {
        //         var user=JSON.parse(this.responseText);
        //         var xh=new XMLHttpRequest();
        //         xh.withCredentials=true;
        //         xh.open("GET", "./roles/"+user.rol_id, true);
        //         xh.onload=function() {
        //             if (this.status!==200) {
        //                 var rol=JSON.parse(this.responseText);
        //                 if(rol.tipo==='COMPRADOR') {
        //                     alert("Usted no esta autorizado para publicar obras")
        //                 }
        //             }
        //         };
        //     }
        // };
        //xhttp.send();
        
        //console.log(params);
        // var xhr=new XMLHttpRequest();
        // xhr.withCredentials = true; 
        // xhr.open("POST", "./publicaciones", true);
        // xhr.setRequestHeader("Authorization", tokens.token);
        // xhr.setRequestHeader("Content-Type", "application/json");
        // xhr.send(JSON.stringify(params));
        // xhr.addEventListener("readystatechange", function() {
        //     var mes=JSON.parse(this.responseText);
        //     if (mes.success===true){
        //         location.href="index.html";
        //     }
        // });
    //}
    //else {
        //alert("No hay ninguna sesion iniciada");
    //}
    
    
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