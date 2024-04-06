<?php

include("../php/conexion.php");

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$idnivel = $_SESSION['idnivel'];



if ($usuario == null || $usuario == '' || $idnivel != 2 || $nombreUsuario == '') {
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
        // Manejar el evento de clic en el botón de editar cita
        $(document).on('click', '.editar-cita', function() {
            const idCita = $(this).data('id');
            const fecha = $(this).data('fecha');
            const hora = $(this).data('hora');
            const tipo = $(this).data('tipo');
            const tutor = $(this).data('tutor');
            const problema = $(this).data('problema');
            cargarProfesoresNivel2();


            // Aquí puedes precargar los campos del formulario con los datos de la cita
            $('#cita-fecha').val(fecha);
            $('#cita-hora').val(hora);
            $('#cita-tipo').val(tipo);
            $('#problema').val(problema);
            $('#tutor').val(tutor);

            // Cargar los datos adicionales de la cita
            cargarDatosCita(idCita);
            cargarProfesoresNivel2();

            $('#editarCitaModal').modal('show');

            // Manejar el evento de cambio de tipo de cita
            $('#cita-tipo').on('change', function() {
                const id_nivel = $(this).val();
                cargarProfesores(id_nivel);
                cargarTiposProblema(id_nivel); // Agregado
            });

        });


        // Manejar el evento de clic en el botón de eliminar cita
        $(document).on('click', '.eliminar-cita', function() {
            const idCita = $(this).data('id');

            // Aquí puedes agregar el código para eliminar la cita
        });

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

        function cargarDatosCita(id_cita) {
            console.log('id_cita:', id_cita); // Agregado
            $.ajax({
                url: './php/obtenerCita.php', // Ruta al archivo obtenerDatosCita.php
                method: 'GET',
                data: {
                    id_cita: id_cita
                },
                dataType: 'json',
                success: function(datosCita) {
                    $('#cita-id').val(datosCita.id_citas);
                    $('#fecha').val(datosCita.fecha); // Modificado
                    $('#matricula').val(datosCita.matricula);
                    $('#profesores').val(datosCita.nempleado);
                    $('#tipo').val(datosCita.tipo);
                    $('#status').val(datosCita.status);
                    $('#hora').val(datosCita.hora); // Modificado
                    $('#cita-tipo').val(datosCita.tipo_cita); // Modificado
                    $('#profesores').val(datosCita.nempleado); // Modificado
                },

                error: function(xhr, status, error) {
                    console.log('Error al cargar los datos de la cita');
                }
            });
        }

        $(document).on('click', '.btn-editar', function() {
            const id_cita = $(this).data('id');
            const fecha = $(this).data('fecha');
            const hora = $(this).data('hora');
            const tipo = $(this).data('tipo');
            const problema = $(this).data('problema');

            // rellenar los campos del modal
            $('#cita-id').val(id_cita);
            $('#fecha').val(fecha);
            $('#hora').val(hora);
            $('#tipo').val(tipo);
            $('#problema').val(problema);

            // cargar datos adicionales de la cita
            cargarDatosCita(id_cita);
        });

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
                    $.getJSON(`./php/obtenerHorasProfesor.php?nempleado=${nempleado}&fecha=${fecha}`, function(horasDisponibles) {
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

        // Agrega este código dentro del bloque <script defer>
        $(document).on('click', '#confirmar-editar', function() {
            const id_citas = $('#cita-id').val();
            const tipo_cita = $('#cita-tipo').val();
            const problema = $('#problema').val();
            const nempleado = $('#profesores').val();
            const fecha = $('#cita-fecha').val();
            const hora = $('#horas').val();
            const status = $('#status').val();
            const tutor = $('#tutor').val();

            const updateTutor = tutor !== '';

            // Validar que todos los campos estén llenos
            if (tipo_cita && problema && nempleado && fecha && hora && status && tutor) {
                $.ajax({
                    url: './php/actualizarCita.php', // Ruta al archivo actualizarCita.php
                    method: 'POST',
                    data: {
                        id_citas: id_citas,
                        tipo_cita: tipo_cita,
                        problema: problema,
                        nempleado: nempleado,
                        fecha: fecha,
                        hora: hora,
                        status: status,
                        tutor: tutor,
                        updateTutor: updateTutor

                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Cita actualizada',
                            text: 'La cita ha sido actualizada exitosamente',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then((result) => {
                            // Aquí puedes agregar código para manejar la respuesta del servidor después de que el temporizador termine
                        });
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un error al actualizar la cita',
                            icon: 'error',
                            timer: 1000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: 'Completa todos los campo',
                    icon: 'error',
                    timer: 1000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            }
        });
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

        .dataTables_wrapper .pagination .page-item .page-link {
            background-color: #343a40;
            border-color: #343a40;
            color: #fff;

        }

        .btn-resuelto {
            background-color: #94E88E;
            border-color: #94E88E;
            color: #000;
        }

        .btn-warning {
            background-color: #C5E5A4;
            border-color: #C5E5A4;
            color: #000;
        }

        .btn-resuelto:hover {
            background-color: #00BF13;
            border-color: #00BF13;
            color: #000;
        }

        .custom-bg-resuelto {
            background-color: #FFEFBF;
            color: #000;
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

        .btn-red {
            background-color: #ff7f7f;
        }

        .btn-primary {
            background-color: #ff7f7f;
            border-color: #ff7f7f;
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
                <a class="navbar-brand hover-effect1" href="indexTutor.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
            <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexTutor.php">
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
                                <a class="dropdown-item hover-effect1" href="./horarioProfesor.php"><i class="bi bi-clock"></i> Horario</a>
                                <a class="dropdown-item hover-effect1" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
                                <a class="dropdown-item hover-effect1" href="./reportes.php"><i class="bi bi-file-earmark-text"></i> Reportes</a>
                                <a class="dropdown-item hover-effect1" href="./medallas.php"><i class="bi bi-award"></i> Medallas</a>
                                <a class="dropdown-item hover-effect1" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="modal fade" id="editarCitaModal" tabindex="-1" aria-labelledby="editarCitaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="editarCitaModalLabel">Editar cita</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-dark-theme">
                    <form id="editar-cita-form">
                        <div class="form-group">
                            <label for="cita-tipo">Tipo de cita:</label>
                            <select class="form-control" id="cita-tipo" required>
                                <option value="" disabled selected>Selecciona un tipo de cita</option>
                                <option value="2">Psicológica</option>
                                <option value="4">Docencia</option>
                            </select>
                        </div>
                        <input type="hidden" id="cita-id" value="">
                        <div class="form-group">
                            <label for="problema">Tipo de problema:</label>
                            <select class="form-control" id="problema" required>
                                <option value="" disabled selected>Selecciona un tipo de problema</option>
                                <!-- Aquí se cargarán los tipos de problema -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cita-profesor">Profesor:</label>
                            <select class="form-control" id="profesores" required>
                                <option value="" disabled selected>Seleccione un profesor</option>
                                <!-- Aquí se cargarán los profesores -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="cita-fecha">Fecha:</label>
                            <input type="date" class="form-control" id="cita-fecha" name="cita-fecha" required>
                        </div>
                        <div class="form-group">
                            <label for="cita-hora">Hora:</label>
                            <select class="form-control" id="horas" name="cita-hora">
                                <option value="" disabled selected>Selecciona una hora</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select class="form-control" id="status" required>
                                <option value="" disabled selected>Selecciona la status</option>
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                                <option value="2">Resuelta</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tutor">Traspasar a otro tutor:</label>
                            <select class="form-control" id="tutor">
                                <option value="">No</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-personalizado" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-dark btn-personalizado" id="confirmar-editar">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <br>
        <br>
        <h1 class="text-center" style="color: #fff;"><i class="bi bi-calendar-check-fill" style="color: #088C4F;"></i> <b>Citas Canalizadas</b></h1>
        </br>
        </br>
        <div class="table-responsive">
            <br>
            <table id="citasTable" class="table table-striped table-light">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>ID de cita</th>
                        <th>Periodo</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Matrícula</th>
                        <th>Nombre del alumno</th>
                        <th>Profesor asignado</th>
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
                    url: './php/cargarCitasCanalizadas.php',
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
                <button class="btn btn-dark btn-personalizado btn-sm editar-cita" data-id="${cita.id_citas}" data-fecha="${cita.fecha}" data-hora="${cita.hora}" data-tipo="${cita.tipo_cita}" data-problema="${cita.tipo}">Editar</button>
                <button class="btn btn-${cita.status == 1 ? 'primary' : 'warning'} btn-personalizado btn-sm inhabilitar-cita" data-id="${cita.id_citas}" data-status="${cita.status}">${cita.status == 1 ? 'Inhabilitar' : 'Habilitar'}</button>
                <button class="btn btn-danger btn-sm btn-personalizado-eliminar eliminar-btn" data-id="${cita.id_citas}" data-nombre="${cita.nombre_alumno}">Eliminar</button>
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

            // Agregar event listener para los botones "Inhabilitar"
            $(document).on('click', '.inhabilitar-cita', function() {
                const idCita = $(this).data('id');
                const button = this; // Referencia al botón que se hizo clic
                inhabilitarCita(idCita, button);
            });


            function inhabilitarCita(idCita, button) {
                $.ajax({
                    url: './php/inhabilitarCita.php',
                    method: 'POST',
                    data: {
                        id_cita: idCita,
                        status: button.getAttribute("data-status") // Enviar el estado actual
                    },
                    success: function(response) {
                        const jsonResponse = JSON.parse(response);

                        if (jsonResponse.status === 'success') {
                            const newStatus = button.getAttribute("data-status") === "1" ? "0" : "1";
                            button.setAttribute("data-status", newStatus);

                            // Cambiar el texto y la clase del botón según el estado
                            if (newStatus === "1") {
                                button.textContent = "Inhabilitar";
                                button.classList.remove("btn-warning");
                                button.classList.add("btn-dark");
                            } else {
                                button.textContent = "Habilitar";
                                button.classList.remove("btn-dark");
                                button.classList.add("btn-warning");
                            }

                            Swal.fire({
                                icon: 'success',
                                title: newStatus === "1" ? 'Cita habilitada' : 'Cita inhabilitada',
                                text: jsonResponse.message,
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        } else {
                            alert(jsonResponse.message);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Hubo un error al cambiar el estado de la cita');
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