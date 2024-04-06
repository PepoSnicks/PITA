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

function obtenerIdCarrera($nempleado)
{
    global $conn;

    $query = "SELECT id_carrera FROM profesor_carrera WHERE nempleado = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $nempleado);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    return $row['id_carrera'];
}


function obtenerProfesores($id_carrera)
{
    global $conn;

    $query = "SELECT p.nempleado, p.nombre
              FROM profesores p
              JOIN profesor_nivel pn ON p.nempleado = pn.nempleado
              JOIN profesor_carrera pc ON p.nempleado = pc.nempleado
              WHERE pn.id_nivel = 2 AND pc.id_carrera = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id_carrera);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $profesores = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $profesores[] = $row;
    }

    return $profesores;
}


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
       /* Estilos para los botones */
       .btn {
            border-radius: 100px; /* Hacer los botones más redondos */
        }

        /* Cambio de color para los botones */
        .btn-secondary,
        .btn-red,
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
            height: 250px;
            width: 250px;
            font-weight: bold;
            border: 6px solid #ffffff !important; /* Ahora el borde es más grueso */
            color: #ffffff !important; /* Cambia el color del texto a beige */
        }

        .btn-custom:hover {
            font-size: 25px;
            padding: 15px 50px;
            margin: 20px;
            height: 250px;
            width: 250px;
            font-weight: bold;
            border: 6px solid #ffffff !important; /* Ahora el borde es más grueso */
            color: #ffffff !important; /* Cambia el color del texto a beige */
        }

        /* Estilos personalizados para los iconos */
        .btn-custom i {
            font-size: 90px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: -10px;
            color: #ffffff !important; /* Cambia el color del texto del icono a beige */
        }

        .btn-custom i:hover {
            font-size: 100px;
            /* Ajuste el tamaño del icono aquí */
            margin-bottom: 10px;
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
  background-color: #8B1D35;      
  color: #fff;
  
}

.btn-personalizado {
    color: white !important; /* Mantener el color de las letras en blanco */
    background-color: #8B1D35; /* Color de fondo original */
    border-color: #8B1D35; /* Color del borde original */
}

