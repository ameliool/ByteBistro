//MODAL DE ADICIONAR PRODUTO
const button = document.getElementById("adicionar-produto")
const modal = document.querySelector("dialog")
const buttonCLose = document.querySelector("dialog button ")

button.onclick = function (){
    modal.showModal()
}
buttonCLose.onclick = function(){
    modal.close()
}