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

    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" />

    <!-- DataTables JavaScript -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>

        <!-- Tema obscuro-->
        <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>


    <title>Alumnos</title>
    <style>
        .btn-red {
            background-color: #ff7f7f;
        }
        .btn-personalizado-eliminar {
            background-color: #ff7f7f;
            border-color: #ff7f7f;
        }

        .btn-seleccionar:hover {
            background-color: #8B1D35;
            border-color: #8B1D35;
            cursor: pointer;
        }

        .btn-azul {
            background-color: #0096C7;
            border-color: #0096C7;
            cursor: pointer;
        }

        .btn-opciones:hover {
            background-color: #0096C7;
            border-color: #0096C7;
            cursor: pointer;
        }

        .dataTables_wrapper .pagination .page-item .page-link {
            background-color: #8B1D35;
            border-color: #8B1D35;
            color: #fff;
        }

        .dataTables_wrapper .pagination .page-item .page-link:hover {
            background-color: #8B1D35;
            border-color: #8B1D35;
            color: #fff;
        }

        th {
            cursor: pointer;
        }

        .badge-success {
            background-color: #C5E5A4;
            color: #000;
        }

        .badge-warning {
            background-color: #FFEAAE;
            color: #000;
        }

        .badge-orange {
            background-color: #FFA07A;
            color: #000;
        }

        .badge-danger {
            background-color: #ff7f7f;
            color: #000;
        }
        .btn-dark {
    background-color: #8B1D35;
    border-color: #8B1D35;
}       

