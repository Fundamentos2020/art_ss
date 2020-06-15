//General variables

//Handlers
//Functions
function getPedidos() {
    var params = {
        status: document.getElementById('status').val,
    };

    getPedidosByComprador(params).then((data) => {
        if(data !== undefined) {
            console.log(data);
        }
    });
}

function confirmOrder() {
    if(localStorage.getItem('l_sesion')) {
        var l_sesion = JSON.parse(localStorage.getItem('l_sesion'));
        if(l_sesion.rol_usuario == "COMPRADOR") {
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
                
            var params = {
                comprador_id: l_sesion.id_usuario,
                estatus: "PROCESANDO",
                monto_total: localStorage.getItem('monto_total'),
                forma_pago: "DEBITO",
                fecha_pedido: fechaA
            };

            savePedido(params).then((server) => {
                if(server !== undefined) {
                    alert("Pedido generado exitosamente!");
                    //update de publicacioes
                    var items = localStorage.getItem('carrito');
                    items = items.split(',');
                    var copyItems = items;
                    items = copyItems.map(function(x) {
                        return parseInt(x);
                    });
                    items.forEach((publicacion) => {
                        var publicacionParams = {
                            comprador_id: l_sesion.id_usuario, 
                            id: publicacion
                        }
                        updatePublicacion(publicacionParams).then((server) => {
                            if(server !== undefined) {
                                bindCarritoCompra();
                            }
                            else
                                alert("Error al guardar el pedido!");
                        });
                    });
                    var carrito = localStorage.getItem('carrito');
                    carrito = "";
                    localStorage.setItem('carrito', carrito);
                }
                else
                    alert("Error al guardar el pedido!");
            });
        }
        else {
            alert("Su cuenta no puede ser usada para comprar productos!");
        }
    }
    else {
        alert("Debe iniciar sesion para realizar una compra!");
    }
}