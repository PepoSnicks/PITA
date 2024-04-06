<?php


include("../php/conexion.php");

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$idnivel = $_SESSION['idnivel'];

if ($usuario == null || $usuario == '' || $idnivel != 5 || $nombreUsuario == '') {
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <title>Gráficas encuestas</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Modo obscuro -->
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

/* Agrega este bloque de estilos al final de tu hoja de estilos o en un archivo separado */

/* Estilo para la clase form-group */
.form-group {
    background-color: #fff; /* Color blanco de fondo */
    color: #000; /* Color del texto en el form-group (puede ser negro u otro color oscuro) */
    border: 1px solid #000; /* Borde del form-group (puede ser del mismo color que el texto) */
    padding: 15px; /* Ajusta el espaciado interior según sea necesario */
    border-radius: 8px; /* Añade esquinas redondeadas si deseas */
}
#problemasChart, #problemasChart2 {
        max-width: 300px; /* Define el ancho máximo deseado */
        width: 100%; /* Asegura que ocupe el 100% del contenedor */
        height: auto; /* Permite que la altura se ajuste automáticamente */
        margin: 0 auto; /* Centra la gráfica en el contenedor */
    }
#problemasChart{
    background-color: #fff
}
#problemasChart2{
    background-color: #fff
}

.navbar {
  background-color: #8B1D35;
}

    </style>

</head>

