//General variables

//Handlers
window.addEventListener('DOMContentLoaded', function () {

    document.getElementById('N').addEventListener('click', getPedidos, false);

});

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