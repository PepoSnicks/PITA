<?php
include("../php/conexion.php");
include("./php/encrypt.php");

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

$por_pagina = 5; // Aquí puedes cambiar el número de registros por página
// Consulta para obtener las notas del usuario
$sql = "SELECT * FROM notas WHERE nempleado = '" . $_SESSION['user'] . "'";
$resultNotas = $conn->query($sql);
list($resultNotas, $paginas, $pagina_actual) = paginar($conn, $sql, $por_pagina);

$conn->close();

function paginar($conn, $consulta, $por_pagina = 15)
{
    $resultado = $conn->query($consulta);
    $total_registros = $resultado->num_rows;

    $paginas = ceil($total_registros / $por_pagina);
    $pagina_actual = (isset($_GET['pagina'])) ? (int)$_GET['pagina'] : 1;
    $inicio = ($pagina_actual - 1) * $por_pagina;

    $consulta .= " LIMIT $inicio, $por_pagina";
    $resultado = $conn->query($consulta);

    return array($resultado, $paginas, $pagina_actual);
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

    <title>Notas</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>


    <style>
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

/* Estilos para el estado :hover (al pasar el cursor) */
.btn-custom:hover {
    background-color: #8B1D35 !important;
    border-color: #8B1D35 !important;
    color: #FFFFFF !important;
}

/* Establecer estilos generales para la clase .btn */
.btn {
  border-radius: 100px;
  border-color: transparent !important;
  color: #FFFFFF !important; /* Establecer el color del texto (iconos) en blanco */
}

.text-dark-theme {
      color: #000; /* Color negro */
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
.page-item.active .page-link {
    z-index: 1;
    color: #fff;
    background-color: #8B1D35;
    border-color: #8B1D35;
}

.navbar {
  background-color: #8B1D35;
}
    </style>
</head>

<body>

<header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-vino">
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
                    <button id="mensajesBtn" class="btn btn-maroon mr-2 text-white hover-effect1" onclick="window.location.href='indexJefe.php'">Regresar</button>
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
                                <a class="dropdown-item" href="./citasMenu.php"><i class="bi bi-calendar"></i> Citas</a>
                                <a class="dropdown-item" href="./horarioProfesor.php"><i class="bi bi-clock"></i> Horario</a>
                                <a class="dropdown-item" href="./profesoresMenu.php"><i class="bi bi-person"></i> Profesores</a>
                                <a class="dropdown-item" href="./reportesMenu.php"><i class="bi bi-file-earmark-text"></i> Reportes</a>
                                <a class="dropdown-item" href="./medallas.php"><i class="bi bi-trophy"></i> Medallas</a>
                                <a class="dropdown-item" href="../php/salir.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-lg-4 col-md-6">
                <h2 style="color: #fff;"><i class="bi bi-save-fill" style="color: #088C4F;"></i> <b>Guardar una nota</b></h2>
                <form id="guardarNotaForm" method="POST">
                    <div class="form-group">
                        <label for="titulo">Título:</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" maxlength="50" required>
                    </div>
                    <div class="form-group">
                        <label for="nota">Nota:</label>
                        <textarea class="form-control" id="nota" name="nota" rows="15" maxlength="300" required></textarea>
                        <small class="form-text text-muted"><span id="caracteres">0</span> / 300 caracteres</small>
                    </div>
                    <div class="text-right">
                    <button id="submitBtn" type="submit" class="btn btn-opciones btn-dark" style="background-color: #088C4F; color: white;">Guardar nota</button>                    </div>
                </form>
            </div>
            <div class="col-lg-8 col-md-6">
                <h2 style="color: #fff;"><i class="bi bi-file-text-fill" style="color: #088C4F;"></i> <b>Notas de <?php echo $nombreUsuario ?></b></h2>
                <br>
                <ul class="list-group">
                    <?php if ($resultNotas->num_rows > 0) : ?>
                        <?php while ($row = $resultNotas->fetch_assoc()) : ?>
                            <?php $decryptedTitle = decrypt($row['titulo']); ?>
                            <?php $decryptedNote = decrypt($row['nota']); ?>
                            <li class="list-group-item">
                                <strong class="text-dark-theme"><?php echo htmlspecialchars($decryptedTitle); ?></strong>
                                <br>
                                <p class="text-dark-theme"><?php echo htmlspecialchars($decryptedNote); ?></p>
                                <div class="mt-2">
                                <button class="btn btn-opciones btn-danger btn-sm" data-toggle="modal" data-target="#editModal<?php echo $row['id_nota']; ?>" style="background-color: #088C4F; color: white;">Editar</button>
                    <button class="btn btn-danger btn-delete btn-red btn-sm" data-id="<?php echo $row['id_nota']; ?>" style="background-color: #088C4F; color: white;">Eliminar</button>
                                </div>
                            </li>

                             <!-- Edit Modal -->
                             <div class="modal fade" id="editModal<?php echo $row['id_nota']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-dark-theme" >Editar Nota</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="./php/updateNota.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_nota" value="<?php echo $row['id_nota']; ?>">
                                                <div class="form-group">
                                                    <label for="titulo" class="text-dark-theme">Título:</label>
                                                    <input type="text" class="form-control" id="titulo" name="titulo" maxlength="50" value="<?php echo htmlspecialchars($decryptedTitle); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="nota" class="text-dark-theme">Nota:</label>
                                                    <textarea class="form-control" id="nota" name="nota" rows="5" maxlength="300" required><?php echo htmlspecialchars($decryptedNote); ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-opciones btn-dark" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-opciones btn-dark">Guardar cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <li class="list-group-item" style="color: #000;">Aún no has escrito una nota</li>
                    <?php endif; ?>
                </ul>
                <br>
                <nav id="citas-pagination" aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $paginas; $i++) : ?>
                            <li class="page-item <?php echo ($i == $pagina_actual) ? 'active' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</body>

<script>

document.addEventListener('DOMContentLoaded', function () {
        <?php if (isset($_SESSION['update_success'])): ?>
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: 'La nota se ha editado con éxito',
                timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
            });
            <?php unset($_SESSION['update_success']); ?>
        <?php endif; ?>
    });
    const notaTextarea = document.getElementById("nota");
    const caracteresSpan = document.getElementById("caracteres");

    notaTextarea.addEventListener("input", function() {
        caracteresSpan.textContent = notaTextarea.value.length;
    });


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

        $(document).ready(function() {
    $('.btn-delete').on('click', function() {
        const idNota = $(this).data('id');

        Swal.fire({
        title: '¿Estás seguro de que deseas eliminar esta nota?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#343a40',
        cancelButtonColor: '#ff7f7f',
        reverseButtons: true
        }).then((result) => {
        if (result.isConfirmed) {
            $.post('./php/deleteNota.php', { id_nota: idNota }, function(response) {
            if (response === 'success') {
                Swal.fire({
                title: 'Nota eliminada',
                text: 'La nota ha sido eliminada',
                icon: 'success',
                showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true  
                }).then(() => {
                location.reload(); // Recarga la página para actualizar la lista de notas
                });
            } else {
                Swal.fire({
                title: 'Error al crear la nota',
                text: 'No se pudo eliminar la nota. Por favor, inténtalo de nuevo',
                icon: 'error',
                showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true 
                });
            }
            });
        }
        });
    });
    });

    $(document).ready(function () {
  $('#submitBtn').on('click', async function (e) {
    e.preventDefault();

    let titulo = $('#titulo').val();
    let nota = $('#nota').val();

    if (titulo.length === 0 || nota.length === 0) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Por favor, completa todos los campos del formulario.',
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
      });
      return;
    }

    try {
      const response = await $.ajax({
        url: './php/guardarNota.php',
        type: 'POST',
        data: {
          titulo: titulo,
          nota: nota,
        },
      });

      if (response === 'success') {
    Swal.fire({
        icon: 'success',
        title: 'Tu nota se ha guardado',
        text: 'La nota se ha guardado correctamente.',
        timer: 1500,
        timerProgressBar: true,
        showConfirmButton: false
    }).then(() => {
        location.reload(); // Recarga la página para actualizar la lista de notas
    });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al guardar la nota: ' + response,
          timer: 2000,
          timerProgressBar: true,
          showConfirmButton: false,
        });
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error en la solicitud de guardado de la nota con error: ' + error.statusText,
        timer: 2000,
        timerProgressBar: true,
        showConfirmButton: false,
      });
    }
  });
});


</script>

</body>


</html>