function mensagem(type, txt) {
    var txtType = type == 'error' ? 'Erro' : 'Sucesso';
    var colorType = type == 'error' ? 'red' : 'green';
    $.alert({
        title: txtType,
        type: colorType,
        content: txt,
    });
}

function mensagem_contato(type, txt) {
    var txtType = type == 'error' ? 'Erro' : 'Sucesso';
    var colorType = type == 'error' ? 'red' : 'green';
    $.alert({
        title: txtType,
        type: colorType,
        content: txt,
    });

    setInterval(function () {
        location.reload();
    }, 2000);
}