<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-vino bg-maroon">
            <div class="container">
                <a class="navbar-brand hover-effect1" href="indexDirector.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
            <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexDirector.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                <button id="mensajesBtn" class="btn btn-vino mr-2 hover-effect" onclick="window.location.href='./mensajes.php'">
                    <i class="bi bi-envelope-fill" data-toggle="tooltip" title="Mensajes"></i>
                </button>
                <button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                </button>
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./graficasMenu.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./graficasMenu.php"><i class="bi bi-person-check"></i> Profesores</a>
                                <a class="dropdown-item hover-effect1" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
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
        <h1 class="text-center" style="color: #ffffff;"><i class="bi bi-bar-chart-fill" style="color: #088C4F;"></i> <b>Gráficas de problemas</b></h1>
        <br>
        <br>
        <div class="row">
            <div class="col-md-6">
                <form id="form-resultados">
                    <div class="form-group">
                        <label for="anio" style="color: #000000;">Año:</label>
                        <input type="number" class="form-control" id="anio" name="anio" placeholder="Ejemplo: 2023" required>
                    </div>
                    <div class="form-group">
                        <label for="periodo" style="color: #000000;">Periodo:</label>
                        <select class="form-control" id="periodo" name="periodo" required>
                            <option value="1">Enero - Abril</option>
                            <option value="2">Mayo - Agosto</option>
                            <option value="3">Septiembre - Diciembre</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form id="form-resultados2">
                    <div class="form-group">
                        <label for="anio2" style="color: #000000;">Año para comparar (opcional):</label>
                        <input type="number" class="form-control" id="anio2" name="anio2" placeholder="Ejemplo: 2022">
                    </div>
                    <div class="form-group">
                        <label for="periodo2" style="color: #000000;">Periodo para comparar (opcional):</label>
                        <select class="form-control" id="periodo2" name="periodo2">
                            <option value="">Ninguno</option>
                            <option value="1">Enero - Abril</option>
                            <option value="2">Mayo - Agosto</option>
                            <option value="3">Septiembre - Diciembre</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center">
        <button type="button" class="btn btn-dark btn-opciones" id="btn-mostrar-graficas">Mostrar gráficas</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="chart-container text-center">
                <h4 class="text-center" style="color: #fff;"><b>Problemas psicológicos</b></h4>
                <canvas id="problemasChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container text-center">
                <h4 class="text-center" style="color: #fff;"><b>Problemas académicos</b></h4>
                <canvas id="problemasChart2"></canvas>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let chart;

            function actualizarGrafica(anio, periodo, anio2 = null, periodo2 = null) {
                $.ajax({
                    url: './php/obtenerProblemasGrafica4.php',
                    method: 'POST',
                    data: {
                        anio: anio,
                        periodo: periodo,
                        anio2: anio2,
                        periodo2: periodo2
                    },
                    dataType: 'json',
                    success: function(data) {
                        mostrarGrafica(data, anio, periodo, anio2, periodo2);
                    }
                });
            }

            function mostrarGrafica(data, anio, periodo, anio2 = null, periodo2 = null) {
                const ctx = document.getElementById('problemasChart2').getContext('2d');

                if (chart) {
                    chart.destroy();
                }

                // Define colores para los segmentos de la gráfica circular
                const backgroundColors = [
                    'rgba(119, 158, 203, 0.7)',
                    'rgba(140, 159, 217, 0.7)',
                    'rgba(135, 171, 190, 0.7)',
                    'rgba(143, 169, 199, 0.7)',
                    'rgba(120, 168, 195, 0.7)',
                    'rgba(126, 191, 214, 0.7)',
                    'rgba(156, 192, 214, 0.7)',
                    'rgba(138, 184, 196, 0.7)',
                    'rgba(123, 177, 189, 0.7)',
                    'rgba(109, 178, 195, 0.7)',
                ];

                const borderColors = [
                    'rgba(119, 158, 203, 1)',
                    'rgba(140, 159, 217, 1)',
                    'rgba(135, 171, 190, 1)',
                    'rgba(143, 169, 199, 1)',
                    'rgba(120, 168, 195, 1)',
                    'rgba(126, 191, 214, 1)',
                    'rgba(156, 192, 214, 1)',
                    'rgba(138, 184, 196, 1)',
                    'rgba(123, 177, 189, 1)',
                    'rgba(109, 178, 195, 1)',
                ];

                const datasets = [{
                    label: `Citas del periodo ${periodo} del año ${anio}`,
                    data: data.data1,
                    backgroundColor: backgroundColors, // Asigna colores de fondo para cada segmento
                    borderColor: borderColors, // Asigna colores de borde para cada segmento
                    borderWidth: 1
                }];

                if (anio2 && periodo2) {
                    datasets.push({
                        label: `Citas del periodo ${periodo2} del año ${anio2}`,
                        data: data.data2,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    });
                }

                chart = new Chart(ctx, {
                    type: 'doughnut', // Cambia a 'doughnut' o 'pie' para una gráfica circular
                    data: {
                        labels: data.labels,
                        datasets: datasets
                    },
                    options: {
                        // Elimina la sección 'scales' para gráficas circulares
                    }
                });
            }

            $('#btn-mostrar-graficas').click(function() {
                const anio = $('#anio').val();
                const periodo = $('#periodo').val();
                const anio2 = $('#anio2').val() || anio;
                const periodo2 = $('#periodo2').val() || null;
                actualizarGrafica(anio, periodo, anio2, periodo2);
            });


        });
        $(document).ready(function() {
            let chart;

            function actualizarGrafica(anio, periodo, anio2 = null, periodo2 = null) {
                $.ajax({
                    url: './php/obtenerProblemasGrafica2.php',
                    method: 'POST',
                    data: {
                        anio: anio,
                        periodo: periodo,
                        anio2: anio2,
                        periodo2: periodo2
                    },
                    dataType: 'json',
                    success: function(data) {
                        mostrarGrafica(data, anio, periodo, anio2, periodo2);
                    }
                });
            }

            function mostrarGrafica(data, anio, periodo, anio2 = null, periodo2 = null) {
                const ctx = document.getElementById('problemasChart').getContext('2d');

                if (chart) {
                    chart.destroy();
                }

                // Define colores para los segmentos de la gráfica circular
                const backgroundColors = [
                    'rgba(119, 158, 203, 0.7)',
                    'rgba(140, 159, 217, 0.7)',
                    'rgba(135, 171, 190, 0.7)',
                    'rgba(143, 169, 199, 0.7)',
                    'rgba(120, 168, 195, 0.7)',
                    'rgba(126, 191, 214, 0.7)',
                    'rgba(156, 192, 214, 0.7)',
                    'rgba(138, 184, 196, 0.7)',
                    'rgba(123, 177, 189, 0.7)',
                    'rgba(109, 178, 195, 0.7)',
                ];

                const borderColors = [
                    'rgba(119, 158, 203, 1)',
                    'rgba(140, 159, 217, 1)',
                    'rgba(135, 171, 190, 1)',
                    'rgba(143, 169, 199, 1)',
                    'rgba(120, 168, 195, 1)',
                    'rgba(126, 191, 214, 1)',
                    'rgba(156, 192, 214, 1)',
                    'rgba(138, 184, 196, 1)',
                    'rgba(123, 177, 189, 1)',
                    'rgba(109, 178, 195, 1)',
                ];

                const datasets = [{
                    label: `Citas del periodo ${periodo} del año ${anio}`,
                    data: data.data1,
                    backgroundColor: backgroundColors, // Asigna colores de fondo para cada segmento
                    borderColor: borderColors, // Asigna colores de borde para cada segmento
                    borderWidth: 1
                }];

                if (anio2 && periodo2) {
                    datasets.push({
                        label: `Citas del periodo ${periodo2} del año ${anio2}`,
                        data: data.data2,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    });
                }

                chart = new Chart(ctx, {
                    type: 'doughnut', // Cambia a 'doughnut' o 'pie' para una gráfica circular
                    data: {
                        labels: data.labels,
                        datasets: datasets
                    },
                    options: {
                        // Elimina la sección 'scales' para gráficas circulares
                    }
                });
            }

            $('#btn-mostrar-graficas').click(function() {
                const anio = $('#anio').val();
                const periodo = $('#periodo').val();
                const anio2 = $('#anio2').val() || anio;
                const periodo2 = $('#periodo2').val() || null;
                actualizarGrafica(anio, periodo, anio2, periodo2);
            });

            // Controlador de eventos para el envío de formularios
            $('#form-resultados, #form-resultados2').submit(function(event) {
                event.preventDefault();
                $('#btn-mostrar-graficas').trigger('click');
            });
        });
    </script>

</body>

</html>