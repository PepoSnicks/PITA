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

$consulta_estado_evaluaciones = "SELECT estado FROM evaluaciones_activas WHERE id = 1";
$resultado_estado_evaluaciones = $conn->query($consulta_estado_evaluaciones);
$fila_estado_evaluaciones = $resultado_estado_evaluaciones->fetch_assoc();
$estado_evaluaciones = $fila_estado_evaluaciones['estado'];

// Establecer la clase del botón según el estado de 'evaluaciones_activas'
$boton_clase = $estado_evaluaciones == 1 ? "btn-secondary" : "btn-dark";
$boton_texto = $estado_evaluaciones == 1 ? "Desactivar evaluaciones" : "Activar evaluaciones";

// Obtener el periodo activo
$sql = "SELECT * FROM periodos WHERE activo = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $periodo_activo = $row['nombre_periodo'];
} else {
    $periodo_activo = "No hay periodo activo";
}


$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

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
    <title>APK</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <!-- Tema obscuro-->
    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <style>
        /* Estilos personalizados para los botones */
        .btn-custom {
            font-size: 24px;
            padding: 15px 40px;
            margin: 20px;
            height: 350px;
            width: 350px;
            font-weight: bold;
            font-size: 30px;
        }

        .btn-custom:hover {
            font-size: 24px;
            padding: 15px 50px;
            margin: 20px;
            height: 350px;
            width: 350px;
            font-weight: bold;
            font-size: 30px;
            background-color: #0096C7;
            border-color: #0096C7;
        }

        /* Estilos personalizados para los iconos */
        .btn-custom i {
            font-size: 60px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: 10px;
        }

        .btn-custom i:hover {
            font-size: 100px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: 10px;
        }

        .nav-link {
            color: #343a40;
        }

        .btn-personalizado:hover {
            background-color: #0096C7;
            border-color: #0096C7;
        }

        .abs-center {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 20vh;
        }

        /* Otros estilos CSS */


        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: black;
        }

        .carousel-img {
            width: 200px;
            height: 700px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="indexProfesor.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
                    <button id="mensajesBtn" class="btn btn-dark mr-2" onclick="window.location.href='indexProfesor.php'">Regresar</button>

                    <button id="dark-theme-toggle" class="btn btn-dark mr-2" data-toggle="tooltip" title="Tema obscuro">
                        <i class="bi bi-moon" id="dark-theme-icon"></i>
                    </button>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">

                        <li class="nav-item dropdown">
                            <a class="btn btn-dark dropdown-toggle mr-2 d-flex align-items-center" href="#" id="usuarioDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo $rutaImagen; ?>" alt="Imagen de perfil" class="rounded-circle mr-2 img-top" style="width: 30px; height: 30px;">
                                <?php echo $nombreUsuario; ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="usuarioDropdown">
                                <a class="dropdown-item" href="./perfil.php"><i class="bi bi-person"></i> Perfil</a>
                                <a class="dropdown-item" href="./citas.php"><i class="bi bi-calendar"></i> Citas</a>
                                <a class="dropdown-item" href="./juego.php"><i class="bi bi-joystick"></i> Juego</a>
                                <a class="dropdown-item" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <br>

    <div class="container">
        <div class="border bg-white p-3 rounded margin">


            <!-- Ícono de la aplicación -->
            <div class="container text-left mt-4">
            <img src="../imagenes/logo_apk.png" alt="Ícono de la aplicación" style="width: 100px; border-radius: 10%;">
            </div>

            <!-- Detalles de la aplicación -->
            <div class="container mt-4">
                <h4 class="text-dark-theme">PITA para Android</h4>
                <p class="text-dark-theme">Hemos desarrollado una aplicación web diseñada para facilitar a los usuarios el acceso rápido a sus citas, mantenerse informados al respecto, solicitarlas y gestionarlas de manera eficiente. Como alumno, podrás llevar un seguimiento detallado y visual de tus calificaciones, consultar tus materias en curso y verificar el estado de tus citas. Por otro lado, como profesor, tendrás la posibilidad de ver qué alumnos tienen citas programadas contigo, lo que te permitirá brindarles la mejor atención posible.</p>
                <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#appDetailsModal">
                    Ver detalles
                </button>

                <br>
                <br>


<!-- Modal con detalles de la aplicación -->
<div class="modal fade" id="appDetailsModal" tabindex="-1" role="dialog" aria-labelledby="appDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark-theme" id="appDetailsModalLabel">Detalles de la aplicación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-dark-theme">
                <p class="text-dark-theme">Versión: 1.0.0</p>
                <p class="text-dark-theme">Sistema operativo compatible: Android</p>
                <p class="text-dark-theme">Peso: 4.13MB</p>
                <p class="text-dark-theme">Desarrollador: Alexis Téllez Almazán</p>
                <h6 class="text-dark-theme">Características principales para alumnos:</h6>
                <ul class="text-dark-theme">
                    <li>Agendar citas</li>
                    <li>Visualizar citas asignadas</li>
                    <li>Revisar calificaciones</li>
                    <li>Estar al corriente en las materias</li>
                </ul>
                <h6 class="text-dark-theme">Características principales para profesores:</h6>
                <ul class="text-dark-theme">
                    <li>Canalizar citas (Si es tutor)</li>
                    <li>Resolver citas para alumnos con más de 30 tipos diferentes de solución</li>
                    <li>Revisar el horario laboral</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
            </div>

            <!-- Carrusel de imágenes -->
            <div class="owl-carousel owl-theme">
                <div class="item">
                    <img src="../imagenes/APK-Preview(1).jpg" alt="Imagen 1" class="carousel-img">
                </div>
                <div class="item">
                    <img src="../imagenes/APK-Preview(3).jpg" alt="Imagen 2" class="carousel-img">
                </div>
                <div class="item">
                    <img src="../imagenes/APK-Preview(4).jpg" alt="Imagen 3" class="carousel-img">
                </div>
                <div class="item">
                    <img src="../imagenes/APK-Preview(5).jpg" alt="Imagen 4" class="carousel-img">
                </div>
                <div class="item">
                    <img src="../imagenes/APK-Preview(6).jpg" alt="Imagen 5" class="carousel-img">
                </div>
                <div class="item">
                    <img src="../imagenes/APK-Preview(7).jpg" alt="Imagen 6" class="carousel-img">
                </div>
            </div>


            <!-- Botón de descarga -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col text-center">
                        <h4 class="text-dark-theme">Para descargar la APK</h4>
                        <br>
                        <a href="http://pitauptex.com/descargas/PITA.apk" download class="btn btn-dark btn-personalizado">Descargar APK</a>
                    </div>
                </div>
            </div>

            <!-- Enlace al análisis de VirusTotal -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col text-center">
                        <h4 class="text-dark-theme">Para verificar que la APK no contiene virus, haz clic en el siguiente enlace para ver el análisis de VirusTotal:</h4>
                        <br>
                        <a href="https://www.virustotal.com/gui/file/d095f3d845d4b59eaa595dc81f6c28e23e7e2ba03398a149a3b4f3309669b284?nocache=1" target="_blank" class="btn btn-dark btn-personalizado">Ver análisis de VirusTotal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script>
    function updateOpacity(event) {
        var isMobile = $(window).width() < 768;

        var allItems = event.target.querySelectorAll(".owl-item img");
        allItems.forEach(function (item) {
            item.style.opacity = "0.5";
        });

        var activeItems = event.target.querySelectorAll(".owl-item.active img");
        activeItems.forEach(function (item, index) {
            if (isMobile) {
                item.style.opacity = "1";
            } else {
                if (index === 1) {
                    item.style.opacity = "1";
                } else {
                    item.style.opacity = "0.5";
                }
            }
        });
    }

    $(document).ready(function () {
        $(".owl-carousel").owlCarousel({
            loop: true,
            margin: 20,
            autoWidth: false,
            nav: true,
            navText: [
                '<i class="bi bi-arrow-left" style="color: black; background-color: transparent; border: none; font-size: 20px;" aria-hidden="true"></i>',
                '<i class="bi bi-arrow-right" style="color: black; background-color: transparent; border: none; font-size: 20px;" aria-hidden="true"></i>'
            ],
            responsive: {
                0: {
                    items: 1,
                    center: true
                },
                768: {
                    items: 3,
                    center: true
                }
            },
            onInitialized: updateOpacity,
            onTranslated: updateOpacity
        });
    });
</script>

</html>