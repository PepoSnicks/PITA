<?php
include('../php/conexion.php');

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$correo = $_SESSION['correo'];


if ($usuario == null || $usuario == '' || $nombreUsuario == '' || $usuario == 1 || $usuario == 2 || $usuario == 3 || $usuario == 4) {
    header("location:../index.php");
    die();
}

// Get profile image URL
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT imagen_de_perfil FROM alumnos WHERE matricula = '" . $_SESSION['user'] . "'";
$result = $conn->query($sql);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$rutaImagen = $baseUrl . "/imagenes/default.jpg";

if ($result->num_rows > 0) {
    $fila = $result->fetch_assoc();
    if (!empty($fila['imagen_de_perfil'])) {
        $rutaImagen = $baseUrl . "/" . $fila['imagen_de_perfil'];
    }
}

// Obtener strikes
$sql = "SELECT strikes FROM alumnos WHERE matricula = '" . $_SESSION['user'] . "'";
$result = $conn->query($sql);
$strikes = 0;

if ($result->num_rows > 0) {
    $fila = $result->fetch_assoc();
    $strikes = $fila['strikes'];
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Estilos de DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" />

    <!-- jQuery y DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

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


    <title>Citas</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

        <!-- Tema obscuro-->
        <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>
    <script defer>
    function isWeekend(date) {
        var day = new Date(date).getDay();
        return day === 5 || day === 6;
    }
    // Obtener profesores con id_nivel 2
    $.getJSON('./php/obtenerProfesor.php', function(profesores) {
        var profesoresInput = $('#profesores');
        var profesorNombreSpan = $('#profesor_nombre');
        profesores.forEach(function(profesor) {
            profesoresInput.val(profesor.nempleado);
            profesorNombreSpan.text(profesor.nombre);
        });
    });
        $(document).ready(function() {

            // Actualizar horas disponibles al cambiar el profesor o la fecha
            $('#profesores, #fecha').on('change', function() {
                var nempleado = $('#profesores').val();
                var fecha = $('#fecha').val();

                if (isWeekend(fecha)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No se pueden seleccionar fines de semana',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                    $('#fecha').val('');
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

                            // Agregar una clase CSS diferente a las opciones de fecha que no son sábados ni domingos
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
            $('#fecha').attr('min', today);

            $('#fecha').on('change', function() {
                if (isWeekend(this.value)) {
                    $('#error-fecha').show();
                    $(this).addClass('is-invalid');
                } else {
                    $('#error-fecha').hide();
                    $(this).removeClass('is-invalid');
                }
            });
            $('#agendarCitaForm').on('submit', function(e) {
                if ($('#fecha').hasClass('is-invalid')) {
                    e.preventDefault();
                }
            });

        });
    </script>
</head>
<style>
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
  background-color: #8B1D35 !important; /* Cambiado a color vino */
  border-color: transparent !important;
  color: #FFFFFF !important; /* Establecer el color del texto (iconos) en blanco */
}

.btn:hover {
  color: #FFFFFF !important; /* Mantener el color del texto (iconos) en blanco al pasar el cursor */
}

.thead-vino th {
      background-color: #8B1D35; /* Color vino */
      color: #fff; /* Texto en color blanco para resaltar */
    }
    .icono-verde {
    color: #088C4F !important;
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

.hover-effect:hover {
    transform: rotate(360deg) scale(1.1); /* Rota el botón 10 grados y aumenta su escala al 110% */
    transition: transform 0.7s ease; /* Añade una transición suave */
}
.hover-effect1:hover {
    transform: scale(1.1); /* Hace que el elemento se escale al 110% del tamaño original */
    transition: transform 0.3s ease; /* Agrega una transición suave */
}
</style>

<body>
<header>
<nav class="navbar navbar-expand-lg navbar-vino bg-maroon">
            <div class="container">
                <a class="navbar-brand hover-effect1" href="indexAlumnos.php"><img src="./css/logo.png" alt="Logo"></a>
                    <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexAlumnos.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                    <button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                        <i class="bi bi-moon" id="dark-theme-icon"></i>
                    </button>
                    <button id="mensajesBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='indexAlumnos.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./materias.php"><i class="bi bi-book"></i> Materias</a>
                                <a class="dropdown-item hover-effect1" href="./calificaciones.php"><i class="bi bi-pencil"></i> Calificaciones</a>
                                <a class="dropdown-item hover-effect1" href="./juego.php"><i class="bi bi-joystick"></i> Juego</a>
                                <a class="dropdown-item hover-effect1" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container my-3">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-md-5">
            <div class="d-flex justify-content-center">
                <h3 class="texto-blanco"><i class="bi bi-calendar-plus-fill icono-verde"></i> <b>Agendar una cita</b></h3>
            </div>

                <form id="agendarCitaForm" method="post" action="./php/agendarCita.php">
                    <ul class="list-unstyled my-2">
                    <li>
    <p class="font-weight-bold mb-1 texto-blanco">Tutor:</p>
    <input type="hidden" id="profesores" name="nempleado" />
    <span id="profesor_nombre" class="texto-blanco"></span>
</li>

                        <br>
                        <li>
    <p class="font-weight-bold mb-1 texto-blanco">Fecha:</p>
    <input type="date" id="fecha" name="fecha" class="texto-blanco" />
</li>

                        <br>
                        <li>
    <p class="font-weight-bold mb-1 texto-blanco">Hora:</p>
    <select id="horas" name="hora" class="texto-blanco">
        <option value="" disabled selected class="texto-blanco">Selecciona una hora</option>
    </select>
</li>

                    </ul>
                    <br>
                    <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-dark btn-personalizado btn-vino">Agendar cita</button>
                    </div>
                </form>
            </div>

            <div class="col-12 col-md-7 mt-3 mt-md-0">
            <div class="d-flex justify-content-center">
                <h3 class="texto-blanco"><i class="bi bi-calendar-plus-fill icono-verde"></i> <b>Citas con tutor</b></h3>
            </div>

                <div class="table-responsive">
                    <table id="citasPendientesTable" class="table table-striped table-bordered table-light">
                        <thead class="thead-vino">
                            <tr class="text-center">
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Tutor</th>
                            </tr>
                        </thead>
                        <?php
                        // Consulta SQL para obtener las citas del alumno
                        $query = "SELECT citas_procesar.fecha, citas_procesar.matricula, profesores.nombre as nombre_profesor, citas_procesar.hora 
                    FROM citas_procesar 
                    JOIN profesores ON citas_procesar.tutor = profesores.nempleado
                    WHERE citas_procesar.matricula = '$usuario'";
                        $resultado = mysqli_query($conn, $query);
                        // Iterar sobre los resultados y agregar filas a la tabla HTML
                        while ($row = mysqli_fetch_assoc($resultado)) {
                            echo "<tr>";
                            echo "<td class='text-center'>" . $row['fecha'] . "</td>";
                            echo "<td class='text-center'>" . substr($row['hora'], 0, 5) . "</td>";
                            echo "<td class='text-center'>" . $row['nombre_profesor'] . "</td>";
                            echo "</tr>";
                        }

                        // Liberar resultado y cerrar la conexión
                        mysqli_free_result($resultado);
                        ?>
                        <tbody>
                            <!-- Aquí se mostrarán las citas del alumno -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-8 offset-md-2">
            <div class="d-flex justify-content-center">
    <h3 class="texto-blanco"><i class="bi bi-calendar-plus-fill icono-verde"></i> <b class="texto-blanco">Citas aprobadas</b></h3>
</div>

                <div class="table-responsive">
                    <table id="citasAprobadasTable" class="table table-striped table-bordered table-light">
                        <thead class="thead-vino">
                            <tr class="text-center">
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Profesor</th>
                                <th>Tipo de cita</th>
                                <th>Tipo de problema</th>
                                <th>Estatus</th>
                                <th>Tutor</th>
                            </tr>
                        </thead>
                        <?php
                        // Consulta SQL para obtener todos los campos de la tabla citas del alumno
                        $query = "SELECT citas.id_citas, citas.fecha, citas.matricula, citas.nempleado, tipo_problema.tipo_problema, citas.status, citas.hora, p1.nombre as nombre_profesor, nivelcitas.nombre as nivel, p2.nombre as nombre_profesor_tutor 
                        FROM citas 
                        JOIN profesores p1 ON citas.nempleado = p1.nempleado
                        JOIN profesores p2 ON citas.tutor = p2.nempleado
                        JOIN tipo_problema ON citas.tipo = tipo_problema.id_tipo_problema
                        JOIN nivelcitas ON citas.id_citasN = nivelcitas.id_citasN
                        WHERE citas.matricula = '$usuario'";

                        $resultado = mysqli_query($conn, $query);
                        // Iterar sobre los resultados y agregar filas a la tabla HTML
                        while ($row = mysqli_fetch_assoc($resultado)) {
                            echo "<tr>";
                            echo "<td>" . $row['fecha'] . "</td>";
                            echo "<td>" . substr($row['hora'], 0, 5) . "</td>";
                            echo "<td>" . $row['nombre_profesor'] . "</td>";
                            echo "<td class='text-center'>" . $row['nivel'] . "</td>";
                            echo "<td class='text-center'>" . $row['tipo_problema'] . "</td>";

                            if ($row['status'] == 1) {
                                echo "<td class='text-center' style='background-color: #C5E5A4'>Activa</td>";
                            } else if ($row['status'] == 0) {
                                echo "<td class='text-center' style='background-color: #ff7f7f'>Inactiva</td>";
                            } else if ($row['status'] == 2) {
                                echo "<td class='text-center' style='background-color: #FFEAAE'>Resuelta</td>";
                            } else if ($row['status'] == 3) {
                                echo "<td class='text-center' style='background-color: #D3D3D3'>No asistío</td>";
                            }
                            echo "<td>" . $row['nombre_profesor_tutor'] . "</td>";
                            echo "</tr>";
                        }
                        // Liberar resultado y cerrar la conexión
                        mysqli_free_result($resultado);
                        mysqli_close($conn);
                        ?>
                        <tbody>
                            <!-- Aquí se mostrarán todos los campos de la tabla citas del alumno -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para bloquear al alumno si tiene más de tres strikes -->
    <div class="modal" tabindex="-1" id="strikesModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title text-dark-theme" hidden>Alerta de Strikes</h5>
                </div>
                <div class="modal-body text-center text-dark-theme">
                    <h1><i style="color: #000;" class="bi bi-exclamation-triangle-fill me-2"></i></h1>
                            <h2 style="color: #000;" class="modal-title">¡Atención!</h2>
                            <br>
                            <h5 style="color: #000;">Se han deshabilitado las citas porque tienes 3 strikes. Por favor, comunícate con el jefe de carrera para resolver el problema</h5>
                            <br>
                    </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-dark btn-personalizado" onclick="location.href='./indexAlumnos.php'">Regresar al menú</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const strikes = <?php echo $strikes; ?>;

            if (strikes >= 3) {
                $('#strikesModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#strikesModal').modal('show');
            }
        });

        $(document).ready(function() {
            $('#citasPendientesTable').DataTable({
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"table-responsive"t>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json"
                },
                "pagingType": "full_numbers",
                "pageLength": 1,
                "lengthMenu": [1, 5, 10, 15, 25, 50, 100],
                "order": [],
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }],
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

            $('#citasAprobadasTable').DataTable({
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"table-responsive"t>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-MX.json"
                },
                "pagingType": "full_numbers",
                "pageLength": 2,
                "lengthMenu": [2, 5, 10, 15, 25, 50, 100],
                "order": [],
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                }],
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
        });


        $('#agendarCitaForm').submit(function(event) {
            event.preventDefault();

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                        title: response.titulo,
                        text: response.texto,
                        icon: response.icono,
                        timer: response.timer,
                        showConfirmButton: response.showConfirmButton,
                        timerProgressBar: response.timerProgressBar
                    }).then((result) => {
                        location.reload();
                    });

                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al crear la cita',
                        icon: 'error',
                        timer: 2500,
                        showConfirmButton: false,
                        timerProgressBar: true
                    });
                }
            });
        });
    </script>
</body>

</html>