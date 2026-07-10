<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Buscador de Empleo - Portal de Empleo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1a426a;
            --dark-input: #0f2d4a;
            --accent-teal: #17a2b8;
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .bg-portal {
            background: linear-gradient(rgba(26, 66, 106, 0.9), rgba(26, 66, 106, 0.9)),
                url('https://images.unsplash.com/photo-1521791136368-1a46827d3ad4?auto=format&fit=crop&w=1920&q=80') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #ffffff;
        }

        .main-content {
            flex-grow: 1;
        }

        .form-card {
            background-color: rgba(15, 45, 74, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 30px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        /* Avatar de Previsualización */
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
            border: 4px solid var(--accent-teal);
            object-fit: cover;
            background-color: var(--dark-input);
        }

        .btn-upload-avatar {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: var(--accent-teal);
            color: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid #0f2d4a;
            transition: transform 0.2s;
        }

        .btn-upload-avatar:hover {
            transform: scale(1.1);
        }

        .custom-form-group .form-control,
        .custom-form-group .form-select {
            background-color: var(--dark-input);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #ffffff;
            padding: 10px 15px;
        }

        .custom-form-group .form-control:focus,
        .custom-form-group .form-select:focus {
            background-color: var(--dark-input);
            border-color: var(--accent-teal);
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(23, 162, 184, 0.25);
        }

        .custom-form-group .form-select option {
            background-color: var(--dark-input);
            color: #fff;
        }

        .custom-form-group .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 6px;
        }

        .section-title {
            border-left: 4px solid var(--accent-teal);
            padding-left: 10px;
            font-size: 1.15rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .file-upload-wrapper {
            background-color: rgba(26, 66, 106, 0.5);
            border: 1px dashed rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }

        .experience-block {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            cursor: pointer;
        }

        .btn-submit {
            background-color: #ffffff;
            color: var(--primary-blue);
            font-weight: 700;
            border-radius: 50px;
            padding: 12px 40px;
            border: 2px solid #ffffff;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-submit:hover {
            background-color: transparent;
            color: #ffffff;
        }
    </style>
</head>

<body>

    <div class="bg-portal">
        <header class="text-center py-3 bg-dark bg-opacity-25 border-bottom border-secondary">
            <p class="m-0 small text-uppercase tracking-wider">
                Sistema Nacional de Empleo - Formulario de Alta de Trabajadores
            </p>
        </header>

        <main class="container main-content py-5">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-10">

                    <div class="mb-4">
                        <a href="login_desempleados.php" class="text-white text-decoration-none small"><i class="bi bi-arrow-left me-2"></i> Volver al Inicio de Sesión</a>
                    </div>

                    <div class="form-card">
                        
                        <!-- SECCIÓN FOTO CARNET (PARTE SUPERIOR - OBLIGATORIO) -->
                        <div class="text-center mb-5">
                            <div class="avatar-preview-container">
                                <img src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/icons/person-bounding-box.svg" id="photoPreview" class="avatar-preview" alt="Foto Carnet">
                                <label for="photoInput" class="btn-upload-avatar">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                                <!-- Input required obligatorio para validar antes del envío -->
                                <input type="file" id="photoInput" name="foto_carnet" accept="image/*" class="d-none" onchange="previewImage(event)" required>
                            </div>
                            <h2 class="fw-bold m-0">Expediente Digital de Buscador de Empleo</h2>
                            <p class="text-white-50 small">Sube tu foto carnet obligatoria arriba para activar el expediente</p>
                        </div>

                        <form id="registrationForm" onsubmit="submitForm(event)">
                            
                            <!-- SECCIÓN 1: DATOS PERSONALES (OBLIGATORIOS) -->
                            <h3 class="section-title mb-4">1. Datos Personales</h3>
                            <div class="row g-3 custom-form-group mb-5">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" placeholder="Ej. Juan Carlos" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Apellidos</label>
                                    <input type="text" class="form-control" placeholder="Ej. Nsue Nguema" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Documento de Identidad (DIP / Pasaporte)</label>
                                    <input type="text" class="form-control" placeholder="Número de documento" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono de Contacto</label>
                                    <input type="tel" class="form-control" placeholder="Ej. +240 222 333 444" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Estado Civil</label>
                                    <select class="form-select" required>
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="soltero">Soltero/a</option>
                                        <option value="casado">Casado/a</option>
                                        <option value="divorciado">Divorciado/a</option>
                                        <option value="viudo">Viudo/a</option>
                                    </select>
                                </div>
                            </div>

                            <!-- SECCIÓN 2: UBICACIÓN GEOGRÁFICA (OBLIGATORIOS) -->
                            <h3 class="section-title mb-4">2. Ubicación y Residencia (Guinea Ecuatorial)</h3>
                            <div class="row g-3 custom-form-group mb-5">
                                <div class="col-md-4">
                                    <label class="form-label">Provincia</label>
                                    <select id="provinciaSelect" class="form-select" onchange="updateDistritos()" required>
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
                                    <label class="form-label">Distrito</label>
                                    <select id="distritoSelect" class="form-select" onchange="updateCiudades()" required disabled>
                                        <option value="" disabled selected>Selecciona primero provincia</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ciudad / Municipio</label>
                                    <select id="ciudadSelect" class="form-select" required disabled>
                                        <option value="" disabled selected>Selecciona primero distrito</option>
                                    </select>
                                </div>
                            </div>

                            <!-- SECCIÓN 3: DOCUMENTACIÓN REQUERIDA (MIXTA) -->
                            <h3 class="section-title mb-4">3. Archivos y Documentación Adjunta</h3>
                            <div class="row g-3 custom-form-group mb-5">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-file-earmark-lock me-1"></i> Copia del DIP escaneado <span class="text-danger">*</span></label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="form-control" accept=".pdf, image/*" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-file-earmark-person me-1"></i> Currículum Vitae <span class="text-danger">*</span></label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="form-control" accept=".pdf, .doc, .docx" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-journal-bookmark me-1"></i> Títulos Académicos <span class="text-muted">(Opcional)</span></label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="form-control" accept=".pdf, image/*">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-folder-plus me-1"></i> Otros Documentos <span class="text-muted">(Opcional)</span></label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" class="form-control" accept=".pdf, image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- SECCIÓN 4: EXPERIENCIA LABORAL -->
                            <div class="experience-header mb-4 p-3 bg-dark bg-opacity-25 rounded d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                <div>
                                    <h3 class="h5 m-0 fw-bold"><i class="bi bi-briefcase me-2"></i>¿Posees experiencia laboral previa?</h3>
                                    <p class="m-0 small text-white-50">Desactiva esta opción si buscas tu primer empleo.</p>
                                </div>
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" id="experienceSwitch" checked onchange="toggleExperienceSection()">
                                </div>
                            </div>

                            <!-- Contenedor Condicional de Experiencia -->
                            <div id="experienceWrapper">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="h6 text-uppercase tracking-wider text-white-50 m-0">Historial Laboral</h4>
                                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="addExperience()">
                                        <i class="bi bi-plus-circle me-1"></i> Añadir Bloque
                                    </button>
                                </div>

                                <div id="experienceContainer">
                                    <div class="experience-block p-4 mb-3 position-relative custom-form-group">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nombre de la Empresa</label>
                                                <input type="text" class="form-control exp-input" placeholder="Ej. Getesa, Somagec...">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Puesto / Cargo</label>
                                                <input type="text" class="form-control exp-input" placeholder="Ej. Técnico, Cajero...">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha de Inicio</label>
                                                <input type="date" class="form-control exp-input">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Fecha de Fin</label>
                                                <input type="date" class="form-control exp-input">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Descripción de funciones</label>
                                                <textarea class="form-control exp-input" rows="3" placeholder="Tareas realizadas..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center pt-4 border-top border-secondary mt-5">
                                <button type="submit" class="btn btn-submit px-5">Finalizar Registro</button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-dark bg-opacity-50 text-center py-3 text-white-50 small mt-auto">
            &copy; 2026 Ministerio de Trabajo y Empleo. Sección de Registro Estatal.
        </footer>
    </div>

    <script>
        // 1. LÓGICA DE PREVISUALIZACIÓN DE FOTO CARNET
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('photoPreview');
                output.src = reader.result;
            }
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        // 2. BASE DE DATOS GEOGRÁFICA INTERNA (Guinea Ecuatorial)
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

        // 3. CONTROL DINÁMICO DE VALIDACIONES (EXPERIENCIA LABORAL)
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

        function addExperience() {
            const container = document.getElementById('experienceContainer');
            const newBlock = document.createElement('div');
            newBlock.className = 'experience-block p-4 mb-3 position-relative custom-form-group';
            
            // Si el switch está activo, los nuevos bloques creados heredan directamente el atributo required
            const isRequired = document.getElementById('experienceSwitch').checked ? 'required' : '';

            newBlock.innerHTML = `
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" onclick="this.parentElement.remove()"></button>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre de la Empresa</label>
                        <input type="text" class="form-control exp-input" placeholder="Ej. Empresa SA" ${isRequired}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Puesto / Cargo</label>
                        <input type="text" class="form-control exp-input" placeholder="Ej. Especialista" ${isRequired}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control exp-input" ${isRequired}>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control exp-input" ${isRequired}>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción de funciones</label>
                        <textarea class="form-control exp-input" rows="3" placeholder="Tareas..." ${isRequired}></textarea>
                    </div>
                </div>
            `;
            container.appendChild(newBlock);
        }

        function submitForm(event) {
            event.preventDefault();
            alert('¡Formulario validado con éxito! Todos los campos obligatorios procesados de forma segura.');
        }

        // Ejecutar configuración inicial al cargar la página
        toggleExperienceSection();
    </script>
</body>
</html>