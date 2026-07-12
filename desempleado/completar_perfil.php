<?php
include '../componentes/header_desempleado.php';
include '../conexion/conexion.php';

$usuario_id = $id_usuario;

try {
    // Obtener datos del usuario
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header('Location: ../login_desempleados.php');
        exit();
    }

    // Verificar si ya tiene perfil completo
    $stmt = $pdo->prepare("SELECT * FROM buscadores_empleo WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $buscador = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($buscador) {
        header('Location: index.php');
        exit();
    } 

} catch (PDOException $e) {
    error_log("Error al cargar datos del usuario: " . $e->getMessage());
    $_SESSION['mensaje_error'] = "Error al cargar los datos del perfil.";
    header('Location: ../login_desempleados.php');
    exit();
}
?>

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

    body,
    html {
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
        border-bottom: 3px solid var(--gov-blue);
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
    .profile-dropdown {
        position: relative !important;
    }

    .profile-menu-img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid var(--gov-green);
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .profile-menu-img:hover {
        transform: scale(1.05);
    }

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

    /* ===== ESTILOS ESPECÍFICOS PARA EL FORMULARIO ===== */
    .section-title {
        border-left: 4px solid var(--gov-blue);
        padding-left: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--gov-dark);
        margin-bottom: 1.5rem;
    }

    .avatar-preview-container {
        width: 140px;
        height: 140px;
        margin: 0 auto 15px auto;
        position: relative;
    }
    .avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid var(--gov-blue);
        object-fit: cover;
        background-color: #e9ecef;
        transition: border-color 0.2s;
    }
    .avatar-preview:hover {
        border-color: var(--gov-blue-light);
    }
    .btn-upload-avatar {
        position: absolute;
        bottom: 0;
        right: 0;
        background-color: var(--gov-blue);
        color: white;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #fff;
        transition: transform 0.2s, background 0.2s;
    }
    .btn-upload-avatar:hover {
        transform: scale(1.1);
        background-color: var(--gov-blue-light);
    }

    .file-upload-wrapper {
        background-color: rgba(11, 58, 96, 0.04);
        border: 1px dashed var(--gov-border);
        border-radius: var(--gov-radius-sm);
        padding: 10px;
        transition: border-color 0.2s, background 0.2s;
    }
    .file-upload-wrapper:hover {
        border-color: var(--gov-blue);
        background-color: rgba(11, 58, 96, 0.06);
    }
    .file-upload-wrapper .form-control {
        border: none;
        padding: 0.4rem 0;
        background: transparent;
        cursor: pointer;
    }
    .file-upload-wrapper .form-control:focus {
        box-shadow: none;
    }

    .experience-block {
        background-color: rgba(11, 58, 96, 0.03);
        border-radius: var(--gov-radius);
        border: 1px solid var(--gov-border);
        padding: 1.5rem;
        margin-bottom: 1rem;
        position: relative;
        transition: background 0.2s, border-color 0.2s;
    }
    .experience-block:hover {
        background-color: rgba(11, 58, 96, 0.05);
        border-color: var(--gov-blue-light);
    }

    .btn-submit {
        background-color: var(--gov-blue);
        color: #ffffff;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 3rem;
        font-weight: 600;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.15);
    }
    .btn-submit:hover {
        background-color: var(--gov-blue-light);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(11, 58, 96, 0.2);
        color: #fff;
    }

    .btn-outline-blue {
        border-color: var(--gov-blue);
        color: var(--gov-blue);
    }
    .btn-outline-blue:hover {
        background-color: var(--gov-blue);
        color: #fff;
        border-color: var(--gov-blue);
    }

    .btn-delete-experience {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
        line-height: 1;
        z-index: 10;
    }
    .btn-delete-experience:hover {
        background-color: #c82333;
        transform: scale(1.1);
    }

    .form-switch .form-check-input {
        width: 2.8em;
        height: 1.4em;
        cursor: pointer;
    }
    .form-switch .form-check-input:checked {
        background-color: var(--gov-green);
        border-color: var(--gov-green);
    }
    .form-switch .form-check-input:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 0.25rem rgba(11, 58, 96, 0.25);
    }

    .btn-close-white {
        filter: invert(1);
    }

    .border-top {
        border-color: var(--gov-border) !important;
    }

    /* ===== INPUTS ===== */
    .form-control,
    .form-select {
        border: 1px solid var(--gov-border);
        border-radius: var(--gov-radius-sm);
        padding: 0.6rem 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        background-color: #fff;
        color: var(--gov-dark);
    }
    .form-control:focus,
    .form-select:focus {
        border-color: var(--gov-blue);
        box-shadow: 0 0 0 3px rgba(11, 58, 96, 0.1);
        background-color: #fff;
    }
    .form-control::placeholder {
        color: #8a9aa8;
    }
    .form-label {
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--gov-dark);
        margin-bottom: 4px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 576px) {
        .avatar-preview-container {
            width: 100px;
            height: 100px;
        }
        .btn-upload-avatar {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
        .experience-block {
            padding: 1rem;
        }
        .dashboard-card {
            padding: 1.5rem !important;
        }
    }

    /* Mensaje de error */
    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: var(--gov-radius);
        color: #721c24;
        padding: 1rem;
        margin-bottom: 1rem;
    }
