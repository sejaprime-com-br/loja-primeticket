function sairPainel() {
    $.ajax({
        url: GLOBAL_URL + 'logout',
        data: {},
        datatype: "json",
        type: "POST",
        success: function (data) {
            var retorno = $.parseJSON(data);
            if (retorno.sucesso == true) {
                window.location = GLOBAL_URL + 'login';
            }
        }
    });
}

function areaPainel() {
    window.location = GLOBAL_URL + 'admin';
}