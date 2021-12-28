$(document).ready(function() {
    $("input[name='cpf']").mask('000.000.000-00', {reverse: true});
    $("input[name='telefone']").mask('(00) 00000-0000');
    $("input[name='nascimento']").mask('00/00/0000');
    $("input[name='credito']").mask("000.000,00", {reverse: true});

    var valores = [];
    $('#id_cidade').find('option').each(function() {
        var valor = $(this).val();
        valores.push(valor);
    });
    
    var valorCidade = $('input[name=id_cidade]').val();
    $('input[name=id_cidade]').val(valores[valorCidade - 1]);
});



  



