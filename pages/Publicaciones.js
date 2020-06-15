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
                var sesion=JSON.parse(localStorage.getItem('l_sesion'));
                if(document.getElementById('pubTitle').value !== '' && document.getElementById('pubDescription').value !== ''
                && document.getElementById('pubUnitPrice').value !== '' && document.getElementById('pubStock').value !== ''
                && document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value !== '') {
                    let form=img.files[0];
                    let send=new FormData();
                    send.append('imagen', form);
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
                    var params = {
                        nombre: document.getElementById('pubTitle').value,
                        descripcion: document.getElementById('pubDescription').value,
                        precio: document.getElementById('pubUnitPrice').value,
                        stock: document.getElementById('pubStock').value,
                        categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value,
                        vistas: 0,
                        ventas: 0,
                        vendedor_id: sesion.id_usuario,
                        fecha_alta: fechaA,
                        imagen: img.files[0].name,
                        token: tokens.token
                    }
    
                    savePublicacion(params).then((data) => {
                        if(data !== undefined) {
                            alert("NUEVO PRODUCTO AGREGADO CORRECTAMENTE");
                            document.getElementById('pubTitle').value = '';
                            document.getElementById('pubDescription').value = '';
                            document.getElementById('pubUnitPrice').value = '';
                            document.getElementById('pubStock').value = '';
                            document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value = '';
                            //document.getElementById('inputImage').defaultValue;
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
    
    // //var form=new FormData(Form)
    // //console.log("Paso");
    // let img=form.files[0];
    // let send=new FormData();
    // send.append('imagen', img);
    // //console.log(send);
    // var param = {
    //     headers: {
    //         'IMAGEN': 'Arrival'
    //     },
    //     method: 'POST',
    //     body: send
    // };
    // let image_name=await fetch("./Controllers/imageController.php", param)
    //     .then( response => response.json() )
    //     .then( data => { return data ; } )
    //     .catch( error => localStorage.setItem('error', error));
    // localStorage.setItem('image_name', image_name.data);
    
    
    //console.log(tokens);
    // if() {
        
    //     else {
    //         var params = {
    //             nombre: document.getElementById('pubTitle').value,
    //             descripcion: document.getElementById('pubDescription').value,
    //             precio: document.getElementById('pubUnitPrice').value,
    //             stock: document.getElementById('pubStock').value,
    //             categoria: document.getElementById('pubCategory')[(document.getElementById('pubCategory')).selectedIndex].value,
    //             vistas: 0,
    //             ventas: 0,
    //             vendedor_id: tokens.id_usuario,
    //             fecha_alta: fechaA,
    //             imagen: localStorage.getItem('image_name')
    //         }
    //         //console.log(params);
    //         var xhr=new XMLHttpRequest();
    //         xhr.withCredentials = true; 
    //         xhr.open("POST", "./publicaciones", true);
    //         xhr.setRequestHeader("Authorization", tokens.token);
    //         xhr.setRequestHeader("Content-Type", "application/json");
    //         xhr.send(JSON.stringify(params));
    //         xhr.addEventListener("readystatechange", function() {
    //             var mes=JSON.parse(this.responseText);
    //             if (mes.success===true){
    //                 location.href="index.html";
    //             }
    //             else {
    //                 alert(mes.messages);
    //             }
    //         });
    //     }
    // }
    
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