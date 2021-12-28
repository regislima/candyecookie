function onRegistraVendaCartao() {
    $("body").addClass("loading");
    $.ajax({
        url : 'index.php?class=FinalizaCompra&method=registraVenda',
        dataType : 'html',
        type : 'POST',
        data : {
            'tipo' : 'cartao',
            'username' : $('#username').val(),
            'cardnumber' : $('#cardnumber').val(),
            'cardmonth' : $('#cardmonth').val(),
            'cardyear' : $('#cardyear').val(),
            'cvv' : $('#cvv').val(),
            'parcelas' : $("#parcelas_cartao option:selected").val(),
            'presente' : $('#presente').val()
        },
        
        success : function(response) {
            $("body").removeClass("loading");
            window.location.href = 'index.php?class=UserSucesso';
        },
        
        error: function (request, status, error) {
            window.location.href = 'index.php?class=UserFalha';
        }
    });
}

function onRegistraVendaBoleto() {
    $("body").addClass("loading");
    $.ajax({
        url : 'index.php?class=FinalizaCompra&method=registraVenda',
        dataType : 'html',
        type : 'POST',
        data : {
            'tipo' : 'boleto',
            'parcelas' : 1,
            'presente' : $('#presente').val()
        },
        
        success : function(response) {
            $("body").removeClass("loading");
            window.location.href = 'index.php?class=UserSucesso';
        },
        
        error: function (request, status, error) {
            window.location.href = 'index.php?class=UserFalha';
        }
    });
}

function onRegistraVendaCredito() {
    $("body").addClass("loading");
    $.ajax({
        url : 'index.php?class=FinalizaCompra&method=registraVenda',
        dataType : 'html',
        type : 'POST',
        data : {
            'tipo' : 'credito',
            'parcelas' : $("#parcelas_creditos option:selected").val(),
            'presente' : $('#presente').val()
        },
        
        success : function(response) {
            $("body").removeClass("loading");
            window.location.href = 'index.php?class=UserSucesso';
        },
        
        error: function (request, status, error) {
            window.location.href = 'index.php?class=UserFalha';
        }
    });
}

$(function() {
    $('[data-toggle="tooltip"]').tooltip();
})