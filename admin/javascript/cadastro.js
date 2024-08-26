 // Script para abrir e fechar o modal
 var modal = document.getElementById("myModal2");
 var btn = document.getElementById("openModalBtn2");
 var span = document.getElementsByClassName("close2")[0];

 // Quando o usuário clica no botão, abre o modal
 btn.onclick = function() {
     modal.style.display = "block";
 }

 // Quando o usuário clica no (x), fecha o modal
 span.onclick = function() {
     modal.style.display = "none";
 }

 // Quando o usuário clica fora do modal, fecha o modal
 window.onclick = function(event) {
     if (event.target == modal) {
         modal.style.display = "none";
     }
 }
