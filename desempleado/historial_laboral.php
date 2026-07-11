<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Historial Laboral - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        /* ===== PALETA INSTITUCIONAL UNIFICADA ===== */
        :root {
            --gov-blue: #0B3A60;
            --gov-blue-light: #165285;
            --gov-gold: #C9A84C;
            --gov-gold-light: #E8D5A3;
            --gov-green: #1E7E34;
            --gov-green-light: #2E9B4A;
            --gov-dark: #0A192F;
            --gov-bg: #F8FAFC;
            --gov-border: #E2E8F0;
            --gov-radius: 8px;
            --gov-radius-sm: 4px;
        }

        body, html {
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--gov-bg);
            color: var(--gov-dark);
            position: relative;
        }
        #canvas-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            opacity: 0.3;
        }
        .main-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar-portal {
            background-color: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(12px);
            border-bottom: 3px solid var(--gov-gold);
            box-shadow: 0 4px 20px rgba(11, 58, 96, 0.04);
            position: relative;
            z-index: 1050;
        }
        .navbar-nav .nav-link {
            font-weight: 500;
            color: var(--gov-dark) !important;
            padding: 0.5rem 1rem;
            border-radius: var(--gov-radius-sm);
            transition: all 0.2s;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            background: rgba(11, 58, 96, 0.06);
            color: var(--gov-blue) !important;
        }
        .navbar-nav .nav-link.active {
            background: rgba(11, 58, 96, 0.10);
            font-weight: 600;
        }

        /* ===== DROPDOWN PERFIL ===== */
        .profile-dropdown { position: relative !important; }
        .profile-menu-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--gov-green);
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .profile-menu-img:hover { transform: scale(1.05); }
        .custom-profile-menu {
            background-color: #ffffff !important;
            border: 1px solid var(--gov-border) !important;
            border-radius: var(--gov-radius) !important;
            box-shadow: 0 12px 30px rgba(10, 25, 47, 0.1) !important;
            padding: 10px !important;
            z-index: 1060 !important;
        }
        @media (min-width: 992px) {
            .custom-profile-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                min-width: 260px;
                transform: translateY(8px) !important;
            }
            .profile-dropdown:hover .custom-profile-menu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
        }
        @media (max-width: 991.98px) {
            .custom-profile-menu {
                position: absolute !important;
                left: 50% !important;
                right: auto !important;
                transform: translate(-85%, 12px) !important;
                width: 260px !important;
                max-width: calc(100vw - 30px) !important;
            }
            .profile-dropdown:hover .custom-profile-menu {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
        }

        /* ===== TARJETAS ===== */
        .dashboard-card {
            background: #ffffff;
            border: 1px solid var(--gov-border);
            border-radius: var(--gov-radius);
            box-shadow: 0 4px 12px rgba(11, 58, 96, 0.015);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dashboard-card:hover {
            border-color: var(--gov-gold);
            box-shadow: 0 10px 25px rgba(11, 58, 96, 0.04);
            transform: translateY(-1px);
        }

        /* ===== BOTONES ===== */
        .btn-gov {
            background-color: var(--gov-blue);
            color: #ffffff;
            border: none;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: background-color 0.2s ease, transform 0.15s;
        }
        .btn-gov:hover {
            background-color: var(--gov-blue-light);
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-gov-outline {
            background: transparent;
            border: 2px solid var(--gov-blue);
            color: var(--gov-blue);
            border-radius: var(--gov-radius-sm);
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-gov-outline:hover {
            background: var(--gov-blue);
            color: white;
        }
        .btn-gold {
            background-color: var(--gov-gold);
            color: #ffffff;
            border: none;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-gold:hover {
            background-color: #B8953A;
            color: white;
            transform: translateY(-1px);
        }
        .btn-green {
            background-color: var(--gov-green);
            color: #ffffff;
            border: none;
            border-radius: var(--gov-radius-sm);
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-green:hover {
            background-color: var(--gov-green-light);
            color: white;
            transform: translateY(-1px);
        }
        .btn-outline-secondary {
            border: 2px solid var(--gov-border);
            color: var(--gov-dark);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 500;
            transition: all 0.25s;
        }
        .btn-outline-secondary:hover {
            background: var(--gov-blue);
            color: white;
            border-color: var(--gov-blue);
        }
        .btn-outline-gold {
            border: 2px solid var(--gov-gold);
            color: var(--gov-gold);
            background: transparent;
            border-radius: var(--gov-radius-sm);
            font-weight: 600;
            transition: all 0.25s;
        }
        .btn-outline-gold:hover {
            background: var(--gov-gold);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(201, 168, 76, 0.30);
        }

        /* ===== FILTROS ===== */
        .form-control-search {
            border-radius: var(--gov-radius-sm) !important;
            border: 1.5px solid var(--gov-border);
            padding: 0.7rem 1.2rem;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            width: 100%;
        }
        .form-control-search:focus {
            border-color: var(--gov-blue);
            box-shadow: 0 0 0 4px rgba(11, 58, 96, 0.12);
        }
        .form-select-custom {
            border-radius: var(--gov-radius-sm);
            border: 1.5px solid var(--gov-border);
            padding: 0.7rem 1.2rem;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }
        .form-select-custom:focus {
            border-color: var(--gov-gold);
            box-shadow: 0 0 0 4px rgba(201, 168, 76, 0.12);
        }

        /* ===== TABLA ===== */
        .table-custom {
            border-radius: var(--gov-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        }
        .table-custom thead {
            background-color: #f8fafc;
        }
        .table-custom th {
            font-weight: 600;
            color: var(--gov-dark);
            border-bottom: 2px solid var(--gov-border);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 0.8rem 1rem;
        }
        .table-custom td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badges de estado */
        .badge-estado {
            font-weight: 500;
            padding: 0.35rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .badge-estado.revision { background: #fff3cd; color: #856404; }
        .badge-estado.preseleccionado { background: #d4edda; color: #155724; }
        .badge-estado.finalizado { background: #e2e3e5; color: #383d41; }
        .badge-estado.entrevista { background: #cce5ff; color: #004085; }
        .badge-estado.descartado { background: #f8d7da; color: #721c24; }

        /* ===== ESTADÍSTICAS RÁPIDAS ===== */
        .stat-mini {
            background: #ffffff;
            border: 1px solid var(--gov-border);
            border-radius: var(--gov-radius-sm);
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transition: all 0.2s;
        }
        .stat-mini:hover {
            border-color: var(--gov-gold);
            background: #fafbfc;
        }
        .stat-mini .stat-icon {
            font-size: 1.5rem;
            color: var(--gov-blue);
            opacity: 0.6;
            width: 2rem;
            text-align: center;
        }
        .stat-mini .stat-number {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--gov-dark);
            line-height: 1;
        }
        .stat-mini .stat-label {
            font-size: 0.75rem;
            color: #6b7a8a;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ===== PAGINACIÓN (idéntica a bolsa y cursos) ===== */
        .pagination-custom .page-link {
            border-radius: var(--gov-radius-sm) !important;
            margin: 0 4px;
            border: 1px solid var(--gov-border);
            color: var(--gov-blue);
            transition: all 0.2s;
        }
        .pagination-custom .page-link:hover {
            background: var(--gov-gold);
            color: white;
            border-color: var(--gov-gold);
        }
        .pagination-custom .page-item.active .page-link {
            background: var(--gov-blue);
            border-color: var(--gov-blue);
            color: white;
        }

        /* ===== FOOTER ===== */
        footer {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(201, 168, 76, 0.15);
        }

        @media (max-width: 576px) {
            .dashboard-card { padding: 1.5rem !important; }
            .stat-mini { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

        <?php include 'header_desempleado.php'; ?>

        <main class="container py-5 flex-grow-1">
            <div class="row g-4">

                <!-- TÍTULO DE LA PÁGINA -->
                <div class="col-12">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-file-earmark-arrow-down fs-1" style="color: var(--gov-green);"></i>
                        <div>
                            <h1 class="h3 fw-bold m-0" style="color: var(--gov-dark);">Mi Historial Laboral</h1>
                            <p class="text-muted m-0">Consulta todas tus postulaciones, contratos anteriores y experiencias laborales registradas.</p>
                        </div>
                    </div>
                    <hr class="mb-4">
                </div>

                <!-- ESTADÍSTICAS RÁPIDAS -->
                <div class="col-12">
                    <div class="row g-3 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-send"></i></div>
                                <div>
                                    <div class="stat-number">12</div>
                                    <div class="stat-label">Total postulaciones</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                                <div>
                                    <div class="stat-number">3</div>
                                    <div class="stat-label">En proceso</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-check2-circle" style="color: var(--gov-green);"></i></div>
                                <div>
                                    <div class="stat-number">5</div>
                                    <div class="stat-label">Preseleccionadas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-mini">
                                <div class="stat-icon"><i class="bi bi-x-circle" style="color: #dc3545;"></i></div>
                                <div>
                                    <div class="stat-number">4</div>
                                    <div class="stat-label">Finalizadas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FILTROS Y BÚSQUEDA -->
                <div class="col-12">
                    <div class="card dashboard-card p-4 mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold small text-muted">Buscar</label>
                                <input type="text" id="searchHistorial" class="form-control form-control-search" placeholder="Buscar por puesto o empresa...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold small text-muted">Estado</label>
                                <select id="estadoFiltro" class="form-select form-select-custom">
                                    <option value="">Todos</option>
                                    <option value="revision">En Revisión</option>
                                    <option value="preseleccionado">Preseleccionado</option>
                                    <option value="entrevista">Entrevista</option>
                                    <option value="finalizado">Finalizado</option>
                                    <option value="descartado">Descartado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-gov w-100" id="filtrarBtn"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary w-100" id="limpiarFiltrosBtn"><i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABLA DE HISTORIAL -->
                <div class="col-12">
                    <div class="card dashboard-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0 h6 text-uppercase tracking-wider text-muted">
                                <i class="bi bi-list-ul me-2"></i>Registros de actividad
                            </h5>
                            <span class="badge-soft-blue" id="registrosCount">3 registros</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-custom" id="historialTable">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Puesto</th>
                                        <th>Empresa</th>
                                        <th>Estado</th>
                                        <th class="text-center">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="historialBody">
                                    <!-- Datos cargados por JS -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginación dinámica -->
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center pagination-custom" id="paginationHistorial">
                                <!-- Generado por JS -->
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>
        </main>

        <?php include 'footer_desempleado.php'; ?>

        <script>
            // ===== DATOS DE EJEMPLO =====
            const historialData = [
                { fecha: '05/07/2026', puesto: 'Auxiliar Técnico de Obras', empresa: 'SOMAGEC', estado: 'revision' },
                { fecha: '28/06/2026', puesto: 'Técnico de Soporte TI', empresa: 'GETESA', estado: 'preseleccionado' },
                { fecha: '15/03/2025', puesto: 'Asistente Administrativo', empresa: 'Ministerio de Educación', estado: 'finalizado' },
                { fecha: '10/01/2026', puesto: 'Ingeniero Civil', empresa: 'Constructora Nacional', estado: 'entrevista' },
                { fecha: '20/12/2025', puesto: 'Enfermero', empresa: 'Hospital La Paz', estado: 'descartado' },
                { fecha: '01/11/2025', puesto: 'Profesor de Matemáticas', empresa: 'Instituto Nacional', estado: 'finalizado' },
            ];

            let currentPage = 1;
            const itemsPerPage = 3;
            let filteredData = [...historialData];

            // ===== FUNCIONES DE RENDERIZADO =====

            // Mapeo de estados a badges
            function getBadgeClass(estado) {
                const map = {
                    'revision': 'badge-estado revision',
                    'preseleccionado': 'badge-estado preseleccionado',
                    'finalizado': 'badge-estado finalizado',
                    'entrevista': 'badge-estado entrevista',
                    'descartado': 'badge-estado descartado'
                };
                return map[estado] || 'badge-estado';
            }

            function getEstadoLabel(estado) {
                const map = {
                    'revision': 'En Revisión',
                    'preseleccionado': 'Preseleccionado',
                    'finalizado': 'Finalizado',
                    'entrevista': 'Entrevista',
                    'descartado': 'Descartado'
                };
                return map[estado] || estado;
            }

            function verDetalle(puesto, empresa) {
                alert(`📋 Detalle de la postulación:\nPuesto: ${puesto}\nEmpresa: ${empresa}\n\nEsta funcionalidad mostrará más información en el futuro.`);
            }

            // Renderizar tabla (página actual)
            function renderHistorial() {
                const start = (currentPage - 1) * itemsPerPage;
                const end = start + itemsPerPage;
                const pageItems = filteredData.slice(start, end);

                const tbody = document.getElementById('historialBody');
                const totalRegistros = filteredData.length;

                document.getElementById('registrosCount').textContent = `${totalRegistros} registros`;

                if (totalRegistros === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No se encontraron registros que coincidan con los filtros.
                            </td>
                        </tr>
                    `;
                    document.getElementById('paginationHistorial').innerHTML = '';
                    return;
                }

                let html = '';
                pageItems.forEach(item => {
                    html += `
                        <tr>
                            <td><span class="fw-medium">${item.fecha}</span></td>
                            <td><strong>${item.puesto}</strong></td>
                            <td>${item.empresa}</td>
                            <td><span class="${getBadgeClass(item.estado)}">${getEstadoLabel(item.estado)}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-gold" onclick="verDetalle('${item.puesto}', '${item.empresa}')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;

                renderPagination();
            }

            // Renderizar paginación (idéntica a bolsa y cursos)
            function renderPagination() {
                const totalPages = Math.ceil(filteredData.length / itemsPerPage);
                const controls = document.getElementById('paginationHistorial');

                if (totalPages <= 1) {
                    controls.innerHTML = '';
                    return;
                }

                let html = '';
                html +=
                    `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Anterior</a></li>`;

                for (let i = 1; i <= totalPages; i++) {
                    html +=
                        `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }

                html +=
                    `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Siguiente</a></li>`;

                controls.innerHTML = html;

                controls.querySelectorAll('a.page-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const page = parseInt(this.dataset.page);
                        if (page >= 1 && page <= totalPages) {
                            currentPage = page;
                            renderHistorial();
                        }
                    });
                });
            }

            // ===== FILTRADO =====
            function filtrarHistorial() {
                const search = document.getElementById('searchHistorial').value.toLowerCase().trim();
                const estado = document.getElementById('estadoFiltro').value;

                filteredData = historialData.filter(item => {
                    const matchSearch = item.puesto.toLowerCase().includes(search) || item.empresa.toLowerCase().includes(search);
                    const matchEstado = estado === '' || item.estado === estado;
                    return matchSearch && matchEstado;
                });

                currentPage = 1;
                renderHistorial();
            }

            // ===== EVENTOS =====
            document.getElementById('filtrarBtn').addEventListener('click', filtrarHistorial);
            document.getElementById('limpiarFiltrosBtn').addEventListener('click', function() {
                document.getElementById('searchHistorial').value = '';
                document.getElementById('estadoFiltro').value = '';
                filtrarHistorial();
            });

            // Búsqueda en tiempo real
            document.getElementById('searchHistorial').addEventListener('input', filtrarHistorial);
            document.getElementById('estadoFiltro').addEventListener('change', filtrarHistorial);

            // ===== INICIALIZAR =====
            renderHistorial();
        </script>

    </body>
    </html>