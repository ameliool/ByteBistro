const menu = document.getElementById("menu")
const menu2 = document.getElementById("menu2")
const menu3 = document.getElementById("menu3")
const cartBtn = document.getElementById("btn-cart")
const cartBtn2 = document.getElementById("btn-cart2")
const cartModal = document.getElementById("modal-cart")
const cartItemsContainer = document.getElementById("cart-items")
const cartTotal = document.getElementById("cart-total")
const checkoutBtn = document.getElementById("checkout-btn")
const closeModalBtn = document.getElementById("close-modal-btn")
const cartCounter = document.getElementById("cart-count")
const cartCounter2 = document.getElementById("cart-count2")
let cart = [];


$('.add-to-cart-btn').on('click', function() {
    let id = $(this).data('id');
    let nome = $(this).data('nome');
    let preco = $(this).data('preco');
    adicionarCarrinho(id, nome, preco);
});


// Função para adicionar ao carrinho
function adicionarCarrinho(id, nome, preco) {
    let quantidade = 1; // Padrão para quantidade
    $.ajax({
        url: 'carrinho.php',
        method: 'POST',
        data: { 
            acao: 'adicionar',
            id: id,
            nome: nome,
            preco: preco,
            quantidade: quantidade
        },
        dataType: 'json',
        success: function (carrinho) {
            atualizarCarrinho(carrinho);
        }
    });
}

// Atualizar o conteúdo do carrinho
function atualizarCarrinho(carrinho) {
    let total = 0;
    let htmlCarrinho = '';
    let contagem = 0;

    for (let id in carrinho) {
        let item = carrinho[id];
        htmlCarrinho += `
            <div class="item-carrinho">
                <span>${item.nome} x ${item.quantidade}</span>
                <span>R$${(item.preco * item.quantidade).toFixed(2)}</span>
                <button onclick="removerCarrinho(${id})">Remover</button>
                <input type="number" value="${item.quantidade}" onchange="alterarQuantidade(${id}, this.value)" min="1">
            </div>
        `;
        total += item.preco * item.quantidade;
        contagem += item.quantidade;
    }

    $('#cart-items').html(htmlCarrinho);
    $('#cart-total').text(total.toFixed(2));
    $('#cart-count').text(contagem);
}

// Função para remover item do carrinho
function removerCarrinho(id) {
    $.ajax({
        url: 'carrinho.php',
        method: 'POST',
        data: {
            acao: 'remover',
            id: id
        },
        dataType: 'json',
        success: function (carrinho) {
            atualizarCarrinho(carrinho);
        }
    });
}

// Alterar quantidade do item no carrinho
function alterarQuantidade(id, quantidade) {
    $.ajax({
        url: 'carrinho.php',
        method: 'POST',
        data: {
            acao: 'alterar_quantidade',
            id: id,
            quantidade: quantidade
        },
        dataType: 'json',
        success: function (carrinho) {
            atualizarCarrinho(carrinho);
        }
    });
}

// Finalizar a compra
$('#checkout-btn').on('click', function () {
    let total = parseFloat($('#cart-total').text());
    $.ajax({
        url: 'carrinho.php',
        method: 'POST',
        data: {
            acao: 'finalizar',
            total: total
        },
        dataType: 'json',
        success: function (resposta) {
            if (resposta.status == 'sucesso') {
                alert('Pedido finalizado com sucesso!');
                window.location.href = 'pedidos.php'; // Redireciona para a página de pedidos
            } else {
                alert('Erro ao finalizar pedido: ' + resposta.message);
            }
        }
    });
});

// Atualizar o carrinho ao carregar a página
$(document).ready(function () {
    $.ajax({
        url: 'carrinho.php',
        method: 'POST',
        data: {
            acao: 'atualizar'
        },
        dataType: 'json',
        success: function (carrinho) {
            atualizarCarrinho(carrinho);
        }
    });
});



//ABRIR MODAL DE PEDIDO ENVIADO

const pedidoEnviado = document.getElementById("modal-pedido-realizado")
const btnFecharPr = document.getElementById("btn-fechar-pr")


checkoutBtn.onclick = function (){
    pedidoEnviado.showModal()
    cartModal.style.display = "none"
}

btnFecharPr.onclick = function() {
    pedidoEnviado.close()
}


