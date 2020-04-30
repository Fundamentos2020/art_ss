window.onload = function() {
    const cont=document.getElementById('gallery');
    let inyect='';
    let arch='Ilustraciones.json';
    xhr=new XMLHttpRequest();
    xhr.open('GET', arch, true);
    xhr.onload=function() {
        if(this.status===200) {
            obras=JSON.parse(this.responseText);
            let inyect='';
            obras.forEach(function(elem) {
                fecha=extraeFecha(elem.fechaAlta);
                inyect+=`
                    <a href="${elem.Nombre}.html" style="color: black;">
                        <div class="col-m_3 col_12">
                            <div class="t1 fondo bf1" style="background-image: url('${elem.imagen}');">
                            </div>
                            <div class="t2 l-name">
                                ${elem.Nombre}
                            </div>
                            <div class="t2 l-price">
                                <div class="pad2">
                                    $${elem.precio}
                                </div>
                            </div>
                            <div class="t3">
                                <div class="t3 col-m_6 col_6 pad2">
                                    <div class="pad3 l-vs">
                                        ${elem.vistas} visitas
                                    </div>
                                </div>
                                <div class="t3 col-m_6 col_6 l-vs">
                                    ${elem.ventas} ventas
                                </div>
                            </div>
                            <div class="t3">
                                <div class="l-date pad4" style="float: right;">
                                    <i>${fecha}</i>
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            })
            document.getElementById('gallery').innerHTML=inyect;
        }
    }
    xhr.send();
}

function extraeFecha(fecha) {
    var d=fecha[8]+fecha[9];
    var m=fecha[5]+fecha[6];
    var a=fecha[0]+fecha[1]+fecha[2]+fecha[3];
    return d+"/"+m+"/"+a;
}