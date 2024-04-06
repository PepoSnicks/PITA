<?php
include("../php/conexion.php");

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$idnivel = $_SESSION['idnivel'];

if ($usuario == null || $usuario == '' || $idnivel != 1 || $nombreUsuario == '') {
    header("location:../index.php");
    die();
}

// Get profile image URL
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT imagen_de_perfil FROM profesores WHERE nempleado = '" . $_SESSION['user'] . "'";
$result = $conn->query($sql);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$rutaImagen = $baseUrl . "/imagenes/default.jpg";

if ($result->num_rows > 0) {
    $fila = $result->fetch_assoc();
    if (!empty($fila['imagen_de_perfil'])) {
        $rutaImagen = $baseUrl . "/" . $fila['imagen_de_perfil'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <!-- Librerías CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Luego Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

    <!-- Y finalmente Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" />

    <!-- DataTables JavaScript -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

    <title>Reportes alumnos</title>

        <!-- Tema obscuro-->
        <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>



    <style>

        /* Estilos para los botones */
       .btn {
            border-radius: 100px; /* Hacer los botones más redondos */
            background-color: transparent; /* Fondo transparente */
        }

.btn-custom i {
  font-size: 90px;
  margin-bottom: -10px;
  color: #ffffff !important;
}

/* Estilos personalizados para los botones */
.btn-custom {
            font-size: 24px;
            padding: 15px 40px;
            margin: 10px;
            height: 300px;
            width: 300px;
            font-weight: bold;
            border: 6px solid #ffffff !important; /* Ahora el borde es más grueso */
            color: #ffffff !important; /* Cambia el color del texto a beige */
            background-color: transparent !important; /* Botón transparente */
        }

        .btn-custom:hover {
            font-size: 25px;
            padding: 15px 50px;
            margin: 10px;
            height: 300px;
            width: 300px;
            font-weight: bold;
            border: 6px solid #ffffff !important; /* Ahora el borde es más grueso */
            color: #ffffff !important; /* Cambia el color del texto a beige */
            background-color: transparent !important; /* Botón transparente */
        }
    


        body {
  margin: 0;
  font-family: "Segoe UI", Benedict;
  font-size: 0.9rem;
  font-weight: 400;
  line-height: 1;
  color: #fff;
  text-align: left;
  background-color: #fff;
}
.btn {
  border-radius: 100px;
  color: #fff !important;
}
/* Estilos para el estado :hover (al pasar el cursor) */
.btn-custom:hover {
    background-color: #8B1D35 !important;
    border-color: #8B1D35 !important;
    color: #FFFFFF !important;
}

.hover-effect:hover {
    transform: rotate(360deg) scale(1.1); /* Rota el botón 10 grados y aumenta su escala al 110% */
    transition: transform 0.7s ease; /* Añade una transición suave */
}

.hover-effect1:hover {
    transform: scale(1.1); /* Hace que el elemento se escale al 110% del tamaño original */
    transition: transform 0.3s ease; /* Agrega una transición suave */
}

.table-dark {
  color: #fff;
  background-color: #8B1D35;
}

.dataTables_wrapper .pagination .page-item .page-link {
    background-color: #8B1D35;
    border-color: #8B1D35;
    color: #fff;
}

.btn-maroon:active,
.btn-maroon:focus,
.btn-vino:active,
.btn-vino:focus {
    outline: none !important; /* Importante para anular el estilo predeterminado */
    box-shadow: none !important; /* Importante para anular el estilo predeterminado (si se aplica) */
}

/* Agrega estilos para el botón .btn-vino y su icono */
.btn-vino,
.btn-vino i {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    color: inherit !important; /* Añade esta línea para heredar el color del texto del botón e icono */
    background-color: transparent !important; /* Añade esta línea para hacer el fondo del icono transparente */
}

/* Agrega estilos para el estado :hover del botón .btn-vino y su icono */
.btn-vino:hover,
.btn-vino:hover i {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    color: inherit !important; /* Añade esta línea para heredar el color del texto del botón e icono en estado hover */
    background-color: transparent !important; /* Añade esta línea para hacer el fondo del icono transparente en estado hover */
}

.btn-dark {
  background-color: #8B1D35;
  border-color: #8B1D35;
}

/* Estilo base para el botón editar */
.btn-editar {
    background-color: #088C4F;
    border-color: #088C4F;
}

/* Estilo para el botón editar al pasar el cursor sobre él */
.btn-editar:hover {
    background-color: #00BF13; /* Cambia el color al pasar el cursor */
    border-color: #00BF13; /* Cambia el color del borde al pasar el cursor */
}

/* Estilo base para el botón eliminar */
.btn-eliminar {
    background-color: #8B1D35;
    border-color: #8B1D35;
}

/* Estilo para el botón eliminar al pasar el cursor sobre él */
.btn-eliminar:hover {
    background-color: #dc3545; /* Cambia el color al pasar el cursor */
    border-color: #dc3545; /* Cambia el color del borde al pasar el cursor */
}

.modal-content {
  position: relative;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-direction: column;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0,0,0,.2);
  border-radius: .3rem;
  outline: 0;
  color: #000;
}

.btn.btn-personalizado{
  border-radius: 100px;
  background-color: #8B1D35;
}

.modal-footer > :not(:first-child) {
  margin-left: .25rem;
  background-color: #8B1D35;
}

.close,
.close:focus {
    color: #000 !important;
    text-shadow: none !important;
    outline: none !important; /* Agrega esta línea para quitar el contorno azul */
}

.close:hover,
.close:focus:hover {
    color: #000 !important;
    outline: none !important; /* Agrega esta línea para quitar el contorno azul */
}

.modal-header .close {
    outline: none !important;
    box-shadow: none !important;
}


    </style>
</head>

<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-vino bg-maroon">
            <div class="container">
                <a class="navbar-brand hover-effect1" href="indexJefe.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
            <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexJefe.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                <button id="mensajesBtn" class="btn btn-vino mr-2 hover-effect" onclick="window.location.href='./mensajes.php'">
                    <i class="bi bi-envelope-fill" data-toggle="tooltip" title="Mensajes"></i>
                </button>
                <button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                </button>
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./reportesMenu.php'">Regresar</button>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
    <a class="btn btn-vino dropdown-toggle mr-2 hover-effect1 d-flex align-items-center" href="#" id="usuarioDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <?php
        // Obtener la ruta de la imagen de perfil del usuario desde la base de datos
        $rutaImagen = $fila['imagen_de_perfil'];
        // Verificar si la ruta de la imagen de perfil no está vacía
        if (!empty($rutaImagen)) {
            // Si hay una imagen de perfil personalizada, mostrarla
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
            echo "<img src='" . $baseUrl . "/" . $rutaImagen . "' alt='Imagen de perfil' class='rounded-circle mr-2 img-top' style='width: 30px; height: 30px;'>";
        } else {
            // Si no hay una imagen de perfil personalizada, mostrar la imagen por defecto
            echo "<img src='../imagenes/default.jpg' alt='Imagen de perfil' class='rounded-circle mr-2 img-top' style='width: 30px; height: 30px;'>";
        }
        ?>
                                <?php echo $nombreUsuario; ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="usuarioDropdown">
                            <a class="dropdown-item hover-effect1" href="./perfil.php"><i class="bi bi-person"></i> Perfil</a>
                            <a class="dropdown-item hover-effect1" href="./profesoresMenu.php"><i class="bi bi-person-check"></i> Profesores</a>
                                <a class="dropdown-item hover-effect1" href="./alumnos.php"><i class="bi bi-people"></i> Alumnos</a>
                                <a class="dropdown-item hover-effect1" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
                                <a class="dropdown-item hover-effect1" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <br>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center" data-toggle="modal" data-target="#alumnosModal"><i class="bi bi-person-fill"></i><span>Alumnos individual</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center" data-toggle="modal" data-target="#gruposModal"><i class="bi bi-people-fill"></i><span>Grupo</span></button>
            </div>
        </div>
    </div>

   <!-- Modal con formulario para grupos -->
<div class="modal fade" id="gruposModal" tabindex="-1" role="dialog" aria-labelledby="gruposModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark-theme" id="gruposModalLabel" style="color: #000;">Filtrar grupos</h5>
                <button type="button" class="close btn btn-negro" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
</button>

            </div>
            <form id="filtrarGruposForm">
                <div class="modal-body text-dark-theme">
                    <!-- Campos de generación, grupo y periodo de inicio -->
                    <div class="form-group">
                        <label for="generacion" style="color: #000;">Generación</label>
                        <input type="number" class="form-control" id="generacion" required min="2023" max="2050">
                    </div>
                    <div class="form-group">
                        <label for="grupo" style="color: #000;">Grupo</label>
                        <input type="text" class="form-control" id="grupo" required>
                    </div>
                    <div class="form-group">
                        <label for="periodo_inicio" style="color: #000;">Periodo de inicio</label>
                        <select class="form-control" id="periodo_inicio" required>
                            <option value="" disabled selected>Selecciona un periodo</option>
                            <option value="1">Enero - Abril</option>
                            <option value="2">Mayo - Agosto</option>
                            <option value="3">Septiembre - Diciembre</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-personalizado" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-personalizado2" onclick="generarPDFGrupo(document.getElementById('generacion').value, document.getElementById('grupo').value, document.getElementById('periodo_inicio').value)">Generar PDF de grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <!-- Modal con formulario alumno individual -->
    <div class="modal fade" id="alumnosModal" tabindex="-1" role="dialog" aria-labelledby="alumnosModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="alumnosModalLabel" style="color: #000;">Filtrar alumnos</h5>
                    <button type="button" class="close btn btn-negro" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true" style="font-size: 1.5em;">&times;</span>
</button>

                </div>
                <form id="filtrarAlumnosForm">
                    <div class="modal-body text-dark-theme">
                        <!-- Campo de matrícula (opcional) -->
                        <div class="form-group">
                            <label for="matricula" style="color: #000;">Matrícula</label>
                            <input type="number" class="form-control" id="matricula">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-personalizado" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-personalizado2">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="d-flex justify-content-center">
            <h1><i class="bi bi-people-fill" style="color: #088C4F;"></i><b>Usuarios</b></h1>
        </div>
        <div class="table-responsive">
            <br>
            <table id="alumnos" class="table table-striped table-light">
            <thead style="background-color: #8b1d35;">
                    <tr>
                        <th style="color: white;">Matrícula</th>
                        <th style="color: white;">Nombre</th>
                        <th style="color: white;">Carrera</th>
                        <th style="color: white;">Generación</th>
                        <th style="color: white;">Grupo</th>
                        <th style="color: white;">Periodo de inicio</th>
                        <th style="color: white;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaAlumnos">
                    <!-- Aquí se agregarán las filas de la tabla dinámicamente con JavaScript en base a la búsqueda -->
                </tbody>
            </table>
        </div>
    </div>

</body>
<script>
    const generacion = document.getElementById('generacion').value;
    const grupo = document.getElementById('grupo').value;

    function generarPDFGrupo(generacion, grupo, periodo_inicio) {
        if (generacion === "" || grupo === "" || periodo_inicio === "") {
            // Mostrar mensaje de error con SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor, completa todos los campos del formulario',
                timer: 2000,
                showCancelButton: false,
                timerProgressBar: true,

                showConfirmButton: false

            });
            return;
        }

        const formData = new FormData();
        formData.append('generacion', generacion);
        formData.append('grupo', grupo);
        formData.append('periodo_inicio', periodo_inicio);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', './php/generarReporteGrupo.php', true);
        xhr.responseType = 'arraybuffer'; // Cambiar a arraybuffer

        xhr.onload = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const contentType = xhr.getResponseHeader('Content-Type');
                    if (contentType == 'application/json') {
                        const errorData = JSON.parse(new TextDecoder().decode(xhr.response));
                        Swal.fire({
                            icon: 'error',
                            title: 'No se encontraron coincidencias',
                            text: errorData.error,
                            timer: 2000,
                            showCancelButton: false,
                            timerProgressBar: true,

                            showConfirmButton: false
                        });
                    } else {
                        const pdfBlob = new Blob([xhr.response], {
                            type: 'application/pdf'
                        });
                        const pdfUrl = URL.createObjectURL(pdfBlob);
                        const link = document.createElement('a');
                        link.href = pdfUrl;
                        link.target = '_blank'; // Abre el enlace en una nueva pestaña
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar el PDF'
                    });
                }
            }
        };

        xhr.send(formData);

    }


    function buscarAlumnos(e) {
        e.preventDefault();

        // Obtener el valor del usuario desde PHP
        const usuario = "<?php echo $usuario; ?>";

        // Obtener los valores del formulario
        const matricula = document.getElementById('matricula').value;

        // Crear un objeto FormData para enviar los datos del formulario
        const formData = new FormData();
        formData.append('usuario', usuario);
        formData.append('matricula', matricula);

        // Realizar una solicitud AJAX al archivo buscar_alumnos.php
        const xhr = new XMLHttpRequest();
        xhr.open('POST', './php/buscarAlumnoReportes.php', true);
        xhr.responseType = 'json';

        xhr.onload = function() {
            if (xhr.status === 200) {
                const alumnos = xhr.response;

                // Agregar los alumnos encontrados a la tabla
                const tablaAlumnos = document.getElementById('tablaAlumnos');
                tablaAlumnos.innerHTML = '';
                alumnos.forEach(alumno => {
                    const tr = document.createElement('tr');

                    tr.innerHTML = `
                    <td>${alumno.matricula}</td>
                    <td>${alumno.nombre}</td>
                    <td>${alumno.carrera_nombre}</td>
                    <td>${alumno.generacion}</td>
                    <td>${alumno.grupo}</td>
                    <td>${alumno.nombre_periodo}</td>
                    <td>
                    <form id="formGenerarPDF" action="./php/generarReporteAlumno.php" method="post">
    <input type="hidden" id="matricula" name="matricula" value="${alumno.matricula}">
    <input type="hidden" id="nombre" name="nombre" value="${alumno.nombre}">
    <input type="hidden" id="carrera_nombre" name="carrera_nombre" value="${alumno.carrera_nombre}">
    <input type="hidden" id="generacion" name="generacion" value="${alumno.generacion}">
    <input type="hidden" id="grupo" name="grupo" value="${alumno.grupo}">
    <input type="hidden" id="nombre_periodo" name="nombre_periodo" value="${alumno.nombre_periodo}">
    <button type="submit" class="btn btn-personalizado2">
        <i class='bi bi-file-earmark-pdf-fill'></i>
        Generar PDF del alumno
    </button>
</form>
    </td>
                `;

                    tablaAlumnos.appendChild(tr);
                });
            }
        };

        xhr.send(formData);
    }

    function generarPDF(matricula, nombre, carrera_nombre, generacion, grupo, nombre_periodo) {
        const formData = new FormData();
        formData.append('matricula', matricula);
        formData.append('nombre', nombre);
        formData.append('carrera_nombre', carrera_nombre);
        formData.append('generacion', generacion);
        formData.append('grupo', grupo);
        formData.append('nombre_periodo', nombre_periodo);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', './php/generarReporteAlumno.php', true);
        xhr.responseType = 'blob'; // La respuesta será un archivo PDF

        xhr.onload = function() {
            if (xhr.status === 200) {
                const pdfBlob = xhr.response;
                const pdfUrl = URL.createObjectURL(pdfBlob);
                const link = document.createElement('a');
                link.href = pdfUrl;
                link.target = '_blank'; // Abre el enlace en una nueva pestaña
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        };

        xhr.send(formData);
    }


    // No olvides agregar el event listener al formulario
    document.getElementById('filtrarAlumnosForm').addEventListener('submit', buscarAlumnos);

    // Inicializar DataTables aquí, después de cargar los datos en la tabla
    $('#alumnos').DataTable({
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
            '<"table-responsive"t>' +
            '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json"
        },
        "pagingType": "full_numbers",
        "pageLength": 2,
        "lengthMenu": [2, 4, 6, 8],
        "order": [],
        "columnDefs": [{
            "orderable": false,
            "targets": -1
        }],
        "oLanguage": {
            "sSearch": "Buscar:",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "No hay citas canalizadas",
            "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sLoadingRecords": "Cargando...",
            "sProcessing": "Procesando...",
            "sPaginate": {
                "sFirst": '<i class="bi bi-chevron-double-left"></i>',
                "sLast": '<i class="bi bi-chevron-double-right"></i>',
                "sNext": '<i class="bi bi-chevron-right"></i>',
                "sPrevious": '<i class="bi bi-chevron-left"></i>'
            }
        },
        "drawCallback": function(settings) {
            $('ul.pagination').addClass('pagination-sm');
            $('ul.pagination li a').addClass('btn btn-dark btn-personalizado');
            $('ul.pagination li.active a').addClass('active');
        },
        "initComplete": function() {
            $('ul.pagination').addClass('pagination-sm');
            $('ul.pagination li a').addClass('btn btn-dark btn-personalizado');
            $('ul.pagination li.active a').addClass('active');
        }
    });

    function checkForNewMessages() {
        // Reemplazar esta URL con la ruta del archivo PHP que verifica si hay mensajes nuevos
        const checkNewMessagesUrl = "./php/nuevosMensajes.php";

        fetch(checkNewMessagesUrl)
            .then(response => response.json())
            .then(data => {
                const mensajesBtn = document.getElementById("mensajesBtn");

                if (data.newMessages) {
                    mensajesBtn.classList.add("btn-red");
                    mensajesBtn.classList.remove("btn-dark");
                } else {
                    mensajesBtn.classList.remove("btn-red");
                    mensajesBtn.classList.add("btn-dark");
                }
            })
            .catch(error => console.error("Error al verificar mensajes nuevos:", error));
    }

    // Verificar mensajes nuevos cada 10 segundos
    setInterval(checkForNewMessages, 10000);

    // Verificar mensajes nuevos al cargar la página
    checkForNewMessages();

    function openPopup(url) {
        window.open(url, "popupWindow", "width=800, height=600, scrollbars=yes");
    }
</script>

</html>