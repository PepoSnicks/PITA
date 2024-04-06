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
  color: #FFFFFF !important;
}
/* Estilos para el estado :hover (al pasar el cursor) */
.btn-custom:hover {
    background-color: #8B1D35 !important;
    border-color: #8B1D35 !important;
    color: #FFFFFF !important;
}

        .btn-red {
            background-color: #ff7f7f;
        }

        /* Estilos personalizados para los botones */
        .btn-custom {
            font-size: 16px;
            padding: 15px 40px;
            margin: 20px;
            height: 150px;
            width: 350px;
            font-weight: bold;
        }

        .btn-custom:hover {
            font-size: 16px;
            padding: 15px 50px;
            margin: 20px;
            height: 150px;
            width: 350px;
            font-weight: bold;
            background-color: #0096C7;
            border-color: #0096C7;
        }

        /* Estilos personalizados para los iconos */
        .btn-custom i {
            font-size: 50px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: 10px;
        }

        .btn-custom i:hover {
            font-size: 60px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: 10px;
        }

        .btn-personalizado {
            background-color: #343a40;
            border-color: #343a40;
            color: #fff;
        }

        .btn-personalizado2 {
            background-color: #C5E5A4;
            border-color: #C5E5A4;
        }

        .btn-personalizado2:hover {
            background-color: #28a745;
            border-color: #28a745;
        }


        .btn-personalizado:hover {
            background-color: #0096C7;
            border-color: #0096C7;
            color: #fff;
        }

        .btn-personalizado-eliminar {
            background-color: #ff7f7f;
            border-color: #ff7f7f;
        }

        .btn-personalizado-eliminar:hover {
            background-color: #ff7f7f;
            border-color: #ff7f7f;
        }

        .dataTables_wrapper .pagination .page-item .page-link {
            background-color: #8b1d35;
            border-color: #8b1d35;
            color: #fff;

        }

        .dataTables_wrapper .pagination .page-item .page-link:hover {
            background-color: #8b1d35;
            border-color: #8b1d35;
            color: #fff;

        }

        .custom-bg-success {
            background-color: #C5E5A4;
            color: #000;
        }

        .custom-bg-danger {
            background-color: #ff7f7f;
            color: #000;
        }

        .custom-bg-resuelto {
            background-color: #FFEFBF;
            color: #000;
        }
        .custom-bg-no-asisitio{
            background-color: #D3D3D3;
            color: #000;
        }
        
        .hover-effect:hover {
    transform: rotate(360deg) scale(1.1); /* Rota el botón 10 grados y aumenta su escala al 110% */
    transition: transform 0.7s ease; /* Añade una transición suave */
}
.hover-effect1:hover {
    transform: scale(1.1); /* Hace que el elemento se escale al 110% del tamaño original */
    transition: transform 0.3s ease; /* Agrega una transición suave */
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


    <!-- Modal con formulario para grupos -->
    <div class="modal fade" id="gruposModal" tabindex="-1" role="dialog" aria-labelledby="gruposModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gruposModalLabel">Filtrar grupos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="filtrarGruposForm">
                    <div class="modal-body">
                        <!-- Campos de generación, grupo y periodo de inicio -->
                        <div class="form-group">
                            <label for="grupo">Grupo</label>
                            <input type="text" class="form-control" id="grupo">
                        </div>
                                                <div class="form-group">
                            <label for="generacion">Generación</label>
                            <input type="number" class="form-control" id="generacion">
                        </div>
                        <div class="form-group">
                            <label for="periodo_inicio">Periodo de inicio</label>
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
                        <button type="button" class="btn btn-personalizado2" onclick="generarPDFCita(document.getElementById('generacion').value, document.getElementById('grupo').value, document.getElementById('periodo_inicio').value)">Generar PDF de grupo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="d-flex justify-content-center">
            <h2><i class="bi bi-calendar-check-fill" style="color: #088C4F;"></i> <b>Citas</b></h2>
        </div>
        <div class="table-responsive">
            <br>
            <table id="citasTable" class="table table-striped table-light">
            <thead style="background-color: #8b1d35;">
                    <tr class="text-center">
                        <th style="color: white;">ID de cita</th>
                        <th style="color: white;">Periodo</th>
                        <th style="color: white;">Fecha</th>
                        <th style="color: white;">Hora</th>
                        <th style="color: white;">Matrícula</th>
                        <th style="color: white;">Nombre del alumno</th>
                        <th style="color: white;">Profesor asignado</th>
                        <th style="color: white;">Tipo de cita</th> <!-- Agrega esta columna -->
                        <th style="color: white;">Tipo de problema</th>
                        <th style="color: white;">Status</th> <!-- Agrega esta columna -->
                        <th style="color: white;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="citasCanalizadas">
                    <!-- Aquí se mostrarán las citas canalizadas -->
                </tbody>
            </table>
        </div>
    </div>


</body>
<script>



    $(document).ready(function() {
        function cargarCitas() {

            $.ajax({
                url: './php/cargarCitaCanalizadasReporte.php',
                method: 'GET',
                dataType: 'json',
                success: function(citas) {
                    let citasCanalizadas = '';
                    citas.forEach(cita => {
                        let statusClass = '';
                        let statusText = '';

                        if (cita.status == 0) {
                            statusClass = 'custom-bg-danger';
                            statusText = 'Inactivo';
                        } else if (cita.status == 1) {
                            statusClass = 'custom-bg-success';
                            statusText = 'Activo';
                        } else if (cita.status == 2) {
                            statusClass = 'custom-bg-resuelto';
                            statusText = 'Resuelta';
                        } else if (cita.status == 3) {
                            statusClass = 'custom-bg-no-asisitio';
                            statusText = 'No asisitió';
                        }

                        citasCanalizadas += `<tr class="text-center">
                <td>${cita.id_citas}</td>
                <td>${cita.periodo == 1 ? 'Enero-Abril' : cita.periodo == 2 ? 'Mayo-Agosto' : 'Septiembre-Diciembre'}</td>
                <td>${cita.fecha}</td>
                <td>${cita.hora.substring(0, 5)}</td>
                <td>${cita.matricula}</td>
                <td>${cita.nombre_alumno}</td>
                <td>${cita.nombre_profesor}</td>
                <td>${cita.nombre_tipo}</td>
                <td>${cita.tipo_problema}</td>
                <td class="${statusClass}">${statusText}</td>
                <td>
                <button class="btns btn-personalizado2 btn-sm generar-cita-pdf" data-id="${cita.id_citas}" data-fecha="${cita.fecha}" data-hora="${cita.hora}" data-tipo="${cita.tipo_cita}" data-problema="${cita.tipo}"><i class='bi bi-file-earmark-pdf-fill'></i></button>
                </td>
                </tr>`;
                    });
                    $('#citasCanalizadas').html(citasCanalizadas);

                    // Actualizar DataTables en lugar de reinicializarlo
                    $('#citasTable').DataTable().clear().draw();
                    $('#citasTable').DataTable().rows.add($(citasCanalizadas)).draw();

                },
                error: function() {
                    console.log('Error al cargar las citas canalizadas');
                }
            });
        }

        $(document).on('click', '.generar-cita-pdf', function() {
            const id_cita = $(this).data('id');
            generarPDFCita(id_cita);
        });


        const generacion = document.getElementById('generacion').value;
        const grupo = document.getElementById('grupo').value;

        function generarPDFCita(id_cita) {
            const formData = new FormData();
            formData.append('id_cita', id_cita);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', './php/generarReporteCita.php', true);
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



        // Inicializar DataTables aquí, después de cargar los datos en la tabla
        $('#citasTable').DataTable({
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json"
            },
            "pagingType": "full_numbers",
            "pageLength": 8,
            "lengthMenu": [2,4,6,8],
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

        // Cargar las citas al cargar la página
        cargarCitas();
        setInterval(cargarCitas, 10000);

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