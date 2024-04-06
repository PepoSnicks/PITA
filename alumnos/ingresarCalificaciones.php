<?php

include('./php/conexion.php');

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$correo = $_SESSION['correo'];


if ($usuario == null || $usuario == '' || $nombreUsuario == '' || $usuario == 1 || $usuario == 2 || $usuario == 3 || $usuario == 4) {
    header("location:../index.php");
    die();
}



//evitar que se acceda desde la URL
$query_get_evaluaciones_activas = "SELECT estado FROM evaluaciones_activas WHERE id = 1";
$stmt_get_evaluaciones_activas = $conn->prepare($query_get_evaluaciones_activas);
$stmt_get_evaluaciones_activas->execute();
$stmt_get_evaluaciones_activas->bind_result($estado);
$stmt_get_evaluaciones_activas->fetch();
$stmt_get_evaluaciones_activas->close();

if ($estado != 1) {
    header("location:./indexAlumnos.php");
    die();
}


//Conseguir la imagen de perfil
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

// Consulta las materias del alumno
$query = "SELECT a.id_asignaturasa, a.id_materias, a.calificacion, m.materia FROM asignaturasa AS a JOIN materias AS m ON a.id_materias = m.id_materias WHERE a.matricula = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$materias = $result->fetch_all(MYSQLI_ASSOC);

// Consulta el promedio del alumno
$query_promedio = "SELECT promedio FROM alumnos WHERE matricula = ?";
$stmt_promedio = $conn->prepare($query_promedio);
$stmt_promedio->bind_param("i", $usuario);
$stmt_promedio->execute();
$result_promedio = $stmt_promedio->get_result();
$fila_promedio = $result_promedio->fetch_assoc();
$promedio_actual = round($fila_promedio['promedio'], 2);

$calificacionesIngresadas = false;

foreach ($materias as $materia) {
    if (!is_null($materia['calificacion'])) {
        $calificacionesIngresadas = true;
        break;
    }
}



?>

<!DOCTYPE html>
<html>

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
    <title>Ingresar calificiones</title>
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
    #descripcion {
        height: 100px;
    }

    .agendar-btn:hover {
        background-color: #0096C7;
        border-color: #0096C7;

    }

    .pagination .page-link {
        background-color: #343A40;
        color: white;
    }

    .pagination .page-item.active .page-link {
        background-color: #0096C7;
    }

    th {
        cursor: pointer;
    }
    .btn-dark {
    color: #fff;
    background-color: #8B1D35;
    border-color: #8B1D35;
}
.icono-verde {
    color: #088C4F !important; /* Cambia el color del icono a verde */
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

.btn-maroon:active,
.btn-maroon:focus,
.btn-vino:active,
.btn-vino:focus {
    outline: none !important; /* Importante para anular el estilo predeterminado */
    box-shadow: none !important; /* Importante para anular el estilo predeterminado (si se aplica) */
}
    .agendar-btn {
        /* Estilos base del botón */
        color: #fff;
        background-color: #8B1D35;
        border-color: #8B1D35;
    }

    /* Cambio de color al pasar el cursor sobre el botón */
    .agendar-btn:hover {
        background-color: #800020; /* Cambia a color rojo #800020 */
        border-color: #800020; /* También cambia el color del borde al mismo color */
    }
    #btnGuardar:hover {
        background-color: #800020; /* Cambia a color rojo #800020 */
        border-color: #800020; /* También cambia el color del borde al mismo color */
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="text-center">
                    <br>
                    <h1><i class="bi bi-book-fill icono-verde"></i><b> Ingresar calificaciones</b></h1>
                    <br>
                    <br>

                </div>
                <form method="POST" action="./php/guardarCalificaciones.php">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3><b>Tu promedio actual:</b> <span id="average"><?php echo $promedio_actual; ?></span></h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <h3><b>Promedio nuevo:</b> <span id="newAverage">0.00</span></h3>
                            </div>
                        </div>
                    </div>
                    <br>

                    <br>
                    <?php foreach ($materias as $materia) : ?>
                        <div class="form-group row">
                            <label for="calificacion_<?php echo $materia['id_asignaturasa']; ?>" class="col-md-6 col-form-label"><?php echo $materia['materia']; ?></label>
                            <div class="col-md-6">
                                <input type="number" name="calificaciones[<?php echo $materia['id_asignaturasa']; ?>]" id="calificacion_<?php echo $materia['id_asignaturasa']; ?>" value="0" min="0" max="10" step="1" class="form-control" required>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <br>
                    <br>
                    <div class="text-center">
                        <button id="btnGuardar" type="button" class="btn btn-dark agendar-btn">Guardar calificaciones</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const usuario = '<?php echo $nombreUsuario ?>';



            $('#btnGuardar').click(function() {
                $('#btnGuardar').click(function() {
                    Swal.fire({
                        title: '¿Estás seguro de guardar estas calificaciones?',
                        text: 'Una vez guardadas, no podrás editarlas y solo podrás hacerlo acudiendo al jefe de carrera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, guardar',
                        confirmButtonColor: '#8B1D35', // Cambia el color del botón de confirmar a verde
                        cancelButtonText: 'Cancelar',
                        cancelButtonColor: '#800020' // Cambia el color del botón de cancelar a rojo
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Aquí llamas a la función que guarda las calificaciones
                            $('form').submit();
                        }
                    });
                });


            });
        });
        $(document).ready(function() {
            function calculateAverage() {
                let total = 0;
                let count = 0;

                $('input[type=number]').each(function() {
                    const value = parseFloat($(this).val());
                    if (!isNaN(value)) {
                        total += value;
                        count++;
                    }
                });

                const average = count > 0 ? total / count : 0;
                $('#newAverage').text(average.toFixed(2));
            }

            $('input[type=number]').on('input', calculateAverage);

            calculateAverage();
        });
    </script>


</body>

<?php
$errorUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

function showAlert($icon, $text, $title, $approved = false)
{
    $timer = 3000; // Tiempo en milisegundos (3 segundos)

    echo "<script>
            function showAlert() {
                Swal.fire({
                    icon: '{$icon}',
                    text: '{$text}',
                    title: '{$title}',
                    timer: {$timer},
                    showConfirmButton: false,
                    allowOutsideClick: true
                }).then(() => {
                    " . ($approved ? "window.location.href='./perfil.php'" : "window.history.back();") . "
                });
            }
            showAlert();
        </script>";
}


$approved = strpos($errorUrl, "calificaciones=approved") !== false;

if ($approved) {
    showAlert('success', 'Si llegaste a ingresar una calificación erronea, comunicacte con tu jefe de carrera y pidele que actualice tu promedio', 'Calificaciones actualizadas', true);
} elseif (strpos($errorUrl, "calificaciones=error") !== false) {
    showAlert('error', 'Revisa las calificaciones y vuelvelo a intentar', 'Error al actualizar tus calificaciones');
}
?>


</html>