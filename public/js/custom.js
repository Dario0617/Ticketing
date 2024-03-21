// Fonction pour charger les tickets
function loadTickets() {
  // Code pour charger les tickets depuis une source de données (par exemple, une API)
  // Les données seront ajoutées dynamiquement au tableau
}

// Charger les tickets au chargement de la page
$(document).ready(function() {
  loadTickets();
});

// Fonction pour formatter la colonne Action
function actionFormatter(value, row, index) {
  return '<button class="btn btn-secondary">Action</button>';
}

// Fonction pour filtrer les tickets ouverts
$('#btnTicketOuverts').click(function() {
  // Code pour filtrer les tickets ouverts
  $('#table').bootstrapTable('filterBy', { etat: 'ouvert' });
});

// Fonction pour filtrer les tickets fermés
$('#btnTicketFermes').click(function() {
  // Code pour filtrer les tickets fermés
  $('#table').bootstrapTable('filterBy', { etat: 'fermé' });
});

// Écouter les événements de recherche sur les champs de recherche Bootstrap Table
$('#searchInput').on('keyup', function() {
  var searchText = $(this).val().toLowerCase();
  $('#table').bootstrapTable('search', searchText);
});


// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }

      form.classList.add('was-validated')
    }, false)
  })
})()