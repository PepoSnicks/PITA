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
    <title>Juego</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

        <!-- Tema obscuro-->
        <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>

    <style>
        canvas {
            display: block;
            margin: 0 auto;
            background-color: #f0f0f0;
            border: 1px solid #000;
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
        canvas {
  display: block;
  margin: 0 auto;
  background-color: #f0f0f0;
  border: 1px solid #000;
}

@media (min-width: 768px) {
  canvas {
    width: 400px;
    height: 400px;
  }
}

@media (max-width: 767px) {
  canvas {
    width: 100%;
    height: 390px;
  }
}

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


    </style>
</head>
<header>
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
                <button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                </button>
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./indexAlumnos.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./citas.php"><i class="bi bi-calendar"></i> Citas</a>
                                <a class="dropdown-item hover-effect1" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
            <div id="gameInfo" class="text-center" style="margin-bottom: 1rem; color: white;">
    <h3 id="scoreDisplay"><b>Puntaje: 0</b></h3>
</div>
                <canvas id="gameCanvas" width="400" height="400"></canvas>
                <script src="juego.js" defer></script>
                <div id="gameInfo" style="display: flex; justify-content: center; align-items: center; margin-bottom: 1rem;">
                    <br>
                    <br>
                    <button id="restartBtn" onclick="restartGame()" class="btn btn-dark btn-opciones btn-vino">Reiniciar</button>
                </div>
            </div>
            <div class="col-md-6">
            <div class="text-center" style="margin-bottom: 1rem; color: white;">
    <h3><b><i class="bi bi-trophy-fill text-warning"></i> Mejores puntajes</b></h3>
</div>

                <table id="scoreTable" class="table table-striped table-light">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Puntaje</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        
        function displayTopScores(data) {
            const table = $('#scoreTable tbody');
            table.empty();
            data.sort((a, b) => b.score - a.score); // Ordenar por puntaje descendente
            data.slice(0, 10).forEach((score, index) => { // Tomar los primeros 10 puntajes
                const row = $('<tr>');
                row.append($('<th>').text(index + 1));
                row.append($('<td>').text(score.nombre));
                row.append($('<td>').text(score.score));
                table.append(row);
            });
        }


        function restartGame() {
            location.reload();
        }

        $(document).ready(function() {
            getTopScores();
        });
    </script>

</body>

</html>