$(document).ready(function() {
    $("#cpf").mask('000.000.000-00', {reverse: true});
    $("#telefone").mask('(00) 00000-0000');
    $("#nascimento").mask('00/00/0000');
    $("#valor_frete").mask("000.000,00", {reverse: true});
    $("#valor_minimo").mask("000.000,00", {reverse: true});

    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    })
});