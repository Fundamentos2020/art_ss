const op=document.getElementById('orden');
const de=document.getElementById('date');
const mn=document.getElementById('Mi');
const mx=document.getElementById('Ma');
const cont=document.getElementById('gallery');
var obras=[];

op.addEventListener('change', sort);
de.addEventListener('change', priceFilter);
mn.addEventListener('change', priceFilter);
mx.addEventListener('change', priceFilter);

function priceFilter() {
    var minimo;
    var maximo;
    arr=[];
    obras=JSON.parse(localStorage.getItem('products'));
    console.log(obras);
    if(mn.value=='') {
        minimo=0;
    }
    else {
        minimo=mn.value;
    }
    if(mx.value=='') {
        maximo=100000000;
    }
    else {
        maximo=mx.value;
    }
    for(c=0;c<obras.length;c++) {
        if(obras[c].precio>=minimo && obras[c].precio<=maximo) {
            arr.push(obras[c]);
        }
    }
    obras=arr;
    dateFilter();
}

function dateFilter() {
    switch (de.value) {
        case '0':
            insertion(obras);
            break;
        case '1':
            var f=new Date();
            arr=[];
            for(c=0;c<obras.length;c++) {
                if(obras[c].fechaAlta.substr(0, 4)==String(f.getFullYear())) {
                    var m;
                    if((f.getMonth()+1)<10) {
                        m='0'+String(f.getMonth()+1);
                    }
                    else {
                        m=String(f.getMonth()+1);
                    }
                    if(obras[c].fechaAlta.substr(5, 2)==m) {
                        var d;
                        if((f.getMonth()+1)<10) {
                            d='0'+String(f.getDate());
                        }
                        else {
                            d=String(f.getDate());
                        }
                        if(obras[c].fechaAlta.substr(8, 2)==d) {
                            arr.push(obras[c]);
                        }
                    }
                }
            }
            obras=arr;
            insertion(arr);
            break;
        case '2':
            var f=new Date();
            w=f.getDay();
            arr=[];
            days=[];
            y=f.getFullYear();
            m=f.getMonth()+1;
            d=f.getDate();
            if(w==0) {
                w=7;
            }
            for(x=w;x>0;x--) {
                date=String(y);
                if(m<10) {
                    date+='-0'+String(m);
                }
                else {
                    date+='-'+String(m);
                }
                if(d<10) {
                    date+='-0'+String(d);
                }
                else {
                    date+='-'+String(d);
                }
                days.push(date);
                d--;
                if(d<1) {
                    m--;
                    if(m==4 || m==6 || m==9 || m==11) {
                        d=30;
                    }
                    else {
                        if(m==2) {
                            if(y%4==0) {
                                d=29;
                            }
                            else {
                                d=28;
                            }
                        }
                        else {
                            d=31;
                        }
                    }
                    if(m<1) {
                        m=12;
                        y--;
                    }
                }
            }
            for(c=0;c<obras.length;c++) {
                r=false;
                i=0;
                while(!r && i<days.length) {
                    if(obras[c].fechaAlta.substr(0, 10)==days[i]) {
                        arr.push(obras[c]);
                        r=true;
                    }
                    i++;
                }
            }
            obras=arr;
            insertion(arr);
            break;
        case '3':
            var f=new Date();
            var m='';
            arr=[];
            if((f.getMonth()+1)<10) {
                m='0'+String(f.getMonth()+1);
            }
            else {
                m=String(f.getMonth()+1);
            }
            for(c=0;c<obras.length;c++) {
                if(obras[c].fechaAlta.substr(5, 2)==m) {
                    arr.push(obras[c]);
                }
            }
            obras=arr;
            insertion(arr);
            break;
        case '4':
            var f=new Date();
            arr=[];
            for(c=0;c<obras.length;c++) {
                if(obras[c].fechaAlta.substr(0, 4)==f.getFullYear()) {
                    arr.push(obras[c]);
                }
            }
            obras=arr;
            insertion(arr);
            break;
    }
}

function sort() {
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
    xhr.open('GET', 'Controllers/publicacionesController.php'+arch, true);
    xhr.onload=function() {
        if(this.status===200) {
            doc=this.responseText;
            obras=JSON.parse(doc).data;
            insertion(obras);
            localStorage.setItem('products', doc);
        }
    }
    xhr.send();
}

function insertion(arr) {
    let inyect='';
    arr.forEach(function(elem) {
        fecha=extraeFecha(elem.fecha_alta);
        inyect+=`
            <a href="StandardImage.html" style="color: black;" onclick="setPublication(${elem.id})">
                <div class="col-m_3 col_12">
                    <div class="t1 fondo bf1" style="background-image: url('${elem.imagen}');">
                    </div>
                    <div class="t2 l-name">
                        ${elem.nombre}
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

function setPublication(id) {
    localStorage.setItem('Actual-Image', id);
}