function deletarFoto(idFoto, idEvento) {
    $.alert({
        title: 'Alerta',
        content: 'Deseja realmente deletar esta foto!',
        icon: 'fa fa-alert',
        animation: 'scale',
        closeAnimation: 'scale',
        buttons: {
            confirm: {
                text: ' DELETAR ',
                btnClass: 'btn-success',
                action: function () {
                    $.ajax({
                        url: GLOBAL_URL + 'delete/foto',
                        data: { 'acao': 'deletar_foto', idFoto, idEvento },
                        datatype: "json",
                        type: "POST",
                        success: function (data) {
                            var retorno = $.parseJSON(data);
                            if (retorno.sucesso == true) {
                                location.reload();
                            }
                        }
                    });
                }
            },
            cancel: {
                text: ' CANCELAR ',
                btnClass: 'btn-danger',
                action: function () {

                }
            }
        }
    });

}