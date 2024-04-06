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

$consulta_estado_evaluaciones = "SELECT estado FROM evaluaciones_activas WHERE id = 1";
$resultado_estado_evaluaciones = $conn->query($consulta_estado_evaluaciones);
$fila_estado_evaluaciones = $resultado_estado_evaluaciones->fetch_assoc();
$estado_evaluaciones = $fila_estado_evaluaciones['estado'];

// Establecer la clase del botón según el estado de 'evaluaciones_activas'
$boton_clase = $estado_evaluaciones == 1 ? "btn-secondary" : "btn-dark";
$boton_texto = $estado_evaluaciones == 1 ? "Desactivar evaluaciones" : "Activar evaluaciones";


$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

    <!-- Modo obscuro -->
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

/* Agrega estilos para el botón .btn-vino y su icono */
.btn-vino,
.btn-vino i {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    color: inherit !important; /* Añade esta línea para heredar el color del texto del botón e icono */
}

/* Agrega estilos para el estado :hover del botón .btn-vino y su icono */
.btn-vino:hover,
.btn-vino:hover i {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
    color: inherit !important; /* Añade esta línea para heredar el color del texto del botón e icono en estado hover */
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
        <div class="row">
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center" onclick="window.location.href='./profesoresMenu.php'"><i class="bi bi-person"></i><span>Profesores</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center" onclick="window.location.href='./alumnos.php'"><i class="bi bi-people"></i><span>Alumnos</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center" onclick="window.location.href='./graficasMenu.php'"><i class="bi bi-bar-chart"></i><span>Gráficas</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center" onclick="window.location.href='./reportesMenu.php'"><i class="bi bi-file-earmark-text"></i><span>Reportes</span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
            <button class="btn btn-dark btn-custom btn-encuesta d-flex flex-column justify-content-center align-items-center" onclick="activarEncuesta()">
                <i class="bi bi-card-checklist"></i>
                <span>Activar encuesta de satisfacción</span>
                <div class="mt-2 text-center">
                    <span class="periodo-activo">Periodo activo: <?php echo $periodo_activo; ?></span><br>
                    <span class="anio-actual">Año actual: <?php echo date("Y"); ?></span>
                </div>
            </button>
        </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn <?php echo $boton_clase; ?> btn-custom d-flex flex-column justify-content-center align-items-center" onclick="window.location.href='./php/activarEvaluaciones.php'"><i class="bi bi-calendar-check"></i><span><?php echo $boton_texto; ?></span></button>
            </div>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3" id="apkButtonContainer">
                <button id="apkButton" class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center mr-2" onclick="window.location.href='./apk.php'"><i class="bi bi-phone"></i><span>APK PITA</span></button>
            </div>
        </div>
    </div>
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

function activarEncuesta() {
    const titulo = "Recuerda primero activar las evaluaciones antes de cambiar de periodo";
const texto = "Al activar la encuesta de satisfacción estarías cambiando el periodo activo al siguiente";

Swal.fire({
    title: titulo,
    text: texto,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Confirmar",
    cancelButtonText: "Cancelar",
    confirmButtonColor: "#ff7f7f", // Color del botón Confirmar (azul)
    cancelButtonColor: "#343a40", // Color del botón Cancelar (rojo)
    reverseButtons: true, // Invierte el orden de los botones
})
.then((result) => {
    if (result.isConfirmed) {
        window.location.href = './php/activarEncuesta.php';
    }
});
    }



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
            title: 'Dedicado para...',
            text: "Mi familia que nunca ha dejado de creer en mí, pese a cualquier situación. A mi nueva familia, que es mi esposa Esmeralda, ella siempre estará en mi corazón y le agradezco por enseñarme lo que valgo y lo que puedo hacer. También agradezco al profesor Celso Márquez por la oportunidad de este proyecto. - Alexis Téllez A.",
            icon: 'none',
            confirmButtonText: 'Aceptar',
            customClass: {
                content: 'italic-text'
            },
            buttonsStyling: {
                backgroundColor: '#0096C7 !important' // Cambia el color a verde
            }
        });
        unlockEasterEggMedal();
    }

    function unlockEasterEggMedal() {
    // Asume que 'nempleado' es la variable que contiene el número de empleado del profesor
    const nempleado = <?php echo $usuario?>;
    
    // Enviar solicitud AJAX para desbloquear la medalla "Easter Egg"
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/easter.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("nempleado=" + nempleado);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            console.log("Medalla desbloqueada");
        }
    };
}
</script>

</html>