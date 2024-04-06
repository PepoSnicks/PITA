<?php
include('../php/conexion.php');

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$correo = $_SESSION['correo'];


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($usuario == null || $usuario == '' || $nombreUsuario == '' || $usuario == 1 || $usuario == 2 || $usuario == 3 || $usuario == 4) {
    header("location:../index.php");
    die();
}

// Get profile image URL
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

// Reemplaza `matricula_alumno` con la matrícula del alumno autenticado
$matricula_alumno = $_SESSION['user'];

$query = "SELECT hc.cuatrimestre, ma.materia, hc.calificacion
          FROM historial_calificaciones hc
          JOIN materias ma ON hc.id_materia = ma.id_materias
          WHERE hc.matricula = ?
          ORDER BY hc.cuatrimestre, ma.materia";

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $matricula_alumno);
$stmt->execute();
$result = $stmt->get_result();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    
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



    <title>Calificaciones</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

                <!-- Tema obscuro-->
                <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>

<style>
    body {
        margin: 0;
        font-family:"Segoe UI",Benedict;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1;
        color: #fff;
        text-align: left;
        background-color: #fff;
        } 
        .btn-dark {
    color: #fff;
    background-color: #8B1D35;
    border-color: #000000;
}
.style attribute {
    background-color: #800020;
    color: #fff;
    text-align: center;
}
    .btn-dark:hover {
        background-color: #800020 !important;
        border-color: #800020 !important;
    }
    .table thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #000;
}
.table td, .table th {
    padding: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #000;
}
    
.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #000;
}

    .btn-personalizado {
        font-weight: bold !important;
        font-size: 20px !important;
    }

    .calificacion {
        text-align: center;
        font-weight: bold;
    }
    #mensajesBtn {
        color: white; /* Cambia el color del texto */
    }

    #mensajesBtn .bi {
        color: white; /* Cambia el color del icono */
    }

    #dark-theme-icon {
        color: white; /* Cambia el color del icono */
    }
    .text-white {
    color: white !important; /* Cambia el color del texto a blanco y usa !important para priorizarlo */
    }
    .icono-verde {
    color: #088C4F !important; /* Cambia el color del icono a verde */
    }
    .mensaje-calificaciones {
    background-color: #8B1D35 !important; /* Cambia el color de fondo a vino */
    color: white !important; /* Cambia el color del texto a blanco */
    }
    .btn-vino {
    background-color: #8B1D35 !important; /* Cambia el color de fondo del botón a vino */
    border-color: #8B1D35 !important; /* Cambia el color del borde del botón a vino */
    color: white !important; /* Cambia el color del texto del botón a blanco */
    }
    .text-blanco {
    color: white !important;
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
</head>

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
                                <a class="dropdown-item hover-effect1" href="./citas.php"><i class="bi bi-calendar"></i> Citas</a>
                                <a class="dropdown-item hover-effect1" href="./juego.php"><i class="bi bi-joystick"></i> Juego</a>
                                <a class="dropdown-item hover-effect1" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

        <div class="container">
    <br>
    <br>
    <div class="d-flex justify-content-center">
        <h1 class="text-white"><i class="bi bi-pencil-fill icono-verde"></i><b> Calificaciones</b></h1>
    </div>
    <br>
    <div class="accordion" id="cuatrimestresAccordion">
        <?php
        $current_cuatrimestre = null;
        $sum_calificaciones = 0;
        $num_calificaciones = 0;
        $counter = 0;
        while ($row = $result->fetch_assoc()) {
            if ($current_cuatrimestre != $row['cuatrimestre']) {
                if ($current_cuatrimestre != null) {
                    echo '</tbody></table></div></div>'; // Cierra el panel del cuatrimestre anterior
                }
                $current_cuatrimestre = $row['cuatrimestre'];
                $counter++;
        ?>
                <div class="card">
                    <div class="card-header" id="heading<?= $counter ?>">
                        <h5 class="mb-0 font-weight-bold">
                            <button class="btn btn-dark btn-personalizado w-100" data-toggle="collapse" data-target="#collapse<?= $counter ?>" aria-expanded="true" aria-controls="collapse<?= $counter ?>">
                                Cuatrimestre <?= $current_cuatrimestre ?>
                            </button>
                        </h5>
                    </div>
                    <div id="collapse<?= $counter ?>" class="collapse" aria-labelledby="heading<?= $counter ?>" data-parent="#cuatrimestresAccordion">
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr style="background-color: #800020; color:#fff; text-align:center;">
                                        <th scope="col">Materia</th>
                                        <th scope="col">Calificación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                            }
                            $calificacion_color = $row['calificacion'] < 6 ? '#ff7f7f' : '#ffffff';
                            echo '<tr><td style="background-color: ' . $calificacion_color . ';">' . $row['materia'] . '</td><td class="calificacion" style="background-color: ' . $calificacion_color . ';">' . $row['calificacion'] . '</td></tr>';
                            $sum_calificaciones += $row['calificacion'];
                            $num_calificaciones++;
                        }
                        if ($current_cuatrimestre != null) {
                            echo '</tbody></table></div></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Muestra mensaje si no hay calificaciones
        if ($counter == 0) {
            echo '<div class="d-flex justify-content-center">
            <div class="alert alert-primary text-center w-80 mensaje-calificaciones" role="alert">
                <i class="bi bi-emoji-smile-fill text-blanco" style="font-size: 5rem;"></i>
                <br>
                <br>
                <h3>Cuando termines tu cuatrimestre actual podrás visualizar tus calificaciones en este espacio. <br><br> ¡Mucho éxito!</h3>
            </div>
        </div>';
        }
        ?>


</body>

</html>