body {
  margin: 0;
  font-family: "Segoe UI", Benedict;
  font-size: 0.9rem;
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

.table-dark {
  color: #fff;
  background-color: #8B1D35;
}

.dataTables_wrapper .pagination .page-item .page-link {
    background-color: #8B1D35;
    border-color: #8B1D35;
    color: #fff;
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

.btn-dark {
  background-color: #8B1D35;
  border-color: #8B1D35;
}

/* Estilo base para el botón editar */
.btn-editar {
    background-color: #088C4F;
    border-color: #088C4F;
}

/* Estilo para el botón editar al pasar el cursor sobre él */
.btn-editar:hover {
    background-color: #00BF13; /* Cambia el color al pasar el cursor */
    border-color: #00BF13; /* Cambia el color del borde al pasar el cursor */
}

/* Estilo base para el botón eliminar */
.btn-eliminar {
    background-color: #8B1D35;
    border-color: #8B1D35;
}

/* Estilo para el botón eliminar al pasar el cursor sobre él */
.btn-eliminar:hover {
    background-color: #dc3545; /* Cambia el color al pasar el cursor */
    border-color: #dc3545; /* Cambia el color del borde al pasar el cursor */
}

.modal-content {
  position: relative;
  display: -ms-flexbox;
  display: flex;
  -ms-flex-direction: column;
  flex-direction: column;
  width: 100%;
  pointer-events: auto;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid rgba(0,0,0,.2);
  border-radius: .3rem;
  outline: 0;
  color: #000;
}

.btn-opciones.btn-dark:hover {
    background-color: #8B1D35;
    border-color: #8B1D35;
    color: #ffffff; /* Puedes ajustar el color de texto según tus preferencias */
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
                <button id="regresarBtn" class="btn btn-vino mr-2 hover-effect1" onclick="window.location.href='./indexJefe.php'">Regresar</button>
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
                                <a class="dropdown-item hover-effect1" href="./profesoresMenu.php"><i class="bi bi-person-check"></i> Profesores</a>
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
    <div class="container">
        <div class="text-center">
            <div class="text-center">
                <br>
                <h1><i class="bi bi-people-fill" style="color: #088C4F;"></i> <b>Alumnos</b></h1>
                <br>
                <div class="row">
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-striped table_id table-light" id="myDataTable">
                    <thead style="background-color: #8B1D35;">
                            <tr>
                                <th style="color: white;">Nombre</th>
                                <th style="color: white;">Matrícula</th>
                                <th style="color: white;">Carrera</th>
                                <th style="color: white;">Cuatrimestre</th>
                                <th style="color: white;">Grupo</th>
                                <th style="color: white;">Turno</th>
                                <th style="color: white;">Activo</th>
                                <th style="color: white;">Promedio</th>
                                <th style="color: white;">Strikes</th>
                                <th style="color: white;">Email</th>
                                <th style="color: white;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $SQL = "SELECT a.nombre, a.matricula, a.strikes, a.id_carrera, a.cuatrimestre, a.grupo, a.turno, a.active, a.email, a.promedio, c.carreras AS carrera_nombre
FROM alumnos a
INNER JOIN carrera c ON a.id_carrera = c.id_carrera
INNER JOIN profesor_carrera pc ON a.id_carrera = pc.id_carrera
WHERE pc.nempleado = $usuario";

                            $dato = mysqli_query($conn, $SQL);

                            if ($dato->num_rows > 0) {
                                while ($fila = mysqli_fetch_array($dato)) {
                            ?>
                                    <tr>
                                        <td><?php echo $fila['nombre']; ?></td>
                                        <td><?php echo $fila['matricula']; ?></td>
                                        <td><?php echo  $fila['carrera_nombre']; ?></td>
                                        <td><?php echo $fila['cuatrimestre']; ?></td>
                                        <td><?php echo $fila['grupo']; ?></td>
                                        <td><?php echo $fila['turno'] ?></td>
                                        <td><?php echo $fila['active'] == 1 ? 'Activo' : 'Inactivo'; ?></td>
                                        <td><?php echo $fila['promedio']; ?></td>
                                        <td>
                                            <?php
                                            switch ($fila['strikes']) {
                                                case 0:
                                                    echo '<span class="badge badge-success">0</span>';
                                                    break;
                                                case 1:
                                                    echo '<span class="badge badge-warning">1</span>';
                                                    break;
                                                case 2:
                                                    echo '<span class="badge badge-orange">2</span>';
                                                    break;
                                                case 3:
                                                    echo '<span class="badge badge-danger">3</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $fila['email']; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <form action="">
                                                    <button class="btn btn-seleccionar btn-dark btn-editar mr-2" data-id="<?php echo $fila['matricula']; ?>"><i class="bi bi-pencil-fill"></i></button>
                                                </form>
                                                <form action="./php/eliminarAlumno.php" method="GET">
                                                    <button class="btn btn-red text-light btn-personalizado-eliminar" type="submit" name="delete" value="<?php echo $fila['matricula'] ?>"><i class="bi bi-trash-fill"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                }
                            } else {
                                ?>
                                <tr class="text-center ">
                                    <td colspan="16">No existen registros</td>
                                </tr>
                            <?php
                            }
                            ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal crear alumno -->
    <div class="modal fade" id="agregarProfesorModal" tabindex="-1" role="dialog" aria-labelledby="agregarProfesorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="agregarProfesorModalLabel" style="color: #000;">Agregar alumno</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="./php/nuevoAlumno.php" class="text-center">
                        <div class="row">
                            <div class="col-md-6 text-dark-theme">
                                <div class="form-group ">
                                    <label for="nombre" style="color: #000;">Nombre:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="form-group">
                                    <label for="matricula" style="color: #000;">Matrícula:</label>
                                    <input type="number" class="form-control" id="matricula" name="matricula" required>
                                </div>
                                <div class="form-group">
                                    <label for="promedio" style="color: #000;">Promedio:</label>
                                    <input type="number" class="form-control" id="promedio" name="promedio" min="0" max="10" step="0.01" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" style="color: #000;">Correo electrónico:</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="contraseña" style="color: #000;">Contraseña momentanea:</label>
                                    <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                                </div>
                            </div>
                            <div class="col-md-6 text-dark-theme">
                                <div class="form-group">
                                    <label for="grupo" style="color: #000;">Grupo:</label>
                                    <input type="number" class="form-control" id="grupo" name="grupo" min="0" max="1" step="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="turno" style="color: #000;">Turno:</label>
                                    <select class="form-control" id="turno" name="turno" required>
                                        <option value="Matutino" style="color: #000;">Matutino</option>
                                        <option value="Vespertino" style="color: #000;">Vespertino</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_carrera" style="color: #000;">ID de la carrera:</label>
                                    <select id="id_carrera" name="id_carrera" class="form-control" required>
                                        <option value="" disabled selected>Selecciona una carrera</option>
                                        <option value="1">Ingeniería en sistemas computacionales</option>
                                        <option value="2">Ingeniería en robótica</option>
                                        <option value="3">Ingeniería en electrónica y telecomunicaciones</option>
                                        <option value="4">Ingeniería en logística y transporte</option>
                                        <option value="7">Licenciatura en administración y gestión empresarial</option>
                                        <option value="8">Licenciatura en comercio internacional y aduanas</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cuatrimestre" style="color: #000;">Cuatrimestre:</label>
                                    <select id="cuatrimestre" name="cuatrimestre" class="form-control" required>
                                        <option value="" disabled selected>Selecciona un cuatrimestre</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select><br id="br"><br id="br">
                                </div>
                            </div>
                            <input type="hidden" name="strikes" value="0">
                            <input type="hidden" name="active" value="1">
                            <input type="hidden" name="imagen_de_perfil" value="NULL">
                        </div>
                        <div class="text-left" id="materiasContainer" style="display: none;">
                            <label for="materias">Materias:</label>
                            <div id="materias" class="materias"></div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-opciones btn-dark">Registrar Alumno</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar el alumno --->
    <div class="modal fade" id="editProfesorModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfesorModalLabel">Editar alumno</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" class="text-center">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Columna 1 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre">Nombre:</label>
                                        <input type="text" id="nombre2" name="nombre2" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="matricula2">Matrícula:</label>
                                        <input type="text" id="matricula2" name="matricula2" class="form-control" required>
                                        <input type="hidden" id="matricula2_org" name="matricula2_org" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Correo electrónico:</label>
                                        <input type="email" id="email2" name="email2" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="promedio2">Promedio:</label>
                                        <input type="number" id="promedio2" name="promedio2" class="form-control" min="0" max="10" step="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="strikes2">Strikes:</label>
                                        <input type="number" id="strikes2" name="strikes2" class="form-control" min="0" max="3" step="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="activo">Activo:</label>
                                        <select id="activo2" name="activo2" class="form-control" required>
                                            <option value=1>Sí</option>
                                            <option value=0>No</option>
                                        </select>
                                    </div>
                                    <!-- Columna 2 -->
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="activo">Grupo:</label>
                                        <input type="number" id="grupo2" name="grupo2" class="form-control" min="0" max="1" step="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="turno2">Turno:</label>
                                        <select class="form-control" id="turno2" name="turno2" required>
                                            <option value="Matutino">Matutino</option>
                                            <option value="Vespertino">Vespertino</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="id_carrera2">ID de la carrera:</label>
                                        <select id="id_carrera2" name="id_carrera2" class="form-control" required>
                                            <option value="" disabled selected>Selecciona una carrera</option>
                                            <option value="1">Ingeniería en sistemas computacionales</option>
                                            <option value="2">Ingeniería en robótica</option>
                                            <option value="3">Ingeniería en electrónica y telecomunicaciones</option>
                                            <option value="4">Ingeniería en logística y transporte</option>
                                            <option value="7">Licenciatura en administración y gestión empresarial</option>
                                            <option value="8">Licenciatura en comercio internacional y aduanas</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="cuatrimestre2">Cuatrimestre:</label>
                                        <select id="cuatrimestre2" name="cuatrimestre2" class="form-control" required>
                                            <option value="" disabled selected>Selecciona un cuatrimestre</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                        </select><br id="br"><br id="br">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-opciones btn-dark" value="Enviar">Guardar cambios</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Controlador del evento click para el botón de editar
            $('body').on('click', '.btn-editar', function(e) {
                e.preventDefault(); // Evitar la acción predeterminada del botón

                const matricula = $(this).data('id');

                // Obtener información del profesor basada en el nempleado
                // Obtener información del profesor basada en el nempleado
                $.ajax({
                    url: './php/cargarAlumno.php',
                    type: 'POST',
                    data: {
                        matricula: matricula // Cambiar 'alumno' a 'matricula'
                    },
                    success: function(response) {
                        const alumno = JSON.parse(response);
                        // Rellenar el formulario en el modal con la información del profesor
                        $('#editProfesorModal input[name="nombre2"]').val(alumno.nombre);
                        $('#editProfesorModal input[name="promedio2"]').val(alumno.promedio);
                        $('#editProfesorModal input[name="matricula2"]').val(alumno.matricula);
                        $('#editProfesorModal input[name="email2"]').val(alumno.email);
                        $('#editProfesorModal input[name="strikes2"]').val(alumno.strikes);
                        $('#editProfesorModal select[name="activo2"]').val(alumno.active);
                        $('#editProfesorModal input[name="grupo2"]').val(alumno.grupo.slice(-1));
                        $('#editProfesorModal select[name="id_carrera2"]').val(alumno.id_carrera);
                        $('#editProfesorModal select[name="cuatrimestre2"]').val(alumno.cuatrimestre);
                        $('#editProfesorModal input[name="matricula2_org"]').val(alumno.matricula);
                        $('#editProfesorModal input[name="turno2"]').val(alumno.turno);

                        // Mostrar u ocultar los campos de carrera y cuatrimestre según corresponda
                        const id_carrera = $('#editProfesorModal select[name="id_carrera2"]');
                        const cuatrimestre = $('#editProfesorModal select[name="cuatrimestre2"]');
                        const label_carrera = $('label[for="id_carrera2"]');
                        const label_cuatrimestre = $('label[for="cuatrimestre2"]');
                        const br = $('#editProfesorModal #br');

                        // Mostrar el modal
                        $('#editProfesorModal').modal('show');
                    },
                    error: function(error) {
                        console.error('Error al cargar la información del profesor:', error);
                    }
                });

            });


            // Controlador del envío del formulario en el modal
            $('#editForm').submit(function(e) {
                e.preventDefault(); // Evitar el envío predeterminado del formulario

                // Obtener los datos del formulario
                let formData = $(this).serialize();

                // Realizar una solicitud AJAX para actualizar la información del profesor en la base de datos
                $.ajax({
                    url: './php/actualizarAlumno.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.includes("self")) {
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: 'No puedes modificar tu propio número de empleado',
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                $('#editProfesorModal').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Alumno actualizado correctamente',
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                $('#editProfesorModal').modal('hide');
                                location.reload();
                            });
                        }
                    }
                });
            });

        });
    </script>

    <script>
        id_carrera.addEventListener("change", async function() {
            const cuatrimestre = document.getElementById("cuatrimestre").value;
            if (cuatrimestre) {
                await cargarMaterias(id_carrera.value, cuatrimestre, "materias");
            }
        });


        // Añadir el evento "change" a cuatrimestre
        cuatrimestre.addEventListener("change", function() {
            // Vaciar el contenedor de materias al cambiar el cuatrimestre
            const materiasDiv = document.getElementById("materias");

            // Verificar si la carrera está seleccionada
            if (id_carrera.value) {
                cargarMaterias(id_carrera.value, cuatrimestre.value);
            }
        });

        async function cargarMaterias(carrera, cuatrimestre, containerId) {
            try {
                const response = await fetch(
                    `./php/cargarMateriasAlumno.php?carrera=${carrera}&cuatrimestre=${cuatrimestre}`
                );
                const materias = await response.json();

                const materiasContainer = document.getElementById("materiasContainer"); // Corregir la referencia al ID
                materiasContainer.innerHTML = "";
                materiasContainer.style.display = "block";

                materias.forEach((materia) => {
                    const label = document.createElement("label");
                    label.innerText = materia.nombre;
                    label.classList.add("custom-checkbox"); // Agregar la clase personalizada al label
                    label.style.marginLeft = "5px"; // Agregar un espacio a la izquierda del label

                    const checkbox = document.createElement("input");
                    checkbox.type = "checkbox";
                    checkbox.name = "materias[]";
                    checkbox.value = materia.id;

                    materiasContainer.appendChild(checkbox); // Mover el checkbox antes del label
                    materiasContainer.appendChild(label);
                    materiasContainer.appendChild(document.createElement("br"));
                });
            } catch (error) {
                console.error("Error:", error);
            }
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

        $(document).ready(function() {
            $("#btnBuscar").on("click", function() {
                var textoBusqueda = $("#buscador").val();
                sessionStorage.setItem("textoBusqueda", textoBusqueda);
                buscarCita(textoBusqueda, 1); // Envía la página 1 al realizar una nueva búsqueda
            });


            function buscarCita(textoBusqueda, page = 1) {
                $.ajax({
                    type: "POST",
                    url: "./php/buscarAlumnos.php",
                    data: {
                        texto: textoBusqueda,
                        page: page, // Envía la página actual en la función
                    },
                    success: function(response) {
                        $(".table_id").html(response);
                    },
                    error: function() {
                        alert("Error al buscar el alumno.");
                    },
                });
            }

        });

        $(document).ready(function() {
            $('#myDataTable').DataTable({
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"table-responsive"t>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-MX.json"
                },
                "pagingType": "full_numbers",
                "pageLength": 2,
                "lengthMenu": [2, 4, 6, 8, 10],
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

        });
    </script>



    <?php
    $errorUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";



    function showAlert($icon, $text, $title)
    {
        $timer = 10000;

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
                    window.history.back();
                });
            }
            showAlert();
        </script>";
    }

    if (strpos($errorUrl, "new=duplicated") == true) {
        showAlert('error', 'Alumno duplicado con matriícula o email', 'Alumno ya existente');
    }
    if (strpos($errorUrl, "new=approved") == true) {
        showAlert('success', 'Se dio de alta al alumno', 'Alumno dado de alta');
    }
    if (strpos($errorUrl, "new=error") == true) {
        showAlert('error', 'Error al dar de alta al alumno', 'Error al dar de alta');
    }
    if (strpos($errorUrl, "delete=error") == true) {
        showAlert('error', 'Error al eliminar al alumno', 'Error al eliminar el alumno');
    }
    if (strpos($errorUrl, "delete=approved") == true) {
        showAlert('success', 'Se ha eliminado al alumno', 'Alumno eliminado');
    }
    if (strpos($errorUrl, "delete=sameuser") == true) {
        showAlert('error', 'No puedes borrar tu usuario', 'Usuario actual');
    }


    ?>

</html>