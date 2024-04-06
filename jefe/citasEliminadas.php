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

function isPastDate($date, $time)
{
    $dateTime = new DateTime($date . ' ' . $time);
    $now = new DateTime();
    return $dateTime < $now;
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

        <!-- Tema obscuro-->
        <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>


    <script defer>
    </script>

    <title>Citas eliminadas</title>
    <style>
        /* Estilos para los botones */
        .btn {
            border-radius: 100px; /* Hacer los botones más redondos */
            border-width: 6px; /* Bordes más gruesos */
        }

        /* Cambio de color para los botones */
        .btn-secondary,
        .btn-red,
        .btn-personalizado,
        .btn-custom {
            background-color: transparent !important; /* Botón transparente */
            border-color: transparent !important; /* Color beige */
            color: #ffffff !important; /* Color beige */
        }

        /* Estilos personalizados para los botones */
        .btn-custom {
            font-size: 24px;
            padding: 15px 40px;
            margin: 250px 20px 80px; /* Margen superior de 60px, margen derecho e izquierdo de 20px, y margen inferior de 80px */
            height: 350px;
            width: 350px;
            font-weight: bold;
            font-size: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Alinear contenido al centro verticalmente */
            align-items: center;
            background-color: transparent !important; /* Botón transparente */
            border-color: #ffffff !important; /* Color beige */
            color: #ffffff !important; /* Color beige */
        }

        .btn-custom:hover {
            font-size: 24px;
            padding: 15px 50px;
            margin: 250px 20px 80px; /* Margen superior de 60px, margen derecho e izquierdo de 20px, y margen inferior de 80px */
            height: 350px;
            width: 350px;
            font-weight: bold;
            font-size: 30px;
            background-color: transparent !important; /* Botón transparente */
            border-color: #ffffff !important; /* Color beige */
            color: #ffffff !important; /* Color beige */
        }

        /* Estilos personalizados para los iconos */
        .btn-custom i {
            font-size: 90px;
            /* Ajuste el tamaño del icono aquí */
            color: #ffffff !important; /* Color beige */
            background-color: transparent !important; /* Fondo transparente */
        }

        .btn-custom i:hover {
            font-size: 100px;
            /* Ajuste el tamaño del icono aquí */
        }

        body {
        margin: 0;
        font-family:"Segoe UI",Benedict;
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

.btn-dark {
  background-color: #8B1D35;
  border-color: #8B1D35;
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
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./indexJefe.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./reportesMenu.php"><i class="bi bi-file-earmark-text"></i> Reportes</a>
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
        <div class="d-flex justify-content-center">
            <h1><i class="bi bi-calendar-check-fill" style="color: #088C4F;"></i> <b>Citas eliminadas</b></h1>
        </div>
        <div class="table-responsive">
            <br>
            <table id="citasTable" class="table table-striped table-light">
            <thead style="background-color: #8B1D35;">
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

    <script>

$(document).on('click', '.editar-cita', function() {
    const id_citas = $(this).data('id');

    $.ajax({
        url: './php/activarCita.php',
        method: 'POST',
        data: {
            id_citas: id_citas,
        },
        success: function(response) {
            Swal.fire({
                title: 'Cita activada',
                text: 'La cita ha sido activada exitosamente',
                icon: 'success',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then((result) => {
    location.reload();
});

        },
        error: function() {
            Swal.fire({
                title: 'Error',
                text: 'Hubo un error al activar la cita',
                icon: 'error',
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }
    });
});


        $(document).ready(function() {
            function cargarCitas() {

                $.ajax({
                    url: './php/cargarCitasEliminadas.php',
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
                <button class="btn btn-dark btn-personalizado btn-sm editar-cita" data-id="${cita.id_citas}" data-fecha="${cita.fecha}" data-hora="${cita.hora}" data-tipo="${cita.tipo_cita}" data-problema="${cita.tipo}">Activar</button>
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


            // Inicializar DataTables aquí, después de cargar los datos en la tabla
            $('#citasTable').DataTable({
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"table-responsive"t>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json"
                },
                "pagingType": "full_numbers",
                "pageLength": 10,
                "lengthMenu": [10, 15, 20, 25, 50, 100],
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
    </script>
</body>

</html>