.btn-personalizado:hover, 
.btn-personalizado:focus, 
.btn-personalizado:active {
    color: white !important; /* Mantener el color de las letras en blanco */
    background-color: #8B1D35 !important; /* Cambiar el color de fondo cuando se pone el cursor sobre el botón */
    border-color: #8B1D35 !important; /* Cambiar el color del borde cuando se pone el cursor sobre el botón */
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

.btn-danger {
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
                <a class="navbar-brand hover-effect1" href="indexJefe.php"><img src="./css/logo.png" alt="Logo"></a>
                <div class="d-flex align-items-center">
            <!-- Agregado el contenedor para el nuevo logo con mx-auto para centrarlo horizontalmente -->
            <div class="d-flex align-items-center mx-auto">
            <a class="navbar-brand text-center hover-effect1" href="indexJefe.php">
            <img src="./css/NavUPTex.png" alt="Logo" style="width: 200px; height: 50px;">
            </a>
            </div>
                <button id="mensajesBtn" class="btn btn-vino mr-2 hover-effect" onclick="window.location.href='./mensajes.php'">
                    <i class="bi bi-envelope-fill" data-toggle="tooltip" title="Mensajes"></i>
                </button>
                <button id="dark-theme-toggle" class="btn btn-vino mr-2 hover-effect" data-toggle="tooltip" title="Tema obscuro">
                    <i class="bi bi-moon text-white" id="dark-theme-icon"></i>
                </button>
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./profesoresMenu.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./alumnos.php"><i class="bi bi-people"></i> Alumnos</a>
                                <a class="dropdown-item hover-effect1" href="./notas.php"><i class="bi bi-sticky"></i> Notas</a>
                                <a class="dropdown-item hover-effect1" href="./reportesMenu.php"><i class="bi bi-file-earmark-text"></i> Reportes</a>
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
        <div class="row justify-content-center">
            <div class="col-12 col-md-4 col-lg-4 mb-2 mb-md-3">
                <button class="btn btn-dark btn-custom d-flex flex-column justify-content-center align-items-center" data-toggle="modal" data-target="#gruposModal"><i class="bi bi-card-checklist"></i><span>Asignar un grupo</span></button>
            </div>
        </div>
    </div>

    <!-- Modal con formulario para grupos -->
    <div class="modal fade" id="gruposModal" tabindex="-1" role="dialog" aria-labelledby="gruposModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="gruposModalLabel" style="color: #000;">Asignar un grupo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="asignarGruposForm">
                    <div class="modal-body text-dark-theme">
                        <!-- Campos de generación, grupo y periodo de inicio -->
                        <div class="form-group">
                            <label for="grupo" style="color: #000;">Grupo</label>
                            <input type="text" class="form-control" id="grupo" name="grupo" required>
                        </div>
                        <div class="form-group">
                            <label for="generacion" style="color: #000;">Generación</label>
                            <input type="number" class="form-control" id="generacion" name="generacion" required min="2023" max="2050">
                        </div>
                        <div class="form-group">
                            <label for="periodo_inicio" style="color: #000;">Periodo de inicio del grupo</label>
                            <select class="form-control" id="periodo_inicio" name="periodo_inicio" required>
                                <option value="" disabled selected>Selecciona un periodo</option>
                                <option value="1">Enero - Abril</option>
                                <option value="2">Mayo - Agosto</option>
                                <option value="3">Septiembre - Diciembre</option>
                            </select>
                        </div>
                        <input type="hidden" name="existing_tutor_id" id="existing_tutor_id">

                        <div class="form-group">
                            <label for="tutores" style="color: #000;">Tutores</label>
                            <select class="form-control" id="tutores" name="tutores" required>
                                <option value="" disabled selected>Selecciona un tutor</option>
                                <?php
                                $nempleado = $_SESSION['user'];
                                $id_carrera = obtenerIdCarrera($nempleado);
                                $profesores = obtenerProfesores($id_carrera);

                                foreach ($profesores as $profesor) {
                                    echo '<option value="' . $profesor['nempleado'] . '">' . $profesor['nombre'] . '</option>';
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-personalizado" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-personalizado">Asignar grupo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="d-flex justify-content-center">
            <h1><i class="bi bi-calendar-check-fill" style="color: #088C4F"></i> <b>Grupos de profesores</b></h1>
        </div>
        <div class="table-responsive">
            <br>
            <table id="alumnos" class="table table-striped table-light">
            <thead style="background-color: #8B1D35;">
                    <tr>
                        <th style="color: white;">Nombre</th>
                        <th style="color: white;">Grupo</th>
                        <th style="color: white;">Generación</th>
                        <th style="color: white;">Periodo de inicio</th>
                        <th style="color: white;">Acciones
                    </th>
                    </tr>
                </thead>
                <tbody id="tablaProfesores">
                    <!-- Aquí se agregarán las filas de la tabla dinámicamente con JavaScript en base a la búsqueda -->
                    
                </tbody>
            </table>
        </div>
    </div>

</body>
<script>
    document.getElementById('asignarGruposForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        fetch('./php/asignarGrupoTutor.php', {
                method: 'POST',
                body: formData
            })
            .then(function(response) {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Error al asignar el grupo');
                }
            })
            .then(function(data) {
                if (data.success) {
                    Swal.fire({
                        title: 'Se ha asignado el tutor al grupo',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Recargar la página después de que se muestre la alerta.
                    });
                } else if (data.existing_tutor_name) {
                    Swal.fire({
                        title: 'Grupo ya asignado',
                        text: 'Actualmente el profesor: ' + data.existing_tutor_name + ' esta acargo de este grupo, ¿Desea eliminarlo y cambiarlo al profesor actual?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'No, cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Establecer el valor del campo oculto con el ID del tutor existente.
                            document.getElementById('existing_tutor_id').value = data.existing_tutor_id;

                            // Crear una nueva instancia de FormData con los campos necesarios.
                            var reassignFormData = new FormData();
                            reassignFormData.append('tutores', formData.get('tutores'));
                            reassignFormData.append('grupo', formData.get('grupo'));
                            reassignFormData.append('generacion', formData.get('generacion'));
                            reassignFormData.append('periodo_inicio', formData.get('periodo_inicio'));
                            reassignFormData.append('existing_tutor_id', data.existing_tutor_id);


                            // Vuelve a enviar el formulario con el ID del tutor existente.
                            fetch('./php/reassignTutor.php', {
                                    method: 'POST',
                                    body: reassignFormData
                                })
                                .then(function(response) {
                                    if (response.ok) {
                                        return response.json();
                                    } else {
                                        throw new Error('Error al reasignar el tutor');
                                    }
                                })
                                .then(function(data) {
                                    console.log('delete_result:', data.delete_result);
                                    console.log('assign_result:', data.assign_result);

                                    if (data.success) {
                                        Swal.fire({
                                            title: 'Tutor reasignado',
                                            text: data.message,
                                            icon: 'success',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        }).then(() => {
                                            location.reload(); // Recargar la página después de que se muestre la alerta.
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: data.message,
                                            icon: 'error',
                                            timer: 2000,
                                            timerProgressBar: true,
                                            showConfirmButton: false
                                        });
                                    }
                                })
                                .catch(function(error) {
                                    console.error('Error al reasignar el tutor:', error);
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'No se pudo reasignar el tutor.',
                                        icon: 'error',
                                        timer: 2000,
                                        timerProgressBar: true,
                                        showConfirmButton: false
                                    });
                                });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            })
            .catch(function(error) {
                console.error('Error al asignar el grupo:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo asignar el grupo.',
                    icon: 'error',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });
    });


    function borrar(id) {
        fetch('./php/borrarTutorGrupo.php', {
                method: 'POST',
                body: new URLSearchParams({
                    id_tutor_grupo: id
                }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    // Eliminar la fila de la tabla
                    const fila = document.querySelector(`[data-id="${id}"]`);
                    if (fila) fila.remove();

                    // Muestra una SweetAlert para avisar que ha sido eliminado
                    Swal.fire({
                        title: 'Eliminado',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                } else {
                    // Muestra una SweetAlert de error
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                }
            })
            .catch(error => {
                console.error('Error al borrar el registro:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo eliminar el registro.',
                    icon: 'error',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            });
    }





    function inicializarDataTables() {

        // Inicializar DataTables aquí, después de cargar los datos en la tabla
        $('#alumnos').DataTable({
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                '<"table-responsive"t>' +
                '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json"
            },
            "pagingType": "full_numbers",
            "pageLength": 5,
            "lengthMenu": [5, 10, 15, 25, 50, 100],
            "order": [],
            "columnDefs": [{
                "orderable": false,
                "targets": -1
            }],
            "oLanguage": {
                "sSearch": "Buscar:",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "No hay citas canalizadas",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
                "sInfoFiltered": "(filtrado de _MAX_ registros totales)",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sLoadingRecords": "Cargando...",
                "sProcessing": "Procesando...",
                "sPaginate": {
                    "sFirst": '<i class="bi bi-chevron-double-left"></i>',
                    "sLast": '<i class="bi bi-chevron-double-right"></i>',
                    "sNext": '<i class="bi bi-chevron-right"></i>',
                    "sPrevious": '<i class="bi bi-chevron-left"></i>'
                }
            },
            "drawCallback": function(settings) {
                $('ul.pagination').addClass('pagination-sm');
                $('ul.pagination li a').addClass('btn btn-dark btn-personalizado');
                $('ul.pagination li.active a').addClass('active');
            },
            "initComplete": function() {
                $('ul.pagination').addClass('pagination-sm');
                $('ul.pagination li a').addClass('btn btn-dark btn-personalizado');
                $('ul.pagination li.active a').addClass('active');
            }
        });
    }


    document.addEventListener('DOMContentLoaded', function() {
        fetch('./php/obtenerTutorGrupos.php')
            .then(response => response.json())
            .then(data => {
                const tablaProfesores = document.getElementById('tablaProfesores');

                data.forEach(row => {
                    const tr = document.createElement('tr');
                    tr.dataset.id = row.id_tutor_grupo;

                    tr.innerHTML = `
                    <td>${row.nombre}</td>
                    <td>${row.grupo}</td>
                    <td>${row.generacion}</td>
                    <td>${row.periodo_nombre}</td>
                    <td>
    <button class="btn btn-danger btn-personalizado-eliminar" onclick="borrar(${row.id_tutor_grupo})" style="background-color: #8B1D35 !important;">
        <i class='bi bi-trash-fill' style="color: #FFFFFF;"></i>
    </button>
</td>

                `;

                    tablaProfesores.appendChild(tr);
                });

                // Llama a la función para inicializar DataTables después de agregar las filas
                inicializarDataTables();

            })
            .catch(error => {
                console.error('Error al obtener los datos de tutor_grupos:', error);
            });
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
</script>

</html>