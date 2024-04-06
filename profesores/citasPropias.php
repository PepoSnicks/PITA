<?php

include("../php/conexion.php");

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$idnivel = $_SESSION['idnivel'];



if ($usuario == null || $usuario == '' || $idnivel != 4 || $nombreUsuario == '') {
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

    <!-- Modo obscuro -->
    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="../js/darktheme.js" defer></script>

    <!-- Revision de las citas -->
    <script src="../js/checkCitas.js"></script>
    
    <!-- Revision de los mensajes -->
    <script src="../js/checkMessage.js"></script>

    <script defer>

    </script>

    <title>Citas</title>
    <style>

.btn-personalizado:hover {
            background-color: #0096C7;
            border-color: #0096C7;
        }

        .btn-personalizado-eliminar {
            background-color: #ff7f7f;
            border-color: #ff7f7f;
        }

        .btn-personalizado-eliminar:hover {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-personalizado-strike:hover {
            background-color: #545454;
            border-color: #545454;
            color: #000;
        }

        .btn-personalizado-strike {
            background-color: #a6a6a6;
            border-color: #a6a6a6;
            color: #000;
        }

        .btn-resuelto {
            background-color: #94E88E;
            border-color: #94E88E;
            color: #000;

        }

        .btn-resuelto:hover {
            background-color: #00BF13;
            border-color: #00BF13;
            color: #000;
        }

        .btn-red {
            background-color: #ff7f7f;
        }

        .dataTables_wrapper .pagination .page-item .page-link {
            background-color: #343a40;
            border-color: #343a40;
            color: #fff;

        }

        .dataTables_wrapper .pagination .page-item .page-link:hover {
            background-color: #0096C7;
            border-color: #0096C7;
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

        .custom-bg-no-asistio{
            background-color: #DDD;
            color: #000;
        }

        body {
  margin: 0;
  font-family: "Segoe UI", Benedict;
  font-size: 1rem;
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

    </style>
</head>

<body>
    <header>
    <nav class="navbar navbar-expand-lg navbar-vino bg-maroon">
            <div class="container">
                <a class="navbar-brand hover-effect1" href="indexProfesor.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
            <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexProfesor.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                <button id="mensajesBtn" class="btn btn-vino mr-2 hover-effect" onclick="window.location.href='./mensajes.php'">
                    <i class="bi bi-envelope-fill" data-toggle="tooltip" title="Mensajes"></i>
                </button>
                <button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                </button>
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./citasMenu.php'">Regresar</button>
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
                            <a class="dropdown-item hover-effect1" href="./reportes.php"><i class="bi bi-file-earmark-text"></i> Reportes</a>
                                <a class="dropdown-item hover-effect1" href="./horarioProfesor.php"><i class="bi bi-clock"></i> Horario</a>
                                <a class="dropdown-item hover-effect1" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
                                <a class="dropdown-item hover-effect1" href="./medallas.php"><i class="bi bi-award"></i> Medallas</a>
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
        <br>
        <br>
        <h1 class="text-center" style="color: #fff;"><i class="bi bi-calendar-check-fill" style="color: #088C4F;"></i> <b>Citas académicas</b></h1>
        </br>
        </br>
        <div class="table-responsive">
            <br>
            <table id="citasTable" class="table table-striped table-light">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>ID de cita</th>
                        <th>Periodos</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Matrícula</th>
                        <th>Nombre del alumno</th>
                        <th>Tipo de cita</th> <!-- Agrega esta columna -->
                        <th>Tipo de problema</th>
                        <th>Status</th> <!-- Agrega esta columna -->
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="citasCanalizadas">
                    <!-- Aquí se mostrarán las citas canalizadas -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {


            $(document).on('click', '.eliminar-btn', function() {
                const citaId = $(this).data('id');
                const citaNombre = $(this).data('nombre');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: `¿Deseas eliminar la cita de ${citaNombre}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#ff7f7f',
                    cancelButtonColor: '#343a40'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Eliminar cita:', citaId);

                        $.ajax({
                            url: `./php/eliminarCita.php`,
                            type: 'GET',
                            data: {
                                id_cita: citaId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        title: 'Eliminado',
                                        text: response.message,
                                        icon: 'success',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Aquí puedes actualizar la tabla de citas o hacer algo más cuando el temporizador termine
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Hubo un problema al eliminar la cita. Por favor, inténtalo de nuevo más tarde.',
                                    icon: 'error',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                });
                            }
                        });
                    }
                });
            });

            // Manejar el evento de clic en el botón de canalizar
            $(document).on('click', '.canalizar-btn', function() {
                const citaId = $(this).data('id');
                const citaNombre = $(this).data('nombre');
                const citaMatricula = $(this).parent().siblings().eq(2).text();

                // Rellenar el formulario con la información de la cita
                $('#matricula').val(citaMatricula);

                // Cargar profesores basados en el tipo de cita seleccionado
                cargarProfesores($('#cita-tipo').val());
                cargarTiposProblema($('#problema').val());

                // Actualizar la lista de profesores y problemas cuando se cambie el tipo de cita
                $('#cita-tipo').on('change', function() {
                    cargarProfesores($(this).val());
                    cargarTiposProblema($(this).val());
                });
            });
        });

        $(document).ready(function() {
            function cargarCitas() {

                $.ajax({
                    url: './php/cargarCitaPropias.php',
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
}  else if (cita.status == 3) {
    statusClass = 'custom-bg-no-asistio';
    statusText = 'No asistió';
}

const resueltoButton = cita.status != 2 ? `<button class="btn btn-dark btn-resuelto btn-sm resuelto-btn" data-id="${cita.id_citas}" data-nombre="${cita.nombre_alumno}"><i class="bi bi-check-lg"></i></button>` : '';

const fechaHoraCita = new Date(cita.fecha + 'T' + cita.hora);
const now = new Date();

let strikeButton = '';

// Con este código modificado
if (now >= fechaHoraCita && cita.status != 3 && cita.strikes <= 3) {
    strikeButton = `<button class="btn btn-danger btn-sm btn-personalizado-strike strike-btn" data-id="${cita.id_citas}" data-nombre="${cita.nombre_alumno}" data-fecha-hora="${cita.fecha}T${cita.hora}">Strike</button>`;
}

                            citasCanalizadas += `<tr class="text-center">
                <td>${cita.id_citas}</td>
                <td>${cita.periodo == 1 ? 'Enero-Abril' : cita.periodo == 2 ? 'Mayo-Agosto' : 'Septiembre-Diciembre'}</td>
                <td>${cita.fecha}</td>
                <td>${cita.hora.substring(0, 5)}</td>
                <td>${cita.matricula}</td>
                <td>${cita.nombre_alumno}</td>
                <td>${cita.nombre_tipo}</td>
                <td>${cita.tipo_problema}</td>
                <td class="${statusClass}">${statusText}</td>
                <td>
                    ${resueltoButton}
                    <button class="btn btn-danger btn-sm btn-personalizado-eliminar eliminar-btn" data-id="${cita.id_citas}" data-nombre="${cita.nombre_alumno}"><i class='bi bi-trash-fill'></i></button>
                    ${strikeButton}
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

            $('body').on('click', '.strike-btn', function() {
                const idCita = $(this).data('id');
                const nombreAlumno = $(this).data('nombre');

                $.ajax({
                    url: './php/actualizarStrikes.php',
                    method: 'POST',
                    data: {
                        id_cita: idCita
                    },
                    dataType: 'json',
                    success: function(response) {
                        let swalConfig = {};

                        if (response.status === 'success') {
                            swalConfig = {
                                title: response.message,
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false

                            };
                        } else if (response.status === 'error') {
                            swalConfig = {
                                title: response.message,
                                icon: 'error',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false

                            };
                        }

                        Swal.fire(swalConfig);
                        // Aquí puedes actualizar la tabla si es necesario
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            });



            // Manejar el evento de clic en el botón de canalizar
            $(document).on('click', '.canalizar-btn', function() {
                const citaId = $(this).data('id');
                const citaNombre = $(this).data('nombre');
                const citaMatricula = $(this).parent().siblings().eq(2).text();

                // Rellenar el formulario con la información de la cita
                $('#matricula').val(citaMatricula);

                // Cargar profesores basados en el tipo de cita seleccionado
                cargarProfesores($('#cita-tipo').val());
                cargarTiposProblema($('#problema').val());

                // Actualizar la lista de profesores y problemas cuando se cambie el tipo de cita
                $('#cita-tipo').on('change', function() {
                    cargarProfesores($(this).val());
                    cargarTiposProblema($(this).val());
                });
            });


            $('body').on('click', '.resuelto-btn', function() {
                var idCita = $(this).data('id');

                $.ajax({
                    url: './php/actualizarCitaResuelto.php',
                    method: 'POST',
                    data: {
                        id_citas: idCita,
                        status: 2 // actualizar el estado de la cita a "Resuelta"
                    },
                    success: function(response) {
                        if (response == 'success') {
                            cargarCitas();
                            Swal.fire({
                                title: '¡Gracias por ayudar al alumno!',
                                text: 'La cita ahora se marcara como resuelta, gracias por ayudar a mejorar a la comunidad estudiantil',
                                icon: 'success',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        } else {
                            console.log('Error al actualizar la cita');
                        }
                    },
                    error: function() {
                        console.log('Error al actualizar la cita');
                    }
                });
            });

            // Inicializar DataTables aquí, después de cargar los datos en la tabla
            $('#citasTable').DataTable({
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"table-responsive"t>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json"
                },
                "pagingType": "full_numbers",
                "pageLength": 2,
                "lengthMenu": [2, 4, 6, 8, 10],
                "order": [[2, "desc"]],
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
            setInterval(cargarCitas, 5000);

        });

        function cargarProfesoresNivel2() {
            $.ajax({
                url: './php/obtenerProfesoresNivel2.php', // Ruta al archivo obtenerProfesoresNivel2.php
                method: 'GET',
                dataType: 'json',
                success: function(profesoresNivel2) {
                    let tutorOptions = '<option value="">Selecciona un profesor</option>';
                    profesoresNivel2.forEach(profesor => {
                        tutorOptions += `<option value="${profesor.nempleado}">${profesor.nombre}</option>`;
                    });
                    $('#tutor').html(tutorOptions);
                },
                error: function() {
                    console.log('Error al cargar los profesores de nivel 2');
                }
            });
        }
    </script>
</body>

</html>