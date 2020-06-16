const filter=document.getElementById('Title').innerHTML;
const list=document.getElementById('List');

window.addEventListener('DOMContentLoaded', function () {
    var info=JSON.parse(localStorage.getItem('l_sesion'));
    let inyect='';
    //console.log("Entro");
    if (info!==null) {
        var obras=[]
        if (filter=="Historial de ventas") {
            getPublicacionesByVendedor({vendedor_id: info.id_usuario }).then((data) => {
                if(data !== undefined) {
                    obras=data.data.res;
                    obras.forEach(function(elem) {
                        console.log(elem);
                        fecha=extraeFecha(elem.fecha_alta);
                        if(elem.comprador_id!==null) {
                            console.log("Entro");
                            inyect+=`
                            <div class="row adapt" style="background-color: grey; border: solid black;">
                                <div class="t1 fondo bf4 col-m_2p5 col_12" style="background-image: url('${elem.imagen}');">
                                </div>
                                <div class="col-m_8 col_12 pad9">
                                    <h1>${elem.nombre}</h1>
                                    <br>
                                    <h4>Vendido</h4>
                                    <h4>Monto pagado: $${elem.precio}</h4>
                                </div>
                                <div class="col_11p5">
                                    <h2 style="font-family: 'Blinker';font-size: 16px; text-align: right;">
                                        <i>${fecha}</i>
                                    </h2>
                                </div>
                            </div>
                            `;
                        }
                    })                    
                }
                else {
                    alert("No se pudieron obtener las ventas");
                }
                list.innerHTML+=inyect;
            })
        }
        else {
            getPublicacionesByComprador({comprador_id: info.id_usuario }).then((data) => {
                if(data !== undefined) {
                    obras=data.data.res;
                    obras.forEach(function(elem) {
                        console.log(elem);
                        fecha=extraeFecha(elem.fecha_alta);
                        if(elem.comprador_id!==null) {
                            //console.log("Entro");
                            inyect+=`
                            <div class="row adapt" style="background-color: grey; border: solid black;">
                                <div class="t1 fondo bf4 col-m_2p5 col_12" style="background-image: url('${elem.imagen}');">
                                </div>
                                <div class="col-m_8 col_12 pad9">
                                    <h1>${elem.nombre}</h1>
                                    <br>
                                    <h4>Vendedor: ${elem.nombre_vendedor}</h4>
                                    <h4>Monto pagado: $${elem.precio}</h4>
                                </div>
                                <div class="col_11p5">
                                    <h2 style="font-family: 'Blinker';font-size: 16px; text-align: right;">
                                        <i>${fecha}</i>
                                    </h2>
                                </div>
                            </div>
                            `;
                        }
                    })                           
                }
                else {
                    alert("No se pudieron obtener las compras");
                }
                list.innerHTML+=inyect;
            })
        }
    }
    else {
        alert("No hay ninguna sesion iniciada");
    }
});

function extraeFecha(fecha) {
    var d=fecha[8]+fecha[9];
    var m=fecha[5]+fecha[6];
    var a=fecha[2]+fecha[3];
    return d+"/"+m+"/"+a;
}