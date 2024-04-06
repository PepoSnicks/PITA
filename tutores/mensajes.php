<?php

//Verificar que el usuario tenga iniciada la sesión, sino lo manda al login

include("../php/conexion.php");
include './php/encrypt.php';

session_start();

$usuario = $_SESSION['user'];
$nombreUsuario = $_SESSION['nombre'];
$idnivel = $_SESSION['idnivel'];

if ($usuario == null || $usuario == '' || $idnivel != 2 || $nombreUsuario == '') {
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

function paginar($conn, $consulta, $por_pagina = 10)
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

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

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

    <title>Mensajes</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <!-- Modo obscuro -->
    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="../js/darktheme.js" defer></script>

    <!-- Revision de las citas -->
    <script src="../js/checkCitas.js"></script>

    <!-- Revision de los mensajes -->
    <script src="../js/checkMessage.js"></script>

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

.btn-success {
  color: #fff;
  background-color: #8B1D35;
  border-color: #8B1D35;
}

.table-dark {
  color: #fff;
  background-color: #8B1D35;
}

.dataTables_wrapper .pagination .page-item .page-link {
    background-color: #343a40;
    border-color: #343a40;
    color: #fff;
}

.text-dark-theme {
    color: #000;
}

.btn-maroon:active,
.btn-maroon:focus,
.btn-vino:active,
.btn-vino:focus {
    outline: none !important; /* Importante para anular el estilo predeterminado */
    box-shadow: none !important; /* Importante para anular el estilo predeterminado (si se aplica) */
}

.page-item.active .page-link {
  z-index: 1;
  color: #fff;
  background-color: #8B1D35;
  border-color: #8B1D35;
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
                                <a class="dropdown-item" href="./perfil.php"><i class="bi bi-person"></i> Perfil</a>
                                <a class="dropdown-item" href="./citasMenu.php"><i class="bi bi-calendar"></i> Citas</a>
                                <a class="dropdown-item" href="./horarioProfesor.php"><i class="bi bi-clock"></i> Horario</a>
                                <a class="dropdown-item" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
                                <a class="dropdown-item" href="./reportes.php"><i class="bi bi-file-earmark-text"></i> Reportes</a>
                                <a class="dropdown-item hover-effect1" href="./medallas.php"><i class="bi bi-award"></i> Medallas</a>
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
            <h1 class="text-center" style="color: #fff;"><i class="bi bi-envelope-fill" style="color: #088C4F;"></i><b> Mensajes</b></h1>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center mt-3 text-center">
            <div class="col-12">
            <button type="button" class="btn btn-dark" data-toggle="modal" data-target="#sendMessageModal" style="background-color: #800020; color: white;">
    Enviar mensaje
</button>
            </div>
        </div>
    </div>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="list-group">
                    <?php
                    $por_pagina = 6;
                    $sql = "SELECT mensajes.id_mensaje, profesores.nempleado, profesores.nombre, profesores.imagen_de_perfil, mensajes.mensaje, mensajes.leido FROM mensajes INNER JOIN profesores ON mensajes.remitente = profesores.nempleado WHERE mensajes.receptor = '$usuario'";
                    $result = $conn->query($sql);
                    list($result, $paginas, $pagina_actual) = paginar($conn, $sql, $por_pagina);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $leido = $row["leido"] == 1 ? "../imagenes/check-double.png" : "../imagenes/check.png";
                            $mensaje = decrypt($row["mensaje"]); // desencriptar mensaje
                            echo "<div class='list-group-item list-group-item-action d-flex justify-content-between align-items-center' onclick='showMessageDetails(" . json_encode($row) . ")'>";
                            echo "<div class='d-flex' onclick='showMessageDetails(" . json_encode($row) . ")'>"; // Agrega el evento onclick aquí
                            $imagenPerfilRemitente = $baseUrl . "/imagenes/default.jpg";
                            if (!empty($row['imagen_de_perfil'])) {
                                $imagenPerfilRemitente = $baseUrl . "/" . $row['imagen_de_perfil'];
                            }
                            echo "<img src='{$imagenPerfilRemitente}' alt='Imagen de perfil del remitente' class='mr-3' style='width: 40px; height: 40px; border-radius: 50%;'>";
                            echo "<div>";
                            echo "<h5>" . $row["nombre"] . "</h5>";
                            echo "<p>" . $mensaje . "</p>";
                            echo "</div>";
                            echo "</div>"; // Cierra el div envolvente
                            echo "<div>";
                            echo "<img src='{$leido}' alt='Estado de lectura' class='mr-3' style='width: 20px; height: 20px;'>";
                            echo "<button type='button' class='btn btn-success' data-toggle='modal' data-target='#replyMessageModal' onclick='event.stopPropagation(); prepareReplyMessageForm(" . $row["nempleado"] . ")'>Responder</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='list-group-item list-group-item-action align-items-center text-center'>
                        <i class='bi bi-emoji-frown-fill mb-2'></i>
                        <p class='mb-0'>No tienes mensajes aún</p>
                    </div>";
                    }
                    ?>
                </div>

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

    <!-- Modal ver más -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="messageModalLabel">Detalles del mensaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-dark-theme" id="messageModalBody">
                    <!-- Detalles del mensaje se cargarán aquí -->
                </div>
                <div class="modal-footer">
                    <!-- Agrega el siguiente código dentro de la sección "modal-footer" del modal "messageModal" -->
                    <form action='./php/eliminarMensaje.php' method='GET' id="deleteMessageForm">
                        <input type="hidden" name="delete" id="messageIdToDelete">
                        <button type='submit' class='btn btn-opciones btn-dark' name='deleteButton'>Borrar</button>
                    </form>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de respuesta al mensaje -->
    <div class="modal fade" id="replyMessageModal" tabindex="-1" role="dialog" aria-labelledby="replyMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="replyMessageModalLabel">Responder mensaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="./php/enviarMensaje.php" method="POST" id="replyMessageForm">
                    <div class="modal-body text-dark-theme">
                        <div class="form-group text-dark-theme" style="display: none;">
                            <label for="replyProfesorId">ID del profesor receptor</label>
                            <input type="text" class="form-control" id="replyProfesorId" name="profesorId" readonly>
                        </div>
                        <div class="form-group">
                            <label for="replyMessageText">
                                <h6>Mensaje</h6>
                            </label>
                            <textarea class="form-control" id="replyMessageText" name="messageText" rows="10" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn  btn-opciones btn-dark " name="enviarMensaje">Enviar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enviar mensaje Modal -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" role="dialog" aria-labelledby="sendMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="sendMessageModalLabel">Enviar mensaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="./php/enviarMensaje.php" method="POST">
                    <div class="modal-body text-dark-theme">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="profesorName">Nombre del profesor receptor</label>
                                    <input type="text" class="form-control" id="profesorName" name="profesorName" oninput="buscarProfesorPorNombre(this.value);" list="resultado_busqueda" required>
                                    <datalist id="resultado_busqueda"></datalist>
                                    <input type="hidden" class="form-control" id="profesorId" name="profesorId">
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-items-center">
                                <img id="profesorImagen" src="/imagenes/default.jpg" alt="Imagen del profesor" style="width: 70px; height: 70px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="messageText">Mensaje</label>
                            <textarea class="form-control" id="messageText" name="messageText" rows="10" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="enviarMensaje">Enviar</button>
                        <button type="button" class="btn btn-opciones btn-dark" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        function buscarProfesorPorNombre(nombre) {
            if (!nombre) {
                // Si el campo de nombre está vacío, no realiza ninguna búsqueda
                return;
            }

            // Realiza una petición AJAX para obtener los profesores que coinciden con el nombre ingresado
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "php/buscarProfesorPorNombre.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const profesores = JSON.parse(xhr.responseText);

                    // Muestra las coincidencias en una lista desplegable
                    const resultadoDiv = document.getElementById("resultado_busqueda");
                    resultadoDiv.innerHTML = ""; // Limpia los resultados anteriores

                    profesores.forEach(function(profesor) {
                        const opcion = document.createElement("option");
                        opcion.value = profesor.nombre;
                        opcion.setAttribute('data-nempleado', profesor.nempleado);
                        opcion.textContent = profesor.nombre;
                        resultadoDiv.appendChild(opcion);
                    });
                }
            };

            xhr.send("nombre=" + encodeURIComponent(nombre));
        }

        // Añade un controlador de eventos para actualizar el campo oculto profesorId cuando se selecciona un profesor
        document.getElementById('profesorName').addEventListener('input', function() {
            const profesorSeleccionado = document.querySelector(`#resultado_busqueda option[value="${this.value}"]`);
            if (profesorSeleccionado) {
                document.getElementById('profesorId').value = profesorSeleccionado.getAttribute('data-nempleado');
            } else {
                document.getElementById('profesorId').value = '';
            }
        });

        // Añade un controlador de eventos para actualizar la imagen del profesor cuando se selecciona un profesor en la lista
        document.getElementById('profesorName').addEventListener('change', function() {
            const profesorSeleccionado = document.querySelector(`#resultado_busqueda option[value="${this.value}"]`);
            if (profesorSeleccionado) {
                const nempleado = profesorSeleccionado.getAttribute('data-nempleado');

                // Obtener la imagen del profesor seleccionado
                $.ajax({
                    url: "./php/obtenerImagenProfesor.php",
                    dataType: "json",
                    data: {
                        nempleado: nempleado
                    },
                    success: function(data) {
                        if (data.rutaImagen) {
                            $("#profesorImagen").attr("src", data.rutaImagen);
                        } else {
                            $("#profesorImagen").attr("src", "/imagenes/default.jpg");
                        }
                    }
                });
            } else {
                $("#profesorImagen").attr("src", "/imagenes/default.jpg");
            }
        });

        // Función para recuperar los mensajes
        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', './php/nuevosMensajes.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.newMessages) {
                        updateMessagesTable();
                    }
                }
            };
            xhr.send();
        }

        // Función para actualizar la lista de mensajes
        function updateMessagesTable() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', './php/actualizarMensajes.php', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const listGroup = document.querySelector('.list-group');
                    listGroup.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }


        // Llamar a la función fetchMessages cada 5 segundos
        setInterval(fetchMessages, 5000);
    </script>
