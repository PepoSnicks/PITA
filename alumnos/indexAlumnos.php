<?php
include('../php/conexion.php');

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$correo = $_SESSION['correo'];

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($usuario == null || $usuario == '' || $nombreUsuario == '' || $usuario == 1 || $usuario == 2 || $usuario == 3 || $usuario == 4 | $usuario == 5) {
    header("location:../index.php");
    die();
}

// Get profile image URL
$conn = new mysqli($servername, $username, $password, $dbname);
$sql = "SELECT imagen_de_perfil FROM alumnos WHERE matricula = '" . $_SESSION['user'] . "'";
$result = $conn->query($sql);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$rutaImagen = $baseUrl . "./imagenes/default.jpg";

if ($result->num_rows > 0) {
    $fila = $result->fetch_assoc();
    if (!empty($fila['imagen_de_perfil'])) {
        $rutaImagen = $baseUrl . "/" . $fila['imagen_de_perfil'];
    }
} else {
    // Si no hay imagen de perfil, asigna la ruta de la imagen por defecto
    $rutaImagen = $baseUrl . "/imagenes/default.jpg";
}

$consultaEstado = "SELECT estado FROM evaluaciones_activas WHERE id = 1";


$consultaEstado = "SELECT estado FROM evaluaciones_activas WHERE id = 1";
$resultadoEstado = $conn->query($consultaEstado);
$filaEstado = $resultadoEstado->fetch_assoc();
$estadoEvaluaciones = $filaEstado['estado'];

$query_calificaciones_ingresadas = "SELECT calificaciones_ingresadas FROM alumnos WHERE matricula = ?";
$stmt_calificaciones_ingresadas = $conn->prepare($query_calificaciones_ingresadas);
$stmt_calificaciones_ingresadas->bind_param("i", $usuario);
$stmt_calificaciones_ingresadas->execute();
$result_calificaciones_ingresadas = $stmt_calificaciones_ingresadas->get_result();
$fila_calificaciones_ingresadas = $result_calificaciones_ingresadas->fetch_assoc();
$calificaciones_ingresadas = $fila_calificaciones_ingresadas['calificaciones_ingresadas'];
// Realiza una consulta para verificar el valor de encuesta_satisfaccion del usuario actual.
$alumno_id = $_SESSION['user'];
$query_check = "SELECT encuesta_satisfaccion FROM alumnos WHERE matricula = $alumno_id";
$result_check = mysqli_query($conn, $query_check);
$row_check = mysqli_fetch_array($result_check);
$encuesta_satisfaccion = $row_check['encuesta_satisfaccion'];

// Establece el valor de $mostrarModal en función de $encuesta_satisfaccion
$mostrarModal = $encuesta_satisfaccion != 1;

$usuario = $_SESSION['user'];


// Obtener el número de strikes del alumno
$sql = "SELECT strikes FROM alumnos WHERE matricula = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$num_strikes = $row['strikes'];

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
    <title>Menú</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <!-- Tema obscuro-->
    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>

    <style>
