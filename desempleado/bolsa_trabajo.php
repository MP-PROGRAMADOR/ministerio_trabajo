<?php
$titulo = 'Bolsa de Trabajo - Portal de Empleo';
include 'header_desempleado.php';
?>

<main class="container py-5 flex-grow-1">
    <div class="row g-4">
        <div class="col-12">
            <div class="card dashboard-card p-4">
                <h5 class="fw-bold mb-3 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h5>
                <form id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" id="searchInput" class="form-control form-control-search" placeholder="Buscar por título o empresa... (búsqueda en tiempo real)">
                        </div>
                        <div class="col-md-3">
                            <select id="sectorSelect" class="form-select form-select-custom">
                                <option value="">Todos los sectores</option>
                                <option value="tecnologia">Tecnología</option>
                                <option value="construccion">Construcción</option>
                                <option value="salud">Salud</option>
                                <option value="educacion">Educación</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="citySelect" class="form-select form-select-custom">
                                <option value="">Todas las ciudades</option>
                                <option value="malabo">Malabo</option>
                                <option value="bata">Bata</option>
                                <option value="ebebiyin">Ebebiyín</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary w-100" id="clearFiltersBtn" title="Limpiar todos los filtros">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12">
            <div class="card dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted"><i class="bi bi-briefcase me-2"></i>Ofertas de Empleo (<span id="jobCount">12</span>)</h5>
                    <span class="badge-soft-blue">Últimas 30 días</span>
                </div>
                <div id="jobList" class="row g-3">
                    <!-- Las tarjetas se generan con JavaScript -->
                </div>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center pagination-custom" id="paginationControls"></ul>
                </nav>
            </div>
        </div>
    </div>
</main>

<!-- Modales -->
<div class="modal fade" id="postulacionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2" style="color: var(--gov-green);"></i>Postularse a: <span id="postulacionTitulo">Oferta</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3"><i class="bi bi-building me-1"></i>Empresa: <strong id="postulacionEmpresa">-</strong></p>
                <form id="postulacionForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre completo *</label>
                        <input type="text" id="postulacionNombre" class="form-control form-control-custom" placeholder="Ej: Juan Carlos Nsue" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Documento de Identidad (DIP) *</label>
                        <input type="text" id="postulacionDIP" class="form-control form-control-custom" placeholder="Ej: 123456789" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Teléfono *</label>
                        <input type="tel" id="postulacionTelefono" class="form-control form-control-custom" placeholder="Ej: 555-123456" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Correo electrónico *</label>
                        <input type="email" id="postulacionEmail" class="form-control form-control-custom" placeholder="ejemplo@correo.gq" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mensaje / Carta de presentación</label>
                        <textarea id="postulacionMensaje" class="form-control form-control-custom" rows="3" placeholder="Cuéntenos por qué es el candidato ideal..."></textarea>
                    </div>
                    <p class="text-muted small">* Campos obligatorios</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-green" id="enviarPostulacionBtn"><i class="bi bi-send me-2"></i>Enviar postulación</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmacionPostulacionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom-color: var(--gov-green);">
                <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2" style="color: var(--gov-green);"></i>Postulación enviada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-hourglass-split" style="font-size: 4rem; color: var(--gov-gold);"></i>
                <h5 class="mt-3 fw-bold">¡Su postulación ha sido recibida!</h5>
                <p class="text-muted">La empresa recibirá sus datos y se pondrá en contacto en caso de ser seleccionado. Recibirá notificaciones sobre el estado de su candidatura.</p>
                <div class="alert alert-info mt-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i> Puede hacer seguimiento de sus postulaciones desde el <strong>Panel General</strong>.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-blue" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer_desempleado.php'; ?>

