const op=document.getElementById('orden');
const cont=document.getElementById('gallery');

op.addEventListener('change', sort);

function sort() {
    obras=JSON.parse(localStorage.getItem('products'));
    switch (op.value) {
        case '0':
            insertion(obras);
            break;
        case '1':
            arr=[];
            for(c=obras.length-1;c>=0;c--) {
                arr.push(obras[c]);
            }
            insertion(arr);
            break;
        case '2':
            for(c=0;c<obras.length-1;c++) {
                for (d=0;d<obras.length-c-1;d++) {
                    if (obras[d].precio>obras[d+1].precio) {
                        t=obras[d];  
                        obras[d]=obras[d+1];
                        obras[d+1]=t;
                    }
                }
            }
            insertion(obras);
            break;
        case '3':
            for(c=0;c<obras.length-1;c++) {
                for (d=0;d<obras.length-c-1;d++) {
                    if (obras[d].vistas<obras[d+1].vistas) {
                        t=obras[d];  
                        obras[d]=obras[d+1];
                        obras[d+1]=t;
                    }
                }
            }
            insertion(obras);
            break;
        case '4':
            for(c=0;c<obras.length-1;c++) {
                for (d=0;d<obras.length-c-1;d++) {
                    if (obras[d].Nombre[0]>obras[d+1].Nombre[0]) {
                        t=obras[d];
                        obras[d]=obras[d+1];
                        obras[d+1]=t;
                    }
                }
            }
            insertion(obras);
            break;
        case '5':
            for(c=0;c<obras.length-1;c++) {
                for (d=0;d<obras.length-c-1;d++) {
                    if (obras[d].Nombre[0]<obras[d+1].Nombre[0]) {
                        t=obras[d];
                        obras[d]=obras[d+1];
                        obras[d+1]=t;
                    }
                }
            }
            insertion(obras);
            break;
    }
}

function loadJson(arch) {
    let inyect='';
    xhr=new XMLHttpRequest();
    xhr.open('GET', arch, true);
    xhr.onload=function() {
        if(this.status===200) {
            doc=this.responseText;
            obras=JSON.parse(doc);
            insertion(obras);
            localStorage.setItem('products', doc);
        }
    }
    xhr.send();
}

function insertion(arr) {
    let inyect='';
    arr.forEach(function(elem) {
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
    cont.innerHTML=inyect;
}

function extraeFecha(fecha) {
    var d=fecha[8]+fecha[9];
    var m=fecha[5]+fecha[6];
    var a=fecha[2]+fecha[3];
    return d+"/"+m+"/"+a;
}