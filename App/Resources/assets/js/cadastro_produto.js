$(document).ready(function() {
    $("input[name='preco_custo']").mask("000.000,00", {reverse: true});
    $("input[name='preco_venda']").mask("000.000,00", {reverse: true});
    $('#cnpjfabricante').mask('00.000.000/0000-00', {reverse: true});

    var valores = [];
    $('#id_fabricante').find('option').each(function() {
        var valor = $(this).val();
        valores.push(valor);
    });
    
    var valorFabricante = $('input[name=id_fabricante]').val();
    $('input[name=id_fabricante]').val(valores[valorFabricante - 1]);

    $("#btnSalvarNovoFabricante").click(function() {
        $.ajax({
            url : '?class=ProdutosForm&method=onAddFabricante',
            dataType : 'html',
            type : 'POST',
            data : {
                "nome_fabricante" : $("#nomefabricante").val(),
                "cnpj_fabricante" : $("#cnpjfabricante").val(),
                "url_fabricante" : $("#urlfabricante").val()
            },
            success : function(response) {
                location.reload(); 
            }
        });
    });
});