</style>

<body>

    <canvas id="canvas-background"></canvas>

    <div class="main-wrapper">

        <?php include '../componentes/menu_desempleado.php'; ?>

        <main class="container py-5 flex-grow-1">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-10">

                    <?php if (isset($_SESSION['mensaje_error'])): ?>
                        <div class="alert-error">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?php 
                                echo htmlspecialchars($_SESSION['mensaje_error']);
                                unset($_SESSION['mensaje_error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="card dashboard-card p-4 p-lg-5">

                        <form id="registrationForm" action="../php/procesar_completar_perfil.php" method="POST" enctype="multipart/form-data">

                            <!-- Campo oculto con el ID del usuario -->
                            <input type="hidden" name="usuario_id" value="<?php echo htmlspecialchars($usuario['id']); ?>">

                            <!-- SECCIÓN FOTO CARNET (OBLIGATORIA) -->
                            <div class="text-center mb-5">
                                <div class="avatar-preview-container">
                                    <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg" id="photoPreview" class="avatar-preview" alt="Foto Carnet">
                                    <label for="photoInput" class="btn-upload-avatar">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" id="photoInput" name="foto_carnet" accept="image/*" class="d-none" onchange="previewImage(event)" required>
                                </div>
                                <h2 class="h4 fw-bold m-0" style="color: var(--gov-dark);">Expediente Digital de Buscador de Empleo</h2>
                                <p class="text-muted small">Sube tu foto carnet obligatoria arriba para activar el expediente</p>
                            </div>

                            <!-- SECCIÓN 1: DATOS PERSONALES (precargados desde la BBDD) -->
                            <h3 class="section-title">1. Datos Personales</h3>
                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellidos *</label>
                                    <input type="text" name="apellidos" class="form-control" 
                                           value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Documento de Identidad (DIP / Pasaporte) *</label>
                                    <input type="text" name="documento_identidad" class="form-control" 
                                           value="<?php echo htmlspecialchars($usuario['documento_identidad']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono de Contacto *</label>
                                    <input type="tel" name="telefono" class="form-control" placeholder="Ej. +240 222 333 444" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado Civil *</label>
                                    <select name="estado_civil" class="form-select" required>
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="soltero">Soltero/a</option>
                                        <option value="casado">Casado/a</option>
                                        <option value="divorciado">Divorciado/a</option>
                                        <option value="viudo">Viudo/a</option>
                                    </select>
                                </div>
                            </div>

                            <!-- SECCIÓN 2: UBICACIÓN -->
                            <h3 class="section-title">2. Ubicación y Residencia (Guinea Ecuatorial)</h3>
                            <div class="row g-3 mb-5">
                                <div class="col-md-4">
                                    <label class="form-label">Provincia *</label>
                                    <select id="provinciaSelect" name="provincia" class="form-select" onchange="updateDistritos()" required>
                                        <option value="" disabled selected>Selecciona Provincia</option>
                                        <option value="bioko_norte">Bioko Norte</option>
                                        <option value="bioko_sur">Bioko Sur</option>
                                        <option value="litoral">Litoral</option>
                                        <option value="centro_sur">Centro Sur</option>
                                        <option value="kie_ntem">Kie-Ntem</option>
                                        <option value="wele_nzas">Wele-Nzas</option>
                                        <option value="annobon">Annobón</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Distrito *</label>
                                    <select id="distritoSelect" name="distrito" class="form-select" onchange="updateCiudades()" required disabled>
                                        <option value="" disabled selected>Selecciona primero provincia</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ciudad / Municipio *</label>
                                    <select id="ciudadSelect" name="ciudad_municipio" class="form-select" required disabled>
                                        <option value="" disabled selected>Selecciona primero distrito</option>
                                    </select>
                                </div>
                            </div>

                            <!-- SECCIÓN 3: DOCUMENTACIÓN -->
                            <h3 class="section-title">3. Archivos y Documentación Adjunta</h3>
                            <div class="row g-3 mb-5">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-file-earmark-lock me-1"></i> Copia del DIP escaneado *</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="copia_dip" class="form-control" accept=".pdf, image/*" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-file-earmark-person me-1"></i> Currículum Vitae *</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="cv" class="form-control" accept=".pdf, .doc, .docx" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-journal-bookmark me-1"></i> Títulos Académicos <span class="text-muted">(Opcional)</span></label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="titulos" class="form-control" accept=".pdf, image/*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-folder-plus me-1"></i> Otros Documentos <span class="text-muted">(Opcional)</span></label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" name="otros_documentos" class="form-control" accept=".pdf, image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- SECCIÓN 4: EXPERIENCIA LABORAL -->
                            <div class="experience-header mb-4 p-3 bg-light rounded-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                <div>
                                    <h3 class="h5 m-0 fw-bold"><i class="bi bi-briefcase me-2"></i>¿Posees experiencia laboral previa?</h3>
                                    <p class="m-0 small text-muted">Desactiva esta opción si buscas tu primer empleo.</p>
                                </div>
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" id="experienceSwitch" name="tiene_experiencia" value="1" checked onchange="toggleExperienceSection()">
                                </div>
                            </div>

                            <div id="experienceWrapper">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="h6 text-uppercase tracking-wider text-muted m-0">Historial Laboral</h4>
                                    <button type="button" class="btn btn-sm btn-outline-blue rounded-pill px-3" onclick="addExperience()">
                                        <i class="bi bi-plus-circle me-1"></i> Añadir Bloque
                                    </button>
                                </div>

                                <div id="experienceContainer">
                                    <div class="experience-block" id="initialExperience">
                                        <button type="button" class="btn-delete-experience" onclick="deleteExperience(this)" title="Eliminar experiencia">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nombre de la Empresa</label>
                                                <input type="text" name="exp_empresa[]" class="form-control exp-input" placeholder="Ej. Getesa, Somagec...">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Puesto / Cargo</label>
                                                <input type="text" name="exp_puesto[]" class="form-control exp-input" placeholder="Ej. Técnico, Cajero...">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha de Inicio</label>
                                                <input type="date" name="exp_fecha_inicio[]" class="form-control exp-input">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha de Fin</label>
                                                <input type="date" name="exp_fecha_fin[]" class="form-control exp-input">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Descripción de funciones</label>
                                                <textarea name="exp_funciones[]" class="form-control exp-input" rows="3" placeholder="Tareas realizadas..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center pt-4 border-top mt-5">
                                <button type="submit" class="btn btn-submit px-5">Finalizar Registro</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../componentes/footer_desempleado.php'; ?>

        <!-- ===== SCRIPTS ESPECÍFICOS ===== -->
        <script>
            // ===== 1. PREVISUALIZACIÓN DE FOTO =====
            function previewImage(event) {
                const reader = new FileReader();
                reader.onload = function() {
                    const output = document.getElementById('photoPreview');
                    output.src = reader.result;
                }
                if (event.target.files[0]) {
                    reader.readAsDataURL(event.target.files[0]);
                }
            }

            // ===== 2. BASE DE DATOS GEOGRÁFICA (Guinea Ecuatorial) =====
            const geoData = {
                bioko_norte: {
                    distritos: { malabo: "Malabo", baney: "Baney" },
                    ciudades: { malabo: ["Malabo", "Rebola"], baney: ["Baney", "Santiago de Baney"] }
                },
                bioko_sur: {
                    distritos: { luba: "Luba", riaba: "Riaba" },
                    ciudades: { luba: ["Luba", "Batete"], riaba: ["Riaba", "Moca"] }
                },
                litoral: {
                    distritos: { bata: "Bata", mbini: "Mbini", kogo: "Cogo" },
                    ciudades: { bata: ["Bata", "Machinda"], mbini: ["Mbini", "Bitika"], kogo: ["Cogo", "Corisco"] }
                },
                centro_sur: {
                    distritos: { evinayong: "Evinayong", akurenam: "Akurenam", niefang: "Niefang" },
                    ciudades: { evinayong: ["Evinayong"], akurenam: ["Akurenam"], niefang: ["Niefang", "Bicurga"] }
                },
                kie_ntem: {
                    distritos: { ebebiyin: "Ebebiyín", micomeseng: "Micomeseng", nsoc_nsomo: "Nsoc-Nsomo" },
                    ciudades: { ebebiyin: ["Ebebiyín", "Bidjabidján"], micomeseng: ["Micomeseng"], nsoc_nsomo: ["Nsoc-Nsomo"] }
                },
                wele_nzas: {
                    distritos: { mongomo: "Mongomo", ansisok: "Añisoc", nsock: "Nsoc", oyala: "Ciudad de la Paz" },
                    ciudades: { mongomo: ["Mongomo"], ansisok: ["Añisoc"], nsock: ["Nsoc"], oyala: ["Ciudad de la Paz", "Mengomeyén"] }
                },
                annobon: {
                    distritos: { san_antonio: "San Antonio de Palé" },
                    ciudades: { san_antonio: ["San Antonio de Palé", "Mabana"] }
                }
            };

            function updateDistritos() {
                const provincia = document.getElementById('provinciaSelect').value;
                const distritoSelect = document.getElementById('distritoSelect');
                const ciudadSelect = document.getElementById('ciudadSelect');

                distritoSelect.innerHTML = '<option value="" disabled selected>Selecciona Distrito</option>';
                ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona primero distrito</option>';
                ciudadSelect.disabled = true;

                if (geoData[provincia]) {
                    distritoSelect.disabled = false;
                    const distritos = geoData[provincia].distritos;
                    for (const key in distritos) {
                        distritoSelect.innerHTML += `<option value="${key}">${distritos[key]}</option>`;
                    }
                }
            }

            function updateCiudades() {
                const provincia = document.getElementById('provinciaSelect').value;
                const distrito = document.getElementById('distritoSelect').value;
                const ciudadSelect = document.getElementById('ciudadSelect');

                ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona Ciudad</option>';

                if (geoData[provincia] && geoData[provincia].ciudades[distrito]) {
                    ciudadSelect.disabled = false;
                    const ciudades = geoData[provincia].ciudades[distrito];
                    ciudades.forEach(ciudad => {
                        ciudadSelect.innerHTML += `<option value="${ciudad.toLowerCase()}">${ciudad}</option>`;
                    });
                }
            }

            // ===== 3. EXPERIENCIA LABORAL =====
            function toggleExperienceSection() {
                const isChecked = document.getElementById('experienceSwitch').checked;
                const wrapper = document.getElementById('experienceWrapper');
                const inputs = document.querySelectorAll('.exp-input');

                if (isChecked) {
                    wrapper.style.display = 'block';
                    inputs.forEach(input => input.required = true);
                } else {
                    wrapper.style.display = 'none';
                    inputs.forEach(input => {
                        input.required = false;
                        input.value = '';
                    });
                }
            }

            function deleteExperience(button) {
                const block = button.closest('.experience-block');
                const container = document.getElementById('experienceContainer');
                if (container.children.length <= 1) {
                    alert('Debe haber al menos un bloque de experiencia.');
                    return;
                }
                block.remove();
            }

            function addExperience() {
                const container = document.getElementById('experienceContainer');
                const newBlock = document.createElement('div');
                newBlock.className = 'experience-block';
                const isRequired = document.getElementById('experienceSwitch').checked ? 'required' : '';

                newBlock.innerHTML = `
                    <button type="button" class="btn-delete-experience" onclick="deleteExperience(this)" title="Eliminar experiencia">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre de la Empresa</label>
                            <input type="text" name="exp_empresa[]" class="form-control exp-input" placeholder="Ej. Empresa SA" ${isRequired}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Puesto / Cargo</label>
                            <input type="text" name="exp_puesto[]" class="form-control exp-input" placeholder="Ej. Especialista" ${isRequired}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Inicio</label>
                            <input type="date" name="exp_fecha_inicio[]" class="form-control exp-input" ${isRequired}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de Fin</label>
                            <input type="date" name="exp_fecha_fin[]" class="form-control exp-input" ${isRequired}>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción de funciones</label>
                            <textarea name="exp_funciones[]" class="form-control exp-input" rows="3" placeholder="Tareas..." ${isRequired}></textarea>
                        </div>
                    </div>
                `;
                container.appendChild(newBlock);
            }

            // ===== 4. INICIALIZAR =====
            toggleExperienceSection();
        </script>

    </body>
</html>