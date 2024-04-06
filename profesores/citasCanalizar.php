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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


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
        function isWeekend(date) {
            var day = new Date(date).getDay();
            return day === 5 || day === 6;
        }

        function cargarProfesores(id_nivel) {

            $.ajax({
                url: './php/obtenerProfesor.php', // Ruta al archivo cargarProfesores.php
                method: 'GET',
                data: {
                    id_nivel: id_nivel
                },
                dataType: 'json',
                success: function(profesores) {
                    let profesorOptions = '<option value="">Selecciona un profesor</option>';
                    profesores.forEach(profesor => {
                        profesorOptions += `<option value="${profesor.nempleado}">${profesor.nombre}</option>`;
                    });
                    $('#profesores').html(profesorOptions);
                },
                error: function() {
                    console.log('Error al cargar los profesores');
                }
            });
        }

        function cargarTiposProblema(id_nivel) {

            $.ajax({
                url: './php/obtenerTiposProblema.php', // Ruta al archivo obtenerTiposProblema.php
                method: 'GET',
                data: {
                    id_nivel: id_nivel
                },
                dataType: 'json',
                success: function(tiposProblema) {
                    let problemaOptions = '<option value="" disabled selected>Selecciona un problema</option>';
                    tiposProblema.forEach(tipo => {
                        problemaOptions += `<option value="${tipo.id_tipo_problema}">${tipo.tipo_problema}</option>`;
                    });
                    $('#problema').html(problemaOptions);
                },

                error: function(xhr, status, error) {
                    console.log('Error al cargar los tipo de problemas');

                }
            });
        }

        $(document).ready(function() {

            // Actualizar horas disponibles al cambiar el profesor o la fecha
            $('#profesores, #cita-fecha').on('change', function() {
                var nempleado = $('#profesores').val();
                var fecha = $('#cita-fecha').val();

                if (isWeekend(fecha)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No se pueden seleccionar fines de semana',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    $('#cita-fecha').val('');
                    return;
                }
                if (nempleado && fecha) {
                    var citaTipo = $('#cita-tipo').val();
$.getJSON(`./php/obtenerHorasProfesor.php?nempleado=${nempleado}&fecha=${fecha}&citaTipo=${citaTipo}`, function(horasDisponibles) {
                        var horasSelect = $('#horas');
                        horasSelect.empty();
                        horasSelect.append('<option value="" disabled selected>Selecciona una hora</option>');
                        horasDisponibles.forEach(function(hora) {
                            var optionText = hora.hora;
                            var optionValue = hora.hora;
                            var optionStyle = "";
                            var optionDisabled = false;

                            if (hora.estado === 'ocupado') {
                                optionText += " (Ocupado)";
                                optionStyle = "color:#ff7f7f;";
                                optionDisabled = true;
                            } else if (hora.estado === 'fuera_horario') {
                                optionStyle = "color:#808080;";
                                optionDisabled = true;
                            }

                            var optionClass = isWeekend(fecha) ? 'option-disabled' : '';

                            horasSelect.append(`<option value="${optionValue}" style="${optionStyle}" class="${optionClass}" ${optionDisabled ? "disabled" : ""}>${optionText}</option>`);
                        });
                    });

                } else {
                    $('#horas').empty();
                    $('#horas').append('<option value="" disabled selected>Selecciona una hora</option>');
                }
            });

        });

        $(document).ready(function() {

            const today = new Date().toISOString().substr(0, 10);
            $('#cita-fecha').attr('min', today);

            $('#cita-fecha').on('change', function() {
                if (isWeekend(this.value)) {
                    $('#error-fecha').show();
                    $(this).addClass('is-invalid');
                } else {
                    $('#error-fecha').hide();
                    $(this).removeClass('is-invalid');
                }
            });
            $('#agendarCitaForm').on('submit', function(e) {
                if ($('#cita-fecha').hasClass('is-invalid')) {
                    e.preventDefault();
                }
            });

        });
    </script>
    <title>Citas por canalizar</title>
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

        .btn-red {
            background-color: #ff7f7f;
        }

        .hidden {
            display: none;
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
    <!-- Canalizar Modal -->
    <div class="modal fade" id="canalizarModal" tabindex="-1" aria-labelledby="canalizarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="canalizarModalLabel" style="color: #000;">Citas por canalizar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-dark-theme">
                    <form id="canalizar-form">
                        <input type="hidden" id="cita-matricula">
                        <input type="hidden" id="cita-id" name="cita-id">
                        <div class="form-group">
                            <label for="cita-tipo" style="color: #000;">Tipo de cita:</label>
                            <select class="form-control" id="cita-tipo" required>
                                <option value="" disabled selected>Selecciona un tipo de cita</option>
                                <option value="3">Psicológica</option>
                                <option value="4">Académica</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="problema" style="color: #000;">Tipo de problema:</label>
                            <select class="form-control" id="problema" required>
                                <option value="" disabled selected>Selecciona un problema</option>
                                <!-- Aquí se cargarán los tipos de problema -->
                            </select>
                        </div>
                        <div class="form-group hidden" id="carrera-group">
                            <label for="carrera" style="color: #000;">Carrera:</label>
                            <select class="form-control" id="carrera" required>
                                <!-- Aquí se cargarán la carrera que tenga el alumno en la tabla alumnos en la columna de id_carrera y el nombre de la carrea esta en la tabla carreras donde tenga en comun el id_carrera y en la columna carreras aparece el nombre de la carrera-->
                            </select>
                        </div>
                        <div class="form-group hidden" id="materias-group">
                            <label for="materias" style="color: #000;">Materia:</label>
                            <select class="form-control" id="materias" required>
                                <option value="" disabled selected>Selecciona una materia</option>
                                <!-- Las materias del alumno se guardan en la tabla asignaturasa donde tenga en comun la columna matricula y despues consigue todas las materias que tenga dicha matricula, consigue las materias de la columa id_materias las cuales son refenrencia a la tabla de materias donde tienen en comun el id_materias y el nombre de la materias se guarda en la tabla materias en la columna materia-->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cita-profesor" style="color: #000;">Profesor:</label>
                            <select class="form-control" id="profesores" required>
                                <!-- Aquí se cargarán los profesores -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cita-fecha" style="color: #000;">Fecha:</label>
                            <input type="date" class="form-control" id="cita-fecha" name="cita-fecha" required>
                        </div>
                        <div class="form-group">
                            <label for="cita-hora" style="color: #000;">Hora:</label>
                            <select class="form-control" id="horas" name="cita-hora">
                                <option value="" disabled selected>Selecciona una hora</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-personalizado" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark btn-personalizado" id="confirmar-canalizar">Canalizar</button>
                </div>
            </div>
        </div>
    </div>

    <br>
    <div class="container">
        <div class="d-flex justify-content-center">
            <h1 class="text-center" style="color: #fff;"><i class="bi bi-calendar-plus-fill" style="color: #088C4F;"></i>  <b>Citas por canalizar</b></h1>
        </div>
        <div class="table-responsive">
            <br>
            <table id="citasTable" class="table table-striped table-light">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>ID de cita</th>
                        <th>Fecha</th>
                        <th>Matrícula</th>
                        <th>Nombre del alumno</th>
                        <th>Hora</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="citasProcesar">
                    <!-- Aquí se mostrarán las citas -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // Función para cargar las citas y actualizar la tabla
            function cargarCitas() {
                $.ajax({
                    url: './php/cargarCitaCanalizar.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(citas) {
                        let citasProcesar = '';
                        citas.forEach(cita => {
                            citasProcesar += `<tr class="text-center">
                        <td>${cita.id_citas}</td>
                        <td>${cita.fecha}</td>
                        <td>${cita.matricula}</td>
                        <td>${cita.nombre_alumno}</td>
                        <td>${cita.hora.substring(0, 5)}</td>
                <td>
                    <button class="btn btn-dark btn-personalizado canalizar-btn" data-toggle="modal" data-target="#canalizarModal" data-id="${cita.id_citas}" data-nombre="${cita.nombre_alumno}">Canalizar</button>
                    <button class="btn btn-danger btn-personalizado-eliminar eliminar-btn" data-toggle="modal" data-target="#eliminarModal" data-id="${cita.id_citas}" data-nombre="${cita.nombre_alumno}">Eliminar</button>
                </td>
            </tr>`;
                        });
                        $('#citasProcesar').html(citasProcesar);

                        // Actualizar DataTables en lugar de reinicializarlo
                        $('#citasTable').DataTable().clear().draw();
                        $('#citasTable').DataTable().rows.add($(citasProcesar)).draw();


                    },
                    error: function() {

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
                "pageLength": 2,
                "lengthMenu": [2, 4, 6, 8, 10],
                "order": [[1, "desc"]],
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }],
                "oLanguage": {
                    "sSearch": "Buscar:",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "No hay citas para canalizar", // Cambia este mensaje
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


            // Manejar el evento de clic en el botón de canalizar
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
                            url: `./php/eliminarCitaProcesar.php`,
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
        });

        function toggleCarreraMateria() {
            const citaTipo = $('#cita-tipo').val();
            if (citaTipo === '4') { // Académica
                $('#carrera-group').removeClass('hidden');
                $('#materias-group').removeClass('hidden');
            } else {
                $('#carrera-group').addClass('hidden');
                $('#materias-group').addClass('hidden');
            }
        }

        function cargarCarrera(matricula) {
            $.ajax({
                url: './php/obtenerCarreraAlumno.php',
                type: 'POST',
                data: {
                    matricula: matricula
                },
                dataType: 'json',
                success: function(response) {
                    $('#carrera').empty();
                    response.forEach(function(carrera) {
                        $('#carrera').append(`<option value="${carrera.id_carrera}">${carrera.carreras}</option>`);
                    });
                },
                error: function() {
                    alert('Error al cargar la carrera del alumno');
                }
            });
        }

        function cargarMaterias(matricula) {
            $.ajax({
                url: './php/obtenerMateriasAlumno.php',
                type: 'POST',
                data: {
                    matricula: matricula
                },
                dataType: 'json',
                success: function(response) {
                    $('#materias').empty().append('<option value="" disabled selected>Selecciona una materia</option>');
                    response.forEach(function(materia) {
                        $('#materias').append(`<option value="${materia.id_materias}">${materia.materia}</option>`);
                    });
                },
                error: function() {
                    alert('Error al cargar las materias del alumno');
                }
            });
        }




        $(document).ready(function() {
            // Manejar el evento de clic en el botón de canalizar
            $(document).on('click', '.canalizar-btn', function() {
                const citaId = $(this).data('id');
                console.log('citaId:', citaId);
                $('#cita-id').val(citaId);
                const citaNombre = $(this).data('nombre');
                const citaMatricula = $(this).parent().siblings().eq(2).text();

                // Rellenar el formulario con la información de la cita
                $('#cita-matricula').val(citaMatricula);

                // Cargar profesores basados en el tipo de cita seleccionado
                cargarProfesores($('#cita-tipo').val());
                cargarTiposProblema($('#problema').val());

                // Cargar carrera y materias
                cargarCarrera(citaMatricula);
                cargarMaterias(citaMatricula);


                // Actualizar la lista de profesores y problemas cuando se cambie el tipo de cita
                $('#cita-tipo').on('change', function() {
                    cargarProfesores($(this).val());
                    cargarTiposProblema($(this).val());
                    toggleCarreraMateria();
                });
            });
        });

        // Manejar el evento de clic en el botón de confirmar canalizar
        $('#confirmar-canalizar').on('click', function() {
            const id_citas = $('#cita-id').val();
            console.log('id_citas:', id_citas); // Agrega esta línea
            const nempleado = $('#profesores').val();
            const matricula = $('#cita-matricula').val();
            const fecha = $('#cita-fecha').val();
            const hora = $('#horas').val();
            const tipo = $('#cita-tipo').val();
            const problema = $('#problema').val();
            let carrera = null;
            let materia = null;

            if (tipo === '4') { // Académica
                carrera = $('#carrera').val();
                materia = $('#materias').val();
                console.log('carrera:', carrera);
                console.log('materia:', materia);

                // Comprueba si carrera y materia tienen un valor de 0 y, de ser así, establecerlos en -1
                if (carrera == 0) {
                    carrera = -1;
                }
                if (materia == 0) {
                    materia = -1;
                }

            } else {
                carrera = null;
                materia = null;
            }

            $.ajax({
                url: './php/canalizarCita.php',
                method: 'POST',
                data: {
                    id_citas: id_citas,
                    nempleado: nempleado,
                    matricula: matricula,
                    fecha: fecha,
                    hora: hora,
                    tipo: tipo,
                    problema: problema,
                    carrera: carrera, // Agrega esta línea
                    materia: materia // Agrega esta línea
                },
                success: function(response) {
                    console.log(response);
                    let parsedResponse = JSON.parse(response);

                    if (parsedResponse.status === 'success') {
                        Swal.fire({
                            title: 'Cita canalizada',
                            text: parsedResponse.message,
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then((result) => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: parsedResponse.message,
                            icon: 'error',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then((result) => {
                            location.reload();
                        });
                    }

                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        });

    </script>
</body>

</html>