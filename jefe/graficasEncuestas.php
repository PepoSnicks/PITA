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
.chart-container-center {
    display: flex;
    justify-content: center !important;
    align-items: center;
    height: 70vh; /* Ajusta la altura según sea necesario */
}
#chart{
    background-color: #fff
}

.navbar {
  background-color: #8B1D35;
}
.chart-container {
            width: 100%;
            max-width: 800px;
            height: 400px;
            margin: 2rem auto;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-maroon">
            <div class="container">
                <a class="navbar-brand" href="indexJefe.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
                    <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center" href="indexJefe.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
            <button id="mensajesBtn" class="btn btn-vino mr-2 hover-effect" onclick="window.location.href='./mensajes.php'">
    <i class="bi bi-envelope-fill text-white" data-toggle="tooltip" title="Mensajes"></i>
</button>
<button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
</button>
                    <button id="mensajesBtn" class="btn btn-maroon mr-2 text-white hover-effect1" onclick="window.location.href='graficasMenu.php'">Regresar</button>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
    <a class="btn btn-dark dropdown-toggle mr-2 d-flex align-items-center" href="#" id="usuarioDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                <a class="dropdown-item" href="./perfil.php"><i class="bi bi-person"></i> Perfil</a>
                                <a class="dropdown-item" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
                                <a class="dropdown-item" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
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
        <h1 class="text-center" style="color: #fff;"><i class="bi bi-bar-chart-fill" style="color: #088C4F;"></i> <b>Gráficas de encuentas de satisfacción</b></h1>        <br>
        <br>
        <div class="row">
            <div class="col-md-6">
                <form id="form-resultados">
                    <div class="form-group">
                        <label for="anio" style="color: #000;">Año:</label>
                        <input type="number" class="form-control" id="anio" name="anio" placeholder="Ejemplo: 2023" required>
                    </div>
                    <div class="form-group">
                        <label for="periodo" style="color: #000;">Periodo:</label>
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
                        <label for="anio2" style="color: #000;">Año para comparar (opcional):</label>
                        <input type="number" class="form-control" id="anio2" name="anio2" placeholder="Ejemplo: 2022">
                    </div>
                    <div class="form-group">
                        <label for="periodo2" style="color: #000;">Periodo para comparar (opcional):</label>
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

    <div class="chart-container">
        <canvas id="chart"></canvas>
    </div>

    <script>
        $(document).ready(function() {
            let chart;

            function calcularRespuestas(datos) {
                const respuestas = Array(11).fill(0);

                datos.forEach(registro => {
                    for (let i = 1; i <= 11; i++) {
                        respuestas[i - 1] += parseInt(registro['pregunta' + i]);
                    }
                });

                return respuestas;
            }

            // Función para obtener los datos y actualizar la gráfica
            function actualizarGrafica(anio, periodo, anio2 = null, periodo2 = null) {
                $.ajax({
                    url: './php/resultados.php',
                    method: 'POST',
                    data: {
                        anio: anio,
                        periodo: periodo
                    },
                    dataType: 'json',
                    success: function(datos) {
                        const respuestas = calcularRespuestas(datos);

                        if (anio2 && periodo2) {
                            $.ajax({
                                url: './php/resultados.php',
                                method: 'POST',
                                data: {
                                    anio: anio2,
                                    periodo: periodo2
                                },
                                dataType: 'json',
                                success: function(datos2) {
                                    const respuestas2 = calcularRespuestas(datos2);
                                    mostrarGrafica(respuestas, respuestas2, anio, anio2);
                                }
                            });
                        } else {
                            mostrarGrafica(respuestas);
                        }
                    }
                });
            }

            function mostrarGrafica(respuestas, respuestas2 = null, anio = '', anio2 = '') {
                const ctx = document.getElementById('chart').getContext('2d');

                if (chart) {
                    chart.destroy();
                }

                const datasets = [{
                    label: `Resultados de la encuesta ${anio}`,
                    data: respuestas,
                    backgroundColor: 'rgba(0,150,199,0.55)',
                    borderColor: '#343A40',
                    borderWidth: 1
                }];

                if (respuestas2) {
                    datasets.push({
                        label: `Resultados de la encuesta ${anio2}`,
                        data: respuestas2,
                        backgroundColor: 'rgba(255, 99, 132, 0.55)',
                        borderColor: '#343A40',
                        borderWidth: 1
                    });
                }

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['¿Te gusta sistema PITA?', '¿Te sientes satisfecho con la calidad de la educación que has recibido hasta ahora?', '¿Crees que los profesores están bien preparados y son competentes en sus áreas de especialización?', '¿Te sientes seguro y cómodo en el campus?', '¿Recomendarías esta universidad a otros estudiantes?', '¿Estás satisfecho con la cantidad y calidad de recursos disponibles para los estudiantes, como bibliotecas, laboratorios y tecnología?', '¿Crees que la universidad está preparando adecuadamente a los estudiantes para su carrera y su futuro profesional?', '¿Crees que la universidad se preocupa por el bienestar y la satisfacción de los estudiantes, más allá de su éxito académico?', '¿Has tenido algún problema o conflicto con algún miembro del personal universitario?', '¿Te sientes apoyado por tus profesores y tutores en el proceso de aprendizaje?', '¿Haz utilizado el sistema PITA?'],
                        datasets: datasets
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            // Controlador de eventos del botón "Mostrar gráficas"
            $('#btn-mostrar-graficas').click(function() {
                const anio = $('#anio').val();
                const periodo = $('#periodo').val();
                const anio2 = $('#anio2').val() || anio; // Asigna el valor de anio si anio2 no se selecciona
                const periodo2 = $('#periodo2').val() || null; // Obtiene el segundo periodo, si existe
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