/* Estilos para los botones */
.btn {
            border-radius: 100px; /* Hacer los botones más redondos */
            background-color: transparent !important; /* Fondo transparente */
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
            margin: 20px;
            height: 350px;
            width: 350px;
            font-weight: bold;
            border: 6px solid #ffffff !important; /* Ahora el borde es más grueso */
            color: #ffffff !important; /* Cambia el color del texto a beige */
        }

        .btn-custom:hover {
            font-size: 25px;
            padding: 15px 50px;
            margin: 20px;
            height: 350px;
            width: 350px;
            font-weight: bold;
            border: 6px solid #ffffff !important; /* Ahora el borde es más grueso */
            color: #ffffff !important; /* Cambia el color del texto a beige */
        }

        /* Estilos personalizados para los iconos */
        .btn-custom i {
            font-size: 90px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: -10px;
            color: #ffffff !important; /* Cambia el color del texto del icono a beige */
        }

        .btn-custom i:hover {
            font-size: 100px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: 10px;
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

.btn-maroon:active,
.btn-maroon:focus,
.btn-vino:active,
.btn-vino:focus {
    outline: none !important; /* Importante para anular el estilo predeterminado */
    box-shadow: none !important; /* Importante para anular el estilo predeterminado (si se aplica) */
}
.alert-primary {
    color: #000;
    background-color: #ffdd05;
    border-color: #000;
}
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-vino bg-maroon">
            <div class="container">
                <a class="navbar-brand hover-effect1" href="indexAlumnos.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
            <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexAlumnos.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                <button id="dark-theme-toggle" class="btn btn-maroon mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                </button>
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
        <a class="dropdown-item hover-effect1" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
    </div>
</li>

   
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <br>
    <?php
    if ($num_strikes == 3) {
        echo "<div class='d-flex justify-content-center'>";
echo "<div class='text-center w-50 alert alert-primary' role='alert' style='text-align: center;'>";
echo "<h2><i class='bi bi-exclamation-triangle-fill' style='font-size: 3em; color: #000000;'></i></h2>
Tienes 3 strikes. Por favor, comunícate con el jefe de carrera para agendar más citas";
echo "</div>";
echo "</div>";


    }
    ?>
    <br>
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center mr-2" onclick="window.location.href='./materias.php'"><i class="bi bi-book"></i><span>Materias</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center mr-2" onclick="window.location.href='./calificaciones.php'"><i class="bi bi-pencil"></i><span>Calificaciones</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center mr-2" onclick="window.location.href='./citas.php'"><i class="bi bi-calendar-check"></i><span>Citas</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center mr-2" onclick="window.location.href='./juego.php'"><i class="bi bi-joystick"></i><span>Juego</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3" id="apkButtonContainer">
                <button id="apkButton" class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center mr-2" onclick="window.location.href='./apk.php'"><i class="bi bi-phone"></i><span>APK PITA</span></button>
            </div>
        </div>
    </div>

    <?php if ($mostrarModal) : ?>
        <div class="modal fade" id="encuestaModal" tabindex="-1" role="dialog" aria-labelledby="encuestaModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark-theme" id="encuestaModalLabel">Encuesta de satisfacción</h5>
                    </div>
                    <form method="POST" action="./php/guardarEncuesta.php">
                        <div class="modal-body text-dark-theme">
                            <ul class="nav nav-tabs custom-class" id="encuestaTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="academico-tab" data-toggle="tab" href="#academico" role="tab" aria-controls="academico" aria-selected="false">Académico</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="personal-tab" data-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="false">Personal</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-3" id="encuestaTabsContent">
                                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                                    <div class="form-group">
                                        <label for="pregunta1">¿Te gusta sistema PITA?</label>
                                        <select name="pregunta1" id="pregunta1" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta11">¿Haz utilizado el sistema PITA?</label>
                                        <select name="pregunta11" id="pregunta11" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta2">¿Te sientes satisfecho con la calidad de la educación que has recibido hasta ahora?</label>
                                        <select name="pregunta2" id="pregunta2" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta3">¿Crees que los profesores están bien preparados y son competentes en sus áreas de especialización?</label>
                                        <select name="pregunta3" id="pregunta3" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta5">¿Recomendarías esta universidad a otros estudiantes?</label>
                                        <select name="pregunta5" id="pregunta5" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="academico" role="tabpanel" aria-labelledby="academico-tab">
                                    <div class="form-group">
                                        <label for="pregunta6">¿Estás satisfecho con la cantidad y calidad de recursos disponibles para los estudiantes, como bibliotecas, laboratorios y tecnología?</label>
                                        <select name="pregunta6" id="pregunta6" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta7">¿Crees que la universidad está preparando adecuadamente a los estudiantes para su carrera y su futuro profesional?</label>
                                        <select name="pregunta7" id="pregunta7" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta8">¿Crees que la universidad se preocupa por el bienestar y la satisfacción de los estudiantes, más allá de su éxito académico?</label>
                                        <select name="pregunta8" id="pregunta8" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                    <div class="form-group">
                                        <label for="pregunta9">¿Has tenido algún problema o conflicto con algún miembro del personal universitario?</label>
                                        <select name="pregunta9" id="pregunta9" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta10">¿Te sientes apoyado por tus profesores y tutores en el proceso de aprendizaje?</label>
                                        <select name="pregunta10" id="pregunta10" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pregunta4">¿Te sientes seguro y cómodo en el campus?</label>
                                        <select name="pregunta4" id="pregunta4" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una respuesta</option>
                                            <option value="1">Sí</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-dark btn-personalizado">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                $(document).ready(function() {
                    $('#encuestaModal').modal('show');
                });
            </script>
        <?php endif; ?>
</body>
<script>
    function isRunningInWebView() {
        return /PITA/i.test(navigator.userAgent);
    }

    function isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    function isRunningInApp() {
        // Para iOS
        if (window.navigator.standalone) {
            return true;
        }

        // Para Android
        if (window.matchMedia("(display-mode: standalone)").matches) {
            return true;
        }

        return false;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const apkButtonContainer = document.getElementById("apkButtonContainer");
        if (!isMobileDevice() || isRunningInApp() || isRunningInWebView()) {
            apkButtonContainer.style.display = 'none';
        }
    });


    $(document).ready(function() {
        const calificacionesIngresadas = <?php echo $calificaciones_ingresadas; ?>;

        if (!calificacionesIngresadas) {
            Swal.fire({
                title: "<h1><i class='bi bi-exclamation-triangle-fill me-2'></i></h1>",
                text: "El periodo de calificaciones ha comenzado y estamos muy entusiasmados por saber cómo es que te fue",
                confirmButtonText: "Ingresar calificaciones",
                allowOutsideClick: false,
                confirmButtonColor: "#800020", // Color del botón Ingresar calificaciones (azul)
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = './ingresarCalificaciones.php';
                }
            });
        }
    });

    const konamiCode = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65];
    let konamiIndex = 0;

    document.addEventListener("keydown", event => {
        if (event.keyCode === konamiCode[konamiIndex]) {
            konamiIndex++;
            if (konamiIndex === konamiCode.length) {
                konamiIndex = 0;
                showSurpriseMessage();
            }
        } else {
            konamiIndex = 0;
        }
    });

    function showSurpriseMessage() {
        Swal.fire({
            title: 'Agradecimientos para...',
            text: "Mi familia que nunca han dejado de creer en mi pese cualquier situación, a mi nueva famlia que es mi esposa, Esmeralda, ella siempre va a estar en mi corazón y le agradezco por enseñarme lo que valgo y lo que puedo hacer y a el profesor Ceslo Marquez por la oportunidad de este proyecto - Alexis Téllez A.",
            icon: 'none',
            confirmButtonText: 'Aceptar',
            customClass: {
                content: 'italic-text'
            }
        });
    }
</script>

</html>