<script>
    // ===== DATOS DE OFERTAS =====
    const jobs = [
        { id: 1, title: 'Técnico de Soporte TI', company: 'GETESA', city: 'Malabo', sector: 'tecnologia', date: '08/07/2026', contract: 'Indefinido', jornada: 'Completa', badge: 'Nuevo', badgeColor: 'gold', accent: 'gold' },
        { id: 2, title: 'Ingeniero Civil', company: 'SOMAGEC', city: 'Bata', sector: 'construccion', date: '05/07/2026', contract: 'Indefinido', jornada: 'Completa', badge: 'Destacado', badgeColor: 'green', accent: 'green' },
        { id: 3, title: 'Enfermero/a', company: 'Hospital Regional', city: 'Ebebiyín', sector: 'salud', date: '01/07/2026', contract: 'Temporal', jornada: 'Parcial', badge: 'Urgente', badgeColor: 'urgent', accent: 'blue' },
        { id: 4, title: 'Profesor de Matemáticas', company: 'Instituto Nacional', city: 'Malabo', sector: 'educacion', date: '10/07/2026', contract: 'Indefinido', jornada: 'Completa', badge: 'Oferta Pública', badgeColor: 'blue', accent: 'blue' },
        { id: 5, title: 'Desarrollador Web', company: 'TechSolutions', city: 'Bata', sector: 'tecnologia', date: '09/07/2026', contract: 'Indefinido', jornada: 'Completa', badge: 'Nuevo', badgeColor: 'gold', accent: 'gold' },
        { id: 6, title: 'Arquitecto', company: 'Estudio de Arquitectura', city: 'Malabo', sector: 'construccion', date: '06/07/2026', contract: 'Indefinido', jornada: 'Completa', badge: null, badgeColor: null, accent: 'green' },
        { id: 7, title: 'Médico General', company: 'Centro de Salud', city: 'Ebebiyín', sector: 'salud', date: '04/07/2026', contract: 'Indefinido', jornada: 'Completa', badge: 'Urgente', badgeColor: 'urgent', accent: 'blue' },
        { id: 8, title: 'Profesor de Inglés', company: 'Academia Linguística', city: 'Bata', sector: 'educacion', date: '02/07/2026', contract: 'Temporal', jornada: 'Parcial', badge: null, badgeColor: null, accent: 'gold' }
    ];

    let currentPage = 1;
    const itemsPerPage = 3;
    let filteredJobs = [...jobs];

    // ===== FUNCIÓN POSTULAR (abre modal) =====
    function abrirPostulacion(titulo, empresa) {
        document.getElementById('postulacionTitulo').textContent = titulo;
        document.getElementById('postulacionEmpresa').textContent = empresa;
        document.getElementById('postulacionNombre').value = '';
        document.getElementById('postulacionDIP').value = '';
        document.getElementById('postulacionTelefono').value = '';
        document.getElementById('postulacionEmail').value = '';
        document.getElementById('postulacionMensaje').value = '';
        const modal = new bootstrap.Modal(document.getElementById('postulacionModal'));
        modal.show();
    }

    // ===== ENVIAR POSTULACIÓN =====
    document.getElementById('enviarPostulacionBtn').addEventListener('click', function() {
        const nombre = document.getElementById('postulacionNombre').value.trim();
        const dip = document.getElementById('postulacionDIP').value.trim();
        const telefono = document.getElementById('postulacionTelefono').value.trim();
        const email = document.getElementById('postulacionEmail').value.trim();

        if (!nombre || !dip || !telefono || !email) {
            alert('Por favor, complete todos los campos obligatorios (*).');
            return;
        }

        const postulacionModal = bootstrap.Modal.getInstance(document.getElementById('postulacionModal'));
        postulacionModal.hide();

        const confirmacionModal = new bootstrap.Modal(document.getElementById('confirmacionPostulacionModal'));
        confirmacionModal.show();

        console.log('Postulación enviada:', {
            nombre, dip, telefono, email,
            mensaje: document.getElementById('postulacionMensaje').value.trim(),
            titulo: document.getElementById('postulacionTitulo').textContent,
            empresa: document.getElementById('postulacionEmpresa').textContent
        });
    });

    // ===== RENDERIZAR TARJETAS =====
    function renderJobs() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = filteredJobs.slice(start, end);

        const container = document.getElementById('jobList');
        document.getElementById('jobCount').textContent = filteredJobs.length;

        if (filteredJobs.length === 0) {
            container.innerHTML = `<div class="col-12 text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>No se encontraron ofertas que coincidan con los filtros.</div>`;
            document.getElementById('paginationControls').innerHTML = '';
            return;
        }

        let html = '';
        pageItems.forEach((job) => {
            const accentClass = job.accent || 'blue';
            let badgeHtml = '';
            if (job.badge) {
                const badgeClass = job.badgeColor === 'gold' ? 'badge-gold' :
                    job.badgeColor === 'green' ? 'badge-green' :
                    job.badgeColor === 'blue' ? 'badge-blue' :
                    job.badgeColor === 'urgent' ? 'badge-urgent' : 'badge-blue';
                badgeHtml = `<span class="${badgeClass} small me-2">${job.badge}</span>`;
            }

            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="job-card h-100">
                        <div class="card-accent ${accentClass}"></div>
                        <div class="pt-2">
                            ${badgeHtml}
                            <h6 class="fw-bold m-0 job-title">${job.title}</h6>
                            <p class="m-0 job-meta mt-1"><i class="bi bi-building"></i><span class="company-name">${job.company}</span></p>
                            <p class="m-0 job-meta"><i class="bi bi-geo-alt"></i>${job.city}</p>
                            <p class="m-0 job-meta"><i class="bi bi-calendar-event"></i>${job.date} · ${job.jornada}</p>
                            <div class="mt-3 d-flex flex-wrap gap-2">
                                <button class="btn btn-blue btn-sm px-3" onclick="abrirPostulacion('${job.title}', '${job.company}')">Postular</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
        renderPagination();
    }

    // ===== PAGINACIÓN =====
    function renderPagination() {
        const totalPages = Math.ceil(filteredJobs.length / itemsPerPage);
        const controls = document.getElementById('paginationControls');
        if (totalPages <= 1) {
            controls.innerHTML = '';
            return;
        }

        let html = '';
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a></li>`;
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a></li>`;

        controls.innerHTML = html;

        controls.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (page >= 1 && page <= totalPages) {
                    currentPage = page;
                    renderJobs();
                }
            });
        });
    }

    // ===== FILTRADO EN TIEMPO REAL =====
    function filterJobs() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
        const sector = document.getElementById('sectorSelect').value;
        const city = document.getElementById('citySelect').value;

        filteredJobs = jobs.filter(job => {
            const matchSearch = job.title.toLowerCase().includes(searchTerm) || job.company.toLowerCase().includes(searchTerm);
            const matchSector = sector === '' || job.sector === sector;
            const matchCity = city === '' || job.city.toLowerCase() === city;
            return matchSearch && matchSector && matchCity;
        });

        currentPage = 1;
        renderJobs();
    }

    // ===== LIMPIAR FILTROS =====
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('sectorSelect').value = '';
        document.getElementById('citySelect').value = '';
        filterJobs();
    });

    // ===== EVENTOS EN TIEMPO REAL =====
    document.getElementById('searchInput').addEventListener('input', filterJobs);
    document.getElementById('sectorSelect').addEventListener('change', filterJobs);
    document.getElementById('citySelect').addEventListener('change', filterJobs);

    // ===== INICIALIZAR =====
    renderJobs();
</script>