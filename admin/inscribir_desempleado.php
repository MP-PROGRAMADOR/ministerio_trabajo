<?php
session_start();

// ===== VERIFICAR SESIÓN (solo administrador) =====
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: ../login_admin.php');
    exit();
}

$titulo = 'Inscribir Desempleado - Portal de Empleo';
include_once '../componentes/header_admin.php';
include_once '../componentes/menu_admin.php';
?>

<style>
    /* ===== ESTILOS MEJORADOS ===== */
    .form-inscripcion-wrapper {
        max-width: 900px;
        margin: 2rem auto 0; /* Margen superior para separar del header */
        padding: 0 15px;
    }
    .form-inscripcion {
        background: #ffffff;
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .form-inscripcion .form-label {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.9rem;
        margin-bottom: 0.3rem;
    }
    .form-inscripcion .form-control,
    .form-inscripcion .form-select {
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 0.7rem 1rem;
        transition: all 0.2s;
        background-color: #f8fafc;
    }
    .form-inscripcion .form-control:focus,
    .form-inscripcion .form-select:focus {
        border-color: #0B3A60;
        box-shadow: 0 0 0 4px rgba(11, 58, 96, 0.1);
        background-color: #ffffff;
    }
    .form-inscripcion .required::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }
    .section-title {
        border-left: 5px solid #0B3A60;
        padding-left: 15px;
        font-size: 1.15rem;
        font-weight: 700;
        color: #0B3A60;
        margin-bottom: 1.5rem;
        margin-top: 2rem;
    }
    .section-title:first-of-type {
        margin-top: 0;
    }
    .avatar-preview-container {
        width: 150px;
        height: 150px;
        margin: 0 auto 15px auto;
        position: relative;
    }
    .avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid #0B3A60;
        object-fit: cover;
        background-color: #e9ecef;
        transition: all 0.3s;
    }
    .avatar-preview:hover {
        border-color: #1A4F7A;
    }
    .btn-upload-avatar {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: #0B3A60;
        color: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid #ffffff;
        transition: transform 0.2s, background-color 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .btn-upload-avatar:hover {
        transform: scale(1.1);
        background-color: #1A4F7A;
    }

    /* ===== INPUTS FILE SIN TEXTO ADICIONAL ===== */
    .file-upload-wrapper {
        background-color: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 0.5rem 1rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        overflow: hidden;
    }
    .file-upload-wrapper:hover {
        border-color: #0B3A60;
        background-color: #f1f5f9;
    }
    /* Ocultar el texto "Ningún archivo seleccionado" */
    .file-upload-wrapper input[type="file"] {
        display: block;
        width: 100%;
        padding: 0.4rem 0;
        font-size: 0.9rem;
        cursor: pointer;
        color: transparent;  /* Oculta el texto */
    }
    /* Estilizar el botón del file input */
    .file-upload-wrapper input[type="file"]::file-selector-button {
        background-color: #0B3A60;
        color: #fff;
        border: none;
        padding: 0.4rem 1.2rem;
        border-radius: 8px;
        font-weight: 500;
        margin-right: 0.5rem;
        transition: background-color 0.2s;
        cursor: pointer;
    }
    .file-upload-wrapper input[type="file"]::file-selector-button:hover {
        background-color: #1A4F7A;
    }
    /* En Firefox, el texto se oculta con color: transparent */
    .file-upload-wrapper input[type="file"]::-webkit-file-upload-button {
        background-color: #0B3A60;
        color: #fff;
        border: none;
        padding: 0.4rem 1.2rem;
        border-radius: 8px;
        font-weight: 500;
        margin-right: 0.5rem;
        transition: background-color 0.2s;
        cursor: pointer;
    }
    .file-upload-wrapper input[type="file"]::-webkit-file-upload-button:hover {
        background-color: #1A4F7A;
    }

    .btn-submit {
        background: #0B3A60;
        color: #fff;
        border: none;
        padding: 0.8rem 3rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(11, 58, 96, 0.2);
    }
    .btn-submit:hover {
        background: #1A4F7A;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(11, 58, 96, 0.3);
        color: #fff;
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border: none;
        padding: 0.8rem 2.5rem;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .form-inscripcion .text-muted {
        color: #94a3b8 !important;
        font-size: 0.8rem;
    }
    .form-inscripcion .form-text {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-top: 0.2rem;
    }
    .header-icon {
        font-size: 2rem;
        color: #0B3A60;
        margin-right: 0.75rem;
    }

    /* Botón volver arriba */
    .btn-back-top {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #0B3A60;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
        font-size: 0.9rem;
    }
    .btn-back-top:hover {
        color: #1A4F7A;
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .form-inscripcion {
            padding: 1.5rem;
        }
        .avatar-preview-container {
            width: 120px;
            height: 120px;
        }
        .btn-submit, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
        .form-inscripcion-wrapper {
            padding: 0 10px;
        }
    }
</style>

<div class="form-inscripcion-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="form-inscripcion" id="formulario">
                <!-- Cabecera -->
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-person-plus header-icon"></i>
                    <h4 class="fw-bold mb-0" style="color: #0B3A60;">Inscribir nuevo desempleado</h4>
                </div>

                <form action="procesar_inscripcion.php" method="POST" enctype="multipart/form-data" novalidate>

                    <!-- ===== FOTO CARNET ===== -->
                    <div class="text-center mb-5">
                        <div class="avatar-preview-container">
                            <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg" id="photoPreview" class="avatar-preview" alt="Foto Carnet">
                            <label for="photoInput" class="btn-upload-avatar">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" id="photoInput" name="foto_carnet" accept="image/*" class="d-none" onchange="previewImage(event)" required>
                        </div>
                        <p class="text-muted small mb-0">Sube una foto carnet (obligatorio)</p>
                    </div>

                    <!-- ===== DATOS PERSONALES ===== -->
                    <h5 class="section-title">Datos personales</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label required">Nombre(s)</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. Juan Carlos" required>
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos" class="form-label required">Apellidos</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Ej. Nsue Nguema" required>
                        </div>
                        <div class="col-md-6">
                            <label for="documento" class="form-label required">Documento de identidad (DIP)</label>
                            <input type="text" class="form-control" id="documento" name="documento_identidad" placeholder="Número de DIP" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono" class="form-label required">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="+240 222 333 444" required>
                        </div>
                        <div class="col-md-6">
                            <label for="estado_civil" class="form-label required">Estado civil</label>
                            <select class="form-select" id="estado_civil" name="estado_civil" required>
                                <option value="" disabled selected>Selecciona una opción</option>
                                <option value="soltero">Soltero/a</option>
                                <option value="casado">Casado/a</option>
                                <option value="divorciado">Divorciado/a</option>
                                <option value="viudo">Viudo/a</option>
                                <option value="union_libre">Unión libre</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@correo.com">
                            <div class="form-text">Opcional, pero recomendado para notificaciones.</div>
                        </div>
                    </div>

                    <!-- ===== UBICACIÓN ===== -->
                    <h5 class="section-title">Ubicación (Guinea Ecuatorial)</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label for="provincia" class="form-label required">Provincia</label>
                            <select class="form-select" id="provinciaSelect" name="provincia" onchange="updateDistritos()" required>
                                <option value="" disabled selected>Selecciona provincia</option>
                                <option value="bioko_norte">Bioko Norte</option>
                                <option value="bioko_sur">Bioko Sur</option>
                                <option value="litoral">Litoral</option>
                                <option value="centro_sur">Centro Sur</option>
                                <option value="kie_ntem">Kié-Ntem</option>
                                <option value="wele_nzas">Wele-Nzas</option>
                                <option value="annobon">Annobón</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="distrito" class="form-label required">Distrito</label>
                            <select class="form-select" id="distritoSelect" name="distrito" onchange="updateCiudades()" required disabled>
                                <option value="" disabled selected>Selecciona primero provincia</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ciudad" class="form-label required">Ciudad / Municipio</label>
                            <select class="form-select" id="ciudadSelect" name="ciudad" required disabled>
                                <option value="" disabled selected>Selecciona primero distrito</option>
                            </select>
                        </div>
                    </div>

                    <!-- ===== DOCUMENTOS ===== -->
                    <h5 class="section-title">Documentos del expediente</h5>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label for="dip" class="form-label required">Copia del DIP (escaneada)</label>
                            <div class="file-upload-wrapper">
                                <input type="file" class="form-control" id="dip" name="archivo_dip" accept=".pdf,.jpg,.jpeg,.png" required>
                            </div>
                            <div class="form-text">PDF, JPG o PNG. Máx. 10MB.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="cv" class="form-label required">Currículum Vitae (CV)</label>
                            <div class="file-upload-wrapper">
                                <input type="file" class="form-control" id="cv" name="archivo_cv" accept=".pdf,.doc,.docx" required>
                            </div>
                            <div class="form-text">PDF, DOC o DOCX. Máx. 10MB.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="titulos" class="form-label">Títulos académicos</label>
                            <div class="file-upload-wrapper">
                                <input type="file" class="form-control" id="titulos" name="archivo_titulos" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="form-text">Opcional. PDF o imagen. Máx. 10MB.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="otros" class="form-label">Otros documentos</label>
                            <div class="file-upload-wrapper">
                                <input type="file" class="form-control" id="otros" name="archivo_otros" accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="form-text">Opcional. PDF o imagen. Máx. 10MB.</div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex flex-wrap gap-3 mt-5 pt-3 border-top">
                        <button type="submit" class="btn btn-submit">
                            <i class="bi bi-check-lg me-2"></i> Inscribir desempleado
                        </button>
                        <button type="reset" class="btn btn-cancel">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Limpiar formulario
                        </button>
                        <a href="inscripciones.php" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="bi bi-list me-2"></i> Ver listado
                        </a>
                    </div>

                    <!-- ===== VOLVER AL PRINCIPIO ===== -->
                    <div class="text-center mt-4">
                        <a href="#formulario" class="btn-back-top">
                            <i class="bi bi-arrow-up-circle-fill"></i> Volver al principio
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== PREVISUALIZACIÓN DE FOTO =====
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

    // ===== GEOGRAFÍA (Guinea Ecuatorial) =====
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

        distritoSelect.innerHTML = '<option value="" disabled selected>Selecciona distrito</option>';
        ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona primero distrito</option>';
        ciudadSelect.disabled = true;

        if (geoData[provincia]) {
            distritoSelect.disabled = false;
            const distritos = geoData[provincia].distritos;
            for (const key in distritos) {
                distritoSelect.innerHTML += `<option value="${key}">${distritos[key]}</option>`;
            }
        } else {
            distritoSelect.disabled = true;
        }
    }

    function updateCiudades() {
        const provincia = document.getElementById('provinciaSelect').value;
        const distrito = document.getElementById('distritoSelect').value;
        const ciudadSelect = document.getElementById('ciudadSelect');

        ciudadSelect.innerHTML = '<option value="" disabled selected>Selecciona ciudad</option>';

        if (geoData[provincia] && geoData[provincia].ciudades[distrito]) {
            ciudadSelect.disabled = false;
            const ciudades = geoData[provincia].ciudades[distrito];
            ciudades.forEach(ciudad => {
                ciudadSelect.innerHTML += `<option value="${ciudad.toLowerCase()}">${ciudad}</option>`;
            });
        } else {
            ciudadSelect.disabled = true;
        }
    }
</script>

<?php
echo '</div>'; // Cierra page-content
echo '</main>'; // Cierra main-content
echo '</div>'; // Cierra wrapper

include_once '../componentes/footer_admin.php';
?>