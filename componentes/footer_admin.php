<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Al final del <body> (después de Bootstrap JS) -->
<script src="../src/js/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>


<script>
  $(document).ready(function() {
      $('#tablaNotificaciones').DataTable({
          responsive: true,
          pageLength: 10,
          lengthMenu: [5, 10, 25, 50],
          order: [[5, 'desc']], // Ordena por fecha por defecto
          columnDefs: [
              { orderable: false, targets: 6 } // Desactiva ordenación en columna 'Acciones'
          ],
          language: {
              "sProcessing":     "Procesando...",
              "sLengthMenu":     "Mostrar _MENU_ registros",
              "sZeroRecords":    "No se encontraron resultados",
              "sEmptyTable":     "Ningún dato disponible en esta tabla",
              "sInfo":           "Mostrando del _START_ al _END_ de _TOTAL_ registros",
              "sInfoEmpty":      "Mostrando 0 al 0 de 0 registros",
              "sInfoFiltered":   "(filtrado de _MAX_ registros en total)",
              "sSearch":         "Buscar:",
              "sLoadingRecords": "Cargando...",
              "oPaginate": {
                  "sFirst":    "Primero",
                  "sLast":     "Último",
                  "sNext":     "<i class='bi bi-chevron-right'></i>",
                  "sPrevious": "<i class='bi bi-chevron-left'></i>"
              }
          }
      });
  });
</script>

<script>
    // Toggle Sidebar para pantallas pequeñas
    document.getElementById("menu-toggle").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("wrapper").classList.toggle("toggled");
    });

    // Inicializar Tooltips de Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const alertElement = document.getElementById('customAlert');
        if (alertElement) {
            setTimeout(function() {
                if (typeof bootstrap !== 'undefined') {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alertElement);
                    bsAlert.close();
                } else {
                    alertElement.style.transition = "opacity 0.5s ease";
                    alertElement.style.opacity = "0";
                    setTimeout(() => alertElement.remove(), 500);
                }
            }, 5000);
        }
    });
</script>

</body>
</html>