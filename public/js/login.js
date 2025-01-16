$(function () {
    $('.formLogin').submit(function (e) {
        e.preventDefault();
        var dados = $('.formLogin').serialize();
        $.ajax({
            url: GLOBAL_URL + 'auth',
            data: dados,
            datatype: "json",
            type: "POST",
            success: function (data) {
                var retorno = $.parseJSON(data);
                if (retorno.sucesso == true) {
                    window.location = GLOBAL_URL;
                } else {
                    mensagem('error', retorno.mensagem);
                }
            }
        });
    });
});

function mensagem(type, txt) {
    var txtType = type == 'error' ? 'Erro' : 'Sucesso';
    var colorType = type == 'error' ? 'red' : 'green';
    $.alert({
        title: txtType,
        type: colorType,
        content: txt,
    });
}