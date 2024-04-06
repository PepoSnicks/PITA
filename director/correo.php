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
        .btn-secondary {
            background-color: #0096C7;
        }

        .btn-red {
            background-color: #ff7f7f;
        }

        .btn-personalizado {
            background-color: #343a40;
            border-color: #343a40;
            color: #fff;
        }

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

        .periodo-activo,
        .anio-actual {
            font-size: 18px;
        }

        .btn-encuesta {
            font-size: 24px;
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
        .btn-primary {
  color: #fff;
  background-color: #007bff;
  border-color: #2db53f;
}
    </style>
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-maroon">
            <div class="container">
                <a class="navbar-brand" href="indexDirector.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
                    <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center" href="indexDirector.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                    <button id="mensajesBtn" class="btn btn-maroon mr-2" onclick="window.location.href='./mensajes.php'">
                        <i class="bi bi-envelope-fill text-white" data-toggle="tooltip" title="Mensajes"></i>
                    </button>
                    <button id="dark-theme-toggle" class="btn btn-maroon mr-2" data-toggle="tooltip" title="Tema obscuro">
                        <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                    </button>
                    <button id="mensajesBtn" class="btn btn-maroon mr-2 text-white" onclick="window.location.href='indexDirector.php'">Regresar</button>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="btn btn-dark dropdown-toggle mr-2 d-flex align-items-center" href="#" id="usuarioDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="<?php echo $rutaImagen; ?>" alt="" class="rounded-circle mr-2 img-top" style="width: 30px; height: 30px;">
                                <?php echo $nombreUsuario; ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="usuarioDropdown">
                            <a class="dropdown-item" href="./perfil.php"><i class="bi bi-person"></i> Perfil</a>
                            <a class="dropdown-item" href="./graficasMenu.php"><i class="bi bi-bar-chart"></i> Gráficas</a>
                                <a class="dropdown-item" href="./correo.php"><i class="bi bi-envelope"></i> Enviar correo</a>
                                <a class="dropdown-item" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
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
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center">
            <h2 style="color: #ffffff;"><i class="bi bi-envelope-fill" style="color: #2db53f;"></i> <b>Enviar un correo a los alumnos</b></h2>
            </div>
            <br>
            <form action="enviar_correo.php" method="post" id="correo-form">
                <div class="form-group">
                    <label for="titulo" style="color: #ffffff;" >Asunto:</label>
                    <input type="text" class="form-control" name="titulo" id="titulo" required>
                </div>
                <div class="form-group">
                    <label for="encabezado" style="color: #ffffff;" >Encabezado:</label>
                    <input type="text" class="form-control" name="encabezado" id="encabezado" required>
                </div>
                <div class="form-group">
                    <label for="parrafo" style="color: #ffffff;" >Contenido del correo:</label>
                    <textarea class="form-control" name="parrafo" id="parrafo" required rows="15"></textarea>
                </div>
                <br>
                <div class="text-center">
  <button type="button" class="btn btn-primary" id="enviar-correo-btn" style="background-color: #2db53f; color: white;">Enviar correo</button>
</div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.tiny.cloud/1/1jixsi2coqgtlis6fek8rbjt7c6l8eqwyihcgv4hoopgsir2/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
  selector: 'textarea#parrafo',
  plugins: 'lists link image charmap print preview anchor textcolor',
  toolbar: 'undo redo | formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  height: 400,
  language: 'es'
});

document.getElementById('enviar-correo-btn').addEventListener('click', function () {
    // Obtiene el contenido del editor TinyMCE
    var content = tinymce.get('parrafo').getContent();

    // Verifica si el contenido está vacío
    if (content.trim() === '') {
        Swal.fire({
            title: 'Aun no has escrito contenido para el correo',
            text: 'El contenido de tu mensaje es lo importante para la comunidad estudiantil',
            icon: 'error',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    } else {
        // Si el contenido no está vacío, envía el formulario
        enviarFormulario();
    }
});

function enviarFormulario() {
    var titulo = document.getElementById('titulo').value;
    var encabezado = document.getElementById('encabezado').value;
    var contenido = tinymce.get('parrafo').getContent();

    var formData = new FormData();
    formData.append('titulo', titulo);
    formData.append('encabezado', encabezado);
    formData.append('parrafo', contenido);

    $.ajax({
        type: 'POST',
        url: 'enviar_correo.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response === 'success') {
                Swal.fire({
                    title: 'El correo se ha enviado exitosamente',
                    text: 'La comunidad estudiantil estará informada',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'Error al enviar el correo: ' + response,
                    icon: 'error',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error de conexión',
                text: 'Error en la conexión con el servidor, vuélvelo a intentar más tarde',
                icon: 'error',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });
        }
    });
}

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
    }
</script>
</body>
</html>