</body>


<script>
    const ENCRYPTION_KEY = CryptoJS.enc.Utf8.parse("k9qyz4PiMsXaDzpdppqH4GgyseKRGEGd");

    function decrypt(data) {
        const rawData = atob(data);
        const rawIv = rawData.slice(0, 16);
        const rawHmac = rawData.slice(16, 48);
        const rawCiphertext = rawData.slice(48);

        const iv = CryptoJS.enc.Latin1.parse(rawIv);
        const hmac = CryptoJS.enc.Latin1.parse(rawHmac);
        const ciphertext = CryptoJS.enc.Latin1.parse(rawCiphertext);

        const decrypted = CryptoJS.AES.decrypt({
            ciphertext: ciphertext
        }, ENCRYPTION_KEY, {
            iv: iv
        });
        const decryptedText = decrypted.toString(CryptoJS.enc.Utf8);

        const calcmac = CryptoJS.HmacSHA256(ciphertext, ENCRYPTION_KEY);

        if (hmac.toString() === calcmac.toString()) {
            return decryptedText;
        } else {
            return false;
        }
    }

    function showMessageDetails(message) {
        const messageModalBody = document.getElementById('messageModalBody');
        const deleteMessageForm = document.getElementById('deleteMessageForm');
        const messageIdToDelete = document.getElementById('messageIdToDelete');
        // Marcar mensaje como leído
        const messageId = message.id_mensaje;
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `./php/eliminarMensaje.php?read=${messageId}`, true);
        xhr.send();

        const decryptedMessage = decrypt(message.mensaje);

        messageModalBody.innerHTML = `
        
    <h6>Nombre del profesor: <b>${message.nombre}</b></h6>

    <h6>ID del remitente: <b>${message.nempleado}</b></h6>
    <h6>Mensaje:</h6>
    <br>
    <textarea style="width: 100%; max-height: 300px;" rows="10" cols="50" readonly>${decryptedMessage}</textarea>`;

        messageIdToDelete.value = message.id_mensaje;
        deleteMessageForm.action = './php/eliminarMensaje.php?read=' + message.id_mensaje;

        $('#messageModal').modal('show');
    }


    $('#messageModal').on('hidden.bs.modal', function(e) {
        updateMessagesTable();
    });

    function prepareReplyMessageForm(nempleado) {
        const replyProfesorId = document.getElementById('replyProfesorId');
        replyProfesorId.value = nempleado;

        // Cierra el modal "messageModal"
        $('#messageModal').modal('hide');

        // Abre el modal "replyMessageModal" después de un pequeño retardo para asegurar que "messageModal" esté cerrado
        setTimeout(function() {
            $('#replyMessageModal').modal('show');
        }, 300);
    }
</script>

<?php
$errorUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
function showAlert($icon, $text, $title)
{
    $timer = 3000;

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
                    window.location.href = './mensajes.php';
                });
            }
            showAlert();
        </script>";
}

if (strpos($errorUrl, "send=success") == true) {
    showAlert('success', 'Mensaje enviado correctamente', 'Mensaje enviado');
}
if (strpos($errorUrl, "send=error") == true) {
    showAlert('error', 'Error al enviar el mensaje', 'Error al enviar');
}
if (strpos($errorUrl, "send=profesorerror") == true) {
    showAlert('error', 'Profesor no encontrado', 'Error al enviar');
}
if (strpos($errorUrl, "send=selfmessageerror") == true) {
    showAlert('error', 'No puedes enviarte un mensaje a ti mismo', 'Error');
}

if (strpos($errorUrl, "delete=approved") == true) {
    showAlert('success', 'Se ha eliminado el mensaje', 'Mensaje eliminado');
}

if (strpos($errorUrl, "delete=error") == true) {
    showAlert('error', 'Error al eliminar el mensaje', 'Error al eliminar');
}

?>

</html>