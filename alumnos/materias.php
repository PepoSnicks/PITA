<?php
include('./php/conexion.php');

session_start();


$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$correo = $_SESSION['correo'];

if ($usuario == null || $usuario == '' || $nombreUsuario == '') {
    header("location:../index.php");
    die();
}




$query = "SELECT materias.materia, materias.descripcion, carrera.carreras, materias.cuatrimestre, profesores.nombre, profesores.nempleado
    FROM materias
    JOIN carrera ON materias.id_carrera = carrera.id_carrera
    JOIN asignaturasa ON materias.id_materias = asignaturasa.id_materias
    LEFT JOIN asignaturasp ON materias.id_materias = asignaturasp.id_materias
    LEFT JOIN profesores ON asignaturasp.nempleado = profesores.nempleado
    WHERE asignaturasa.matricula = $usuario AND materias.id_materias NOT IN (297, 296, 232, 97)";
$result = mysqli_query($conn, $query);


// Consulta para obtener solo las materias con los ID especificados y que estén registradas para el usuario actual
$estancias = "SELECT materias.materia, materias.descripcion, carrera.carreras, materias.cuatrimestre
    FROM materias
    JOIN carrera ON materias.id_carrera = carrera.id_carrera
    JOIN asignaturasa ON materias.id_materias = asignaturasa.id_materias
    LEFT JOIN asignaturasp ON materias.id_materias = asignaturasp.id_materias
    WHERE asignaturasa.matricula = $usuario AND materias.id_materias IN (297, 296, 232, 97)";
$result_estancias = mysqli_query($conn, $estancias);

$carrera_query = "SELECT carrera.carreras
                  FROM carrera
                  JOIN alumnos ON alumnos.id_carrera = carrera.id_carrera
                  WHERE alumnos.matricula = $usuario";
$result_carrera = mysqli_query($conn, $carrera_query);
$row_carrera = mysqli_fetch_assoc($result_carrera);
$carrera = $row_carrera['carreras'];

// Consulta SQL para obtener el cuatrimestre del alumno
$cuatrimestre_query = "SELECT cuatrimestre FROM alumnos WHERE matricula = '$usuario'";
$result_cuatrimestre = mysqli_query($conn, $cuatrimestre_query);
$row_cuatrimestre = mysqli_fetch_assoc($result_cuatrimestre);
$cuatrimestre = $row_cuatrimestre['cuatrimestre'];

$sql = "SELECT imagen_de_perfil FROM alumnos WHERE matricula = '" . $_SESSION['user'] . "'";
$result_imagen = $conn->query($sql);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$rutaImagen = $baseUrl . "/imagenes/default.jpg";

if ($result_imagen->num_rows > 0) {
    $fila = $result_imagen->fetch_assoc();
    if (!empty($fila['imagen_de_perfil'])) {
        $rutaImagen = $baseUrl . "/" . $fila['imagen_de_perfil'];
    }
}


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
    <title>Materias</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

        <!-- Tema obscuro-->
        <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>

</head>
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
    .pagination .page-link {
        background-color: #343A40;
        color: white;
    }
    .pagination .page-item.active .page-link {
        background-color: #0096C7;
    }
    .table thead.thead-dark th {
        background-color: #8B1D35; /* Cambia el color del encabezado de la tabla aquí */
        color: white; /* Cambia el color del texto del encabezado de la tabla aquí */
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

        .btn {
  border-radius: 100px;
  background-color: #8B1D35 !important; /* Cambiado a color vino */
  border-color: transparent !important;
  color: #FFFFFF !important; /* Establecer el color del texto (iconos) en blanco */
}

.btn:hover {
  color: #FFFFFF !important; /* Mantener el color del texto (iconos) en blanco al pasar el cursor */
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
                                <a class="dropdown-item hover-effect1" href="./calificaciones.php"><i class="bi bi-pencil"></i> Calificaciones</a>
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
    <br>
    <div class="container">
        <div class="text-center">
            <div class="text-center">
            <h1 class="text-white"><i class="bi bi-book-fill icono-verde"></i><b> Materias</b></h1>

            <br>
            <h3 class="text-white"><b>Carrera: </b><?php echo $carrera; ?><br><br><b>Cuatrimestre: </b><?php echo $cuatrimestre; ?></h3>

                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-light">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th scope="col">Materia</th>
                                <th scope="col">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr class="justify-content-center">
                                    <td><?php echo $row['materia']; ?></td>
                                    <td><?php echo $row['descripcion']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>