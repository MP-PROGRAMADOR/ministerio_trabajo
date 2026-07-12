    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   
    <script src="../src/js/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaOfertas').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        order: [[0, 'asc']], // Orden inicial
        pageLength: 5,
        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "Todos"]],
        responsive: true
    });
});

// Funciones para las acciones de editar/eliminar
function editarOferta(id) {
    // Lógica para abrir modal o redirigir
    console.log("Editar oferta ID:", id);
}

function eliminarOferta(id) {
    if (confirm("¿Estás seguro de que deseas eliminar esta oferta de empleo?")) {
        // Lógica de eliminación vía AJAX o formulario
        console.log("Eliminar oferta ID:", id);
    }
}
</script>
   
   <script>
        // Toggle Sidebar en dispositivos móviles
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }
    </script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
    const alerta = document.getElementById('alertaSesion');
    
    if (alerta) {
        // Desaparece automáticamente después de 5000 milisegundos (5 segundos)
        setTimeout(() => {
            // Animación suave usando la API de Bootstrap Alert
            const bsAlert = new bootstrap.Alert(alerta);
            bsAlert.close();
        }, 5000);
    }
});

function cerrarAlertaManualmente() {
    const alerta = document.getElementById('alertaSesion');
    if (alerta) {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
        bsAlert.close();
    }
}
</script>


  <script>


    // Delegación de eventos para capturar el botón de edición incluso tras cambiar de página en DataTables
    $('#tablaOfertas').on('click', '.btn-editar-oferta', function(e) {
        e.preventDefault();
        
        // Obtener el botón exacto (incluso si se hace clic en el icono i)
        const btn = $(e.target).closest('.btn-editar-oferta');

        // Extraer atributos
        const id          = btn.attr('data-id');
        const titulo      = btn.attr('data-titulo');
        const provincia   = btn.attr('data-provincia');
        const salario     = btn.attr('data-salario');
        const estado      = btn.attr('data-estado');
        const descripcion = btn.attr('data-descripcion');
        const requisitos  = btn.attr('data-requisitos');

        // Asignar a los campos del modal
        $('#edit_id').val(id);
        $('#edit_titulo_puesto').val(titulo);
        $('#edit_provincia').val(provincia);
        $('#edit_salario_ofrecido').val(salario && salario !== 'null' ? salario : '');
        $('#edit_estado').val(estado);
        $('#edit_descripcion').val(descripcion);
        $('#edit_requisitos').val(requisitos);

        // Abrir modal con Bootstrap 5
        const elModal = document.getElementById('modalEditarOferta');
        if (elModal) {
            const modalEditar = bootstrap.Modal.getOrCreateInstance(elModal);
            modalEditar.show();
        }
    });

    </script>


</body>
</html>