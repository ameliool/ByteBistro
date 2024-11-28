const button = document.getElementById("div-add-mesa")
const modal = document.getElementById("add-mesa")
const buttonCLose = document.querySelector("dialog button")

button.onclick = function (){
    modal.showModal()
}

setInterval(() =>{
    window.location.reload()
}, 5000)

