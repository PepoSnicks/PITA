<?php

include("../php/conexion.php");

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$idnivel = $_SESSION['idnivel'];

if ($usuario == null || $usuario == '' || $idnivel != 3 || $nombreUsuario == '') {
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

$por_pagina = 5; // Aquí puedes cambiar el número de registros por página

function paginar($conn, $consulta, $por_pagina = 10)
{
    $resultado = $conn->query($consulta);
    $total_registros = $resultado->num_rows;

    $paginas = ceil($total_registros / $por_pagina);
    $pagina_actual = (isset($_GET['pagina'])) ? (int)$_GET['pagina'] : 1;
    $inicio = ($pagina_actual - 1) * $por_pagina;

    $consulta .= " LIMIT $inicio, $por_pagina";
    $resultado = $conn->query($consulta);

    return array($resultado, $paginas, $pagina_actual);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <title>Horario</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">
    <!-- Modo obscuro -->
    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="../js/darktheme.js" defer></script>

    <!-- Revision de las citas -->
    <script src="../js/checkCitas.js"></script>
    
    <!-- Revision de los mensajes -->
    <script src="../js/checkMessage.js"></script>

    <style>
        .btn-red {
            background-color: #ff7f7f;
        }

        .btn-opciones:hover {
            background-color: #0096C7;
            border-color: #0096C7;
            cursor: pointer;
        }

        .pagination .page-link {
            background-color: #343A40;
            color: white;
        }

        .pagination .page-item.active .page-link {
            background-color: #0096C7;
        }

        .ocupado {
            background-color: #C5E5A4;
        }

        .no-ocupado {
            background-color: #fff;
        }

        .color-box {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            border: 1px solid black;
        }
        .current-time {
    background-color: #FEEAA2;
}

.current-time-overlay {
    background-color: rgba(254, 234, 162, 0.7);
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
}
.table td {
    position: relative;
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

.table-dark {
  color: #fff;
  background-color: #212529;
}

.dataTables_wrapper .pagination .page-item .page-link {
    background-color: #343a40;
    border-color: #343a40;
    color: #fff;
}

    .thead-vino th {
      background-color: #8B1D35; /* Color vino */
      color: #fff; /* Texto en color blanco para resaltar */
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

    </style>
</head>

<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-vino bg-maroon">
            <div class="container">
                <a class="navbar-brand hover-effect1" href="indexPsicologo.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
            <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexPsicologo.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                <button id="mensajesBtn" class="btn btn-vino mr-2 hover-effect" onclick="window.location.href='./mensajes.php'">
                    <i class="bi bi-envelope-fill" data-toggle="tooltip" title="Mensajes"></i>
                </button>
                <button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                </button>
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./indexPsicologo.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./citasMenu.php"><i class="bi bi-calendar"></i> Citas</a>
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
    <div class="col-12 text-center">
    <br>
        <br>
        <h1 class="text-center" style="color: #fff;"><i class="bi bi-clock" style="color: #088C4F;"></i> <b>Horario del profesor <?php echo $nombreUsuario ?></b></h1>
        </br>
        </br>
    </div>
    <br>
    <div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="container">
                <div class="col-12 text-center">
                    <div class="container text-center">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-light">
                                        <thead class="thead-vino">
                                            <tr class="text-center">
                                                    <th>Hora</th>
                                                    <th>Lunes</th>
                                                    <th>Martes</th>
                                                    <th>Miércoles</th>
                                                    <th>Jueves</th>
                                                    <th>Viernes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">07:00 - 08:00</th>
                                                    <td id="dia1_07"></td>
                                                    <td id="dia2_07"></td>
                                                    <td id="dia3_07"></td>
                                                    <td id="dia4_07"></td>
                                                    <td id="dia5_07"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">08:00 - 09:00</th>
                                                    <td id="dia1_08"></td>
                                                    <td id="dia2_08"></td>
                                                    <td id="dia3_08"></td>
                                                    <td id="dia4_08"></td>
                                                    <td id="dia5_08"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">09:00 - 10:00</th>
                                                    <td id="dia1_09"></td>
                                                    <td id="dia2_09"></td>
                                                    <td id="dia3_09"></td>
                                                    <td id="dia4_09"></td>
                                                    <td id="dia5_09"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">10:00 - 11:00</th>
                                                    <td id="dia1_10"></td>
                                                    <td id="dia2_10"></td>
                                                    <td id="dia3_10"></td>
                                                    <td id="dia4_10"></td>
                                                    <td id="dia5_10"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">11:00 - 12:00</th>
                                                    <td id="dia1_11"></td>
                                                    <td id="dia2_11"></td>
                                                    <td id="dia3_11"></td>
                                                    <td id="dia4_11"></td>
                                                    <td id="dia5_11"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">12:00 - 13:00</th>
                                                    <td id="dia1_12"></td>
                                                    <td id="dia2_12"></td>
                                                    <td id="dia3_12"></td>
                                                    <td id="dia4_12"></td>
                                                    <td id="dia5_12"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">13:00 - 14:00</th>
                                                    <td id="dia1_13"></td>
                                                    <td id="dia2_13"></td>
                                                    <td id="dia3_13"></td>
                                                    <td id="dia4_13"></td>
                                                    <td id="dia5_13"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">14:00 - 15:00</th>
                                                    <td id="dia1_14"></td>
                                                    <td id="dia2_14"></td>
                                                    <td id="dia3_14"></td>
                                                    <td id="dia4_14"></td>
                                                    <td id="dia5_14"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">15:00 - 16:00</th>
                                                    <td id="dia1_15"></td>
                                                    <td id="dia2_15"></td>
                                                    <td id="dia3_15"></td>
                                                    <td id="dia4_15"></td>
                                                    <td id="dia5_15"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">16:00 - 17:00</th>
                                                    <td id="dia1_16"></td>
                                                    <td id="dia2_16"></td>
                                                    <td id="dia3_16"></td>
                                                    <td id="dia4_16"></td>
                                                    <td id="dia5_16"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">17:00 - 18:00</th>
                                                    <td id="dia1_17"></td>
                                                    <td id="dia2_17"></td>
                                                    <td id="dia3_17"></td>
                                                    <td id="dia4_17"></td>
                                                    <td id="dia5_17"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">18:00 - 19:00</th>
                                                    <td id="dia1_18"></td>
                                                    <td id="dia2_18"></td>
                                                    <td id="dia3_18"></td>
                                                    <td id="dia4_18"></td>
                                                    <td id="dia5_18"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">19:00 - 20:00</th>
                                                    <td id="dia1_19"></td>
                                                    <td id="dia2_19"></td>
                                                    <td id="dia3_19"></td>
                                                    <td id="dia4_19"></td>
                                                    <td id="dia5_19"></td>
                                                </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4><strong>Leyenda</strong></h4>
                        <div class="d-inline-flex">
                            <div class="color-box" style="background-color: #98BBF5;"></div> <span> Tutorías</span>
                        </div>
                        <div class="d-inline-flex">
                            <div class="color-box" style="background-color: #F7CAC9;"></div> <span> Psicológicas</span>
                        </div>
                        <div class="d-inline-flex">
                            <div class="color-box" style="background-color: #B2DFDB;"></div> <span> Académicas</span>
                        </div>
                        <div class="d-inline-flex">
                            <div class="color-box" style="background-color: #FEEAA2;"></div> <span> Hora actual</span>
                        </div>
                    </div>
                </div>
            </div>

            <script>

$(document).ready(function () {
    var currentDateTime = new Date();
    var currentDay = currentDateTime.getDay();
    var currentHour = currentDateTime.getHours();

    if (currentDay >= 1 && currentDay <= 5) {
        var dayColumn = "dia" + currentDay;
        var hourRow = (currentHour < 10) ? "0" + currentHour : currentHour;

        var currentTimeOverlay = $('<div class="current-time-overlay"></div>');
        $("#" + dayColumn + "_" + hourRow).append(currentTimeOverlay);
    }
});
var usuario = '<?php echo $_SESSION['usuario']; ?>';

var profesor = {
    nempleado: usuario
};
cargarHorarioProfesor(profesor);

function cargarHorarioProfesor(profesor) {
    console.log("Cargando horario para: ", profesor.nempleado); // Agrega esta línea

    $.ajax({
        url: './php/obtenerHorarioProfesor.php',
        type: 'POST',
        data: {
            nempleado: parseInt(profesor.nempleado) // Aquí se convierte a número entero
        },
        dataType: 'json',
        success: function(response) {
            console.log(response);
            if (response) {
                llenarTablaHorario(response);
            }
        },
        error: function() {
            alert('Error al cargar el horario del profesor');
        }
    });
}



                function llenarTablaHorario(horario) {
                    horario.forEach(function(item) {
                        var dia_semana = item['dia_semana'];
                        var hora_inicio = parseInt(item['hora_inicio'].substring(0, 2));
                        var hora_fin = parseInt(item['hora_fin'].substring(0, 2));
                        var tipo_hora = parseInt(item['tipo_hora']);

                        var color = getColorFromTipoHora(tipo_hora);

                        // Itera sobre todas las horas desde la hora de inicio hasta la hora de finalización
                        for (var hora_actual = hora_inicio; hora_actual < hora_fin; hora_actual++) {
                            var id_celda = 'dia' + dia_semana + '_' + hora_actual.toString().padStart(2, '0');
                            $('#' + id_celda).css('background-color', color);
                        }
                    });
                }

                function getColorFromTipoHora(tipo_hora) {
                    switch (tipo_hora) {
                        case 1: // Tutorías
                            return '#98BBF5'; // Amarillo
                        case 2: // Psicológicas
                            return '#F7CAC9'; // Azul claro
                        case 4: // Académicas
                            return '#B2DFDB'; // Verde
                        default:
                            return ''; // Sin color
                    }
                }

            </script>

</body>

<?php
$errorUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

function showAlert($icon, $text, $title)
{
    $timer = 3000;

    echo "<script>
            $(document).ready(function() {
                Swal.fire({
                    icon: '{$icon}',
                    text: '{$text}',
                    title: '{$title}',
                    timer: {$timer},
                    showConfirmButton: false,
                    allowOutsideClick: true
                }).then(() => {
                    window.history.back();
                });
            });
        </script>";
}

$horarioProfesorUrl = "/ruta/a/horarioProfesor.php"; // Asegúrate de cambiar esto a la ruta correcta en tu aplicación

if (strpos($errorUrl, "horario=approved") == true) {
    showAlert('success', 'El horario ha sido guardado con exito', 'Horario guardado', "{$horarioProfesorUrl}?horario=approved");
}
if (strpos($errorUrl, "horario=error") == true) {
    showAlert('error', 'Por favor intenta guardar el horario más tarde', 'Error al guardar el horario', "{$horarioProfesorUrl}?horario=error");
}
if (strpos($errorUrl, "horario=invalid") == true) {
    showAlert('error', 'Verifica que la hora fin sea mayor a la hora de inicio', 'Horas erroneas', "{$horarioProfesorUrl}?horario=invalid");
}

?>

</html>