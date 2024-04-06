<?php
include("../php/conexion.php");
include("../php/encrypt.php");
include("../php/medallas.php");

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$idnivel = $_SESSION['idnivel'];


$medallas_info = obtener_medallas_desbloqueadas($usuario);
$medallas = $medallas_info['medallas'];
$total_medallas = $medallas_info['total_medallas'];
$total_medallas_usuario = $medallas_info['total_medallas_usuario'];


if ($usuario == null || $usuario == '' || $idnivel != 2 || $nombreUsuario == '') {
    header("location:../index.php");
    die();
}

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

$sql = "SELECT medallas.id, medallas.nombre, medallas.descripcion, medallas.icono, medallas.color, medallas_profesor.nempleado FROM medallas LEFT JOIN medallas_profesor ON medallas.id = medallas_profesor.medalla_id AND medallas_profesor.nempleado = ? ORDER BY medallas.id";

$conn->close();
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Fontawesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Luego Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

    <!-- Y finalmente Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <title>Medallas</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <!-- Modo obscuro -->
    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="../js/darktheme.js" defer></script>

    <!-- Revision de las citas -->
    <script src="../js/checkCitas.js"></script>
    
    <!-- Revision de los mensajes -->
    <script src="../js/checkMessage.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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
            margin-bottom: -1px;
            color: #ffffff !important; /* Cambia el color del texto del icono a beige */
        }

        .btn-custom i:hover {
            font-size: 100px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: 100px;
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

        <?php
    $styles = "";
    foreach ($medallas as $medalla) {
        $styles .= ".btn-custom-{$medalla['id_medalla']}:hover {
            background-color: {$medalla['color']} !important;
            border-color: {$medalla['color']} !important;
            color: #000 !important;
        }\n";
        $styles .= ".btn-custom-{$medalla['id_medalla']}:hover .medalla-descripcion {
            color: #000 !important;
            font-weight: bold !important;
        }\n";
    }
    echo $styles;
    ?>
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
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./indexTutor.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./horarioProfesor.php"><i class="bi bi-clock"></i> Horario</a>
                                <a class="dropdown-item hover-effect1" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
                                <a class="dropdown-item hover-effect1" href="./reportes.php"><i class="bi bi-file-earmark-text"></i> Reportes</a>
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
            <h2 style="color: #fff;"><i class="bi bi-award-fill" style="color: #088C4F;"></i> <b>Medallas</b></h1>
        </div>
    <div class="row">
        <div class="col-lg-8 col-md-6">
            <h6>Medallas: <?php echo $total_medallas_usuario; ?> / <?php echo $total_medallas; ?></h6>
        </div>
    </div>
    <div class="container">
    <div class="row">
        <?php foreach ($medallas as $medalla): ?>
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <div class="btn btn-dark btn-custom btn-custom-<?php echo $medalla['id_medalla']; ?> d-flex flex-column justify-content-center align-items-center mr-2" title="<?php echo $medalla['descripcion']; ?>">
                    <i class="<?php echo $medalla['icono']; ?>"></i>
                    <span><b><?php echo $medalla['nombre']; ?></b></span>
                    <small class="text-white mt-2 medalla-descripcion"><?php echo $medalla['descripcion']; ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
<script>

</script>

</body>

</html>