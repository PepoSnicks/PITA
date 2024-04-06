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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




?>

<!DOCTYPE html>
<html lang="en">



<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Luego Popper.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

    <!-- Y finalmente Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.6.1/font/bootstrap-icons.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" />

    <!-- DataTables JavaScript -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>


    <title>Profesores</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

    <!-- Tema obscuro-->
    <link id="dark-theme" rel="stylesheet" type="text/css" href="./css/dark-theme.css" disabled>
    <script src="./css/darktheme.js" defer></script>


    <style>
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
    <div class="container">
        <div class="text-center">
            <br>
            <h1 style="color: #ffffff;"><i class="bi bi-people-fill" style="color: #088C4F;"></i> <b>Profesores</b></h1>
            <br>
            <div class="row">
            <div class="col-12 text-center">
                <button class="btn btn-opciones rounded-pill btn-dark" id="btnAgregarProfesor" data-toggle="modal" data-target="#agregarProfesorModal" style="background-color: #8B1D35;">Agregar profesor</button>
            </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table_id table-light" id="myDataTable">
                <thead style="background-color: #8B1D35;">
                <tr>
                <th style="color: white;">Nombre</th>
                <th style="color: white;">N.º Profesor</th>
                <th style="color: white;">Nivel de acceso</th>
                <th style="color: white;">Email</th>
                <th style="color: white;">Activo</th>
                <th style="color: white;">Carrera(s) a su cargo</th>
                <th style="color: white;">Nivel(es)</th>
                <th style="color: white;">Acciones</th>
            </tr>
        </thead>
                    <tbody>
                        <?php

                        // Función para mapear el valor de id_nivel al nombre de nivel correspondiente
                        function nivelNombre($id_nivel)
                        {
                            switch ($id_nivel) {
                                case 1:
                                    return "Jefe de carrera";
                                    break;
                                case 2:
                                    return "Tutor";
                                    break;
                                case 3:
                                    return "Psicólogo";
                                    break;
                                case 4:
                                    return "Docente";
                                    break;
                                default:
                                    return "";
                                    break;
                            }
                        }

                        function carreraNombre($id_carrera)
                        {
                            switch ($id_carrera) {
                                case 1:
                                    return "Ingeniería en sistemas computacionales";
                                    break;
                                case 2:
                                    return "Ingeniería en robótica";
                                    break;
                                case 3:
                                    return "Ingeniería en electrónica y telecomunicaciones";
                                    break;
                                case 4:
                                    return "Ingeniería en logística y transporte";
                                    break;
                                case 5:
                                    return "Jefe de carrera";
                                    break;
                                case 6:
                                    return "psicologo";
                                    break;
                                case 7:
                                    return "Licenciatura en administración y gestión empresarial";
                                    break;
                                case 8:
                                    return "Licenciatura en comercio internacional y aduanas";
                                    break;
                                default:
                                    return "";
                                    break;
                            }
                        }


                        // Consulta para extraer datos de profesores, carreras y niveles
                        $sql = "SELECT p.nombre, p.nempleado, p.active, p.email, p.id_nivel,
                        GROUP_CONCAT(DISTINCT pc.id_carrera SEPARATOR ', ') AS carreras,
                        GROUP_CONCAT(DISTINCT pn.id_nivel SEPARATOR ', ') AS niveles,
                        GROUP_CONCAT(DISTINCT pc2.nempleado SEPARATOR ', ') AS profesores_carrera
                        FROM profesores p
                        LEFT JOIN profesor_carrera pc ON p.nempleado = pc.nempleado
                        LEFT JOIN profesor_nivel pn ON p.nempleado = pn.nempleado
                        LEFT JOIN profesor_carrera pc2 ON pc.id_carrera = pc2.id_carrera AND pc2.nempleado != p.nempleado
                        WHERE p.nempleado != $usuario AND pn.id_nivel != 1 
                              AND p.nempleado IN (
                                SELECT nempleado FROM profesor_carrera WHERE id_carrera IN (
                                  SELECT id_carrera FROM profesor_carrera WHERE nempleado = $usuario
                                )
                              )
                              GROUP BY p.nempleado";


                        $result = $conn->query($sql);

                        // Verificar si hay resultados
                        if ($result->num_rows > 0) {
                            // Mostrar los datos de cada fila en una fila de la tabla
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["nombre"] . "</td>";
                                echo "<td>" . $row["nempleado"] . "</td>";
                                echo "<td>" . nivelNombre($row["id_nivel"]) . "</td>"; // Muestra el nombre del nivel de acceso según el valor de id_nivel
                                echo "<td>" . $row["email"] . "</td>";
                                echo "<td>" . ($row["active"] == 1 ? "Sí" : "No") . "</td>"; // Muestra "Si" cuando active es 1
                                echo "<td>" . ($row['carreras'] ? nl2br(implode("\n", array_map('carreraNombre', explode(', ', $row['carreras'])))) : " ") . "</td>";
                                echo "<td>" . ($row["niveles"] ? nl2br(implode("\n", array_map('nivelNombre', explode(', ', $row['niveles'])))) : " ") . "</td>";
                                echo "<td><div class='btn-group'>";
                                echo "<button class='btn btn-dark btn-opciones btn-editar mr-2' data-toggle='modal' data-target='#editarModal' data-nempleado='" . $row["nempleado"] . "'><i class='bi bi-pencil-fill'></i></button>";
                                echo "<button class='btn btn-danger btn-opciones btn-eliminar' data-nempleado='" . $row["nempleado"] . "'><i class='bi bi-trash-fill'></i></button>";
                                echo "</div></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No se encontraron resultados de profesores</td></tr>";
                        }

                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php

    ?>

    <!-- Modal editar -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModallLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="editModalLabel" style="color: #000;">Editar profesor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-dark-theme">
                    <form id="editar-profesor-form" method="post">
                        <input type="hidden" name="nempleado" id="nempleado-input">
                        <div class="form-group">
                            <label for="nombre-input" style="color: #000;">Nombre: </label>
                            <input type="text" class="form-control" id="nombre-input" name="nombre">
                        </div>
                        <div class="form-group">
                            <label for="email-input" style="color: #000;">Email: </label>
                            <input type="email" class="form-control" id="email-input" name="email">
                        </div>
                        <div class="form-group">
                            <label for="email-input" style="color: #000;">Activo: </label>
                            <select class="form-control" id="activo-input" name="activo-input">
                                <option value="1">Sí</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_nivel-input" style="color: #000;">Nivel de acceso: </label>
                            <select class="form-control" id="id_nivel-input" name="id_nivel-input">
                                <option value="2">Tutor</option>
                                <option value="3">Psicólogo</option>
                                <option value="4">Docente</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nivel-input" style="color: #000;">Niveles: </label>
                            <br>
                            <input type="checkbox" id="tutor" name="nivel[]" value="2">
                            <label for="tutor" style="color: #000;"> Tutor</label>
                            <br>
                            <input type="checkbox" id="docente" name="nivel[]" value="4">
                            <label for="docente" style="color: #000;"> Docente</label>
                            <br>
                            <input type="checkbox" id="psicologo" name="nivel[]" value="3">
                            <label for="psicologo" style="color: #000;"> Psicólogo</label>

                        </div>
                        <div class="form-group">
                            <label for="carreras-input" style="color: #000;">Carreras: </label>
                            <br>
                            <?php
                            // Consultar las carreras asignadas al profesor actual
                            $sql = "SELECT carrera.id_carrera, carrera.carreras
FROM profesor_carrera
JOIN carrera ON profesor_carrera.id_carrera = carrera.id_carrera
WHERE profesor_carrera.nempleado = $usuario";

                            $result_carreras_editar = $conn->query($sql);

                            // Generar los elementos HTML de las carreras
                            if ($result_carreras_editar->num_rows > 0) {
                                while ($row = $result_carreras_editar->fetch_assoc()) {
                                    echo '<input type="checkbox" id="carrera-' . $row['id_carrera'] . '" name="carreras[]" value="' . $row['id_carrera'] . '">';
                                    echo '<label for="carrera-' . $row['id_carrera'] . '">' . $row['carreras'] . '</label><br>';
                                }
                            } else {
                                echo "No se encontraron carreras asignadas";
                            }
                            ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="editar-profesor-form" class="btn btn-dark btn-opciones">Guardar cambios</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal crear profesor -->
    <div class="modal fade" id="agregarProfesorModal" tabindex="-1" role="dialog" aria-labelledby="agregarProfesorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark-theme" id="agregarProfesorModalLabel" style="color: #000;">Agregar profesor</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-dark-theme">
                    <form method="POST" action="./php/nuevoProfesor.php" class="text-center">
                        <div class="row">
                            <div class="col-md-12 nivel-container text-center">
                                <div class="form-group">
                                    <label for="nivel_acceso" style="color: #000;">Tipo de profesor:</label>
                                    <br>
                                    <input type="checkbox" id="tutor" name="nivel_acceso[]" value="2">
                                    <label for="tutor" style="color: #000;">Tutor</label>
                                    <br>
                                    <input type="checkbox" id="profesor" name="nivel_acceso[]" value="4">
                                    <label for="profesor" style="color: #000;">Profesor</label>
                                    <br>
                                    <input type="checkbox" id="psicologo" name="nivel_acceso[]" value="3">
                                    <label for="psicologo" style="color: #000;">Psicólogo</label>
                                    <br>
                                </div>
                            </div>
                            <div class="col-md-12 other-fields-container" style="display: none;">
                                <div class="form-group">
                                    <label for="carreras-input" style="color: #000;">Carreras: </label>
                                    <br>
                                    <?php
                                    // Consultar las carreras asignadas al profesor actual
                                    $sql = "SELECT carrera.id_carrera, carrera.carreras
FROM profesor_carrera
JOIN carrera ON profesor_carrera.id_carrera = carrera.id_carrera
WHERE profesor_carrera.nempleado = $usuario";

                                    $result_carreras_agregar = $conn->query($sql);

                                    // Generar los elementos HTML de las carreras
                                    if ($result_carreras_agregar->num_rows > 0) {
                                        while ($row = $result_carreras_agregar->fetch_assoc()) {
                                            echo '<input type="checkbox" id="carrera-' . $row['id_carrera'] . '" name="carreras[]" value="' . $row['id_carrera'] . '">';
                                            echo '<label for="carrera-' . $row['id_carrera'] . '">' . $row['carreras'] . '</label><br>';
                                        }
                                    } else {
                                        echo "No se encontraron carreras asignadas";
                                    }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label for="nombre" style="color: #000;">Nombre:</label>
                                    <input class="form-control" type="text" id="nombre" name="nombre" required>
                                </div>
                                <div class="form-group">
                                    <label for="num_empleado" style="color: #000;">Número de empleado:</label>
                                    <input class="form-control" type="number" id="num_empleado" name="num_empleado" required>
                                </div>
                                <div class="form-group">
                                    <label for="email" style="color: #000;">Correo electrónico:</label>
                                    <input class="form-control" type="email" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="contraseña" style="color: #000;">Contraseña:</label>
                                    <input class="form-control" type="password" id="contraseña_profesor" name="contraseña" required>
                                    <div class="progress mt-2">
                                        <div id="passwordStrengthBarProfesor" class="progress-bar"></div>
                                    </div>
                                    <span id="passwordStrengthMessageProfesor" class="mt-1 d-block"></span>
                                </div>

                            </div>
                        </div>
                        <div class="text-left" id="materiasContainer" style="display: none;">
                            <label for="materias" style="color: #000;">Materias:</label>
                            <div id="materias" class="materias"></div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-dark btn-opciones" value="Enviar">Agregar profesor</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('contraseña_profesor').addEventListener('input', function() {
            const passwordStrengthBar = document.getElementById('passwordStrengthBarProfesor');
            const passwordStrengthMessage = document.getElementById('passwordStrengthMessageProfesor');
            const passwordStrength = isPasswordSecure(this.value);

            let barColor = '';
            let strengthText = '';

            switch (passwordStrength) {
                case 0:
                case 1:
                    barColor = 'bg-danger';
                    strengthText = 'Muy débil';
                    break;
                case 2:
                    barColor = 'bg-warning';
                    strengthText = 'Débil';
                    break;
                case 3:
                    barColor = 'bg-info';
                    strengthText = 'Buena';
                    break;
                case 4:
                    barColor = 'bg-success';
                    strengthText = 'Fuerte';
                    break;
            }

            passwordStrengthBar.className = `progress-bar ${barColor}`;
            passwordStrengthBar.style.width = `${passwordStrength * 25}%`;
            passwordStrengthMessage.textContent = strengthText;
        });

        function isPasswordSecure(password) {
            let strength = 0;
            const regexRules = [
                /[a-z]/,
                /[A-Z]/,
                /[0-9]/,
                /[^A-Za-z0-9]/,
            ];

            for (const regex of regexRules) {
                if (regex.test(password)) strength++;
            }

            return strength;
        }


        $(document).ready(function() {
            $("#btnAgregarProfesor").click(function() {
                $('#agregarProfesorModal').modal('show');
            });
        });

        $(document).ready(function() {
            function updateVisibility() {
                if ($('#profesor, #psicologo, #tutor').is(':checked')) {
                    $('.other-fields-container').show();
                } else {
                    $('.other-fields-container').hide();
                }
            }

            $('#profesor, #psicologo, #tutor').change(function() {
                updateVisibility();
            });

            // Inicializa la visibilidad al cargar la página
            updateVisibility();
        });

        $("#agregarProfesorModal form").submit(function(e) {
            e.preventDefault();
            submitProfesorForm();
        });

        async function submitProfesorForm() {
            const passwordStrength = isPasswordSecure(document.getElementById('contraseña_profesor').value);
            if (passwordStrength < 2) {
                Swal.fire({
                    title: 'Contraseña muy débil',
                    text: 'Por favor, elige una contraseña más segura',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false,
                    allowOutsideClick: true
                });
                return;
            } else {

                const formData = new FormData(document.querySelector("#agregarProfesorModal form"));
                const response = await fetch('./php/nuevoProfesor.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Profesor agregado',
                        text: 'El profesor ha sido agregado correctamente',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: true
                    }).then(() => {
                        $('#agregarProfesorModal').modal('hide');
                        location.reload();
                    });
                } else {
                    let message = '';

                    switch (data.message) {
                        case 'duplicated':
                            message = 'El profesor ya existe, por favor revisa el número de empleado o correo electrónico';
                            break;
                        case 'weak_password':
                            message = 'La contraseña es insegura, revisa que tu contraseña cumpla lo el mínimo nivel que es "débil"';
                            break;
                        case 'error':
                        default:
                            message = 'Error al registrar el profesor';
                            break;
                    }

                    Swal.fire({
                        title: 'Error',
                        text: message,
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false,
                        allowOutsideClick: true
                    });
                }
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


        $(document).ready(function() {
            $('.btn-editar').click(function() {
                var nempleado = $(this).data('nempleado');
                $.ajax({
                    url: './php/obtenerProfesorEditar.php',
                    method: 'POST',
                    data: {
                        nempleado: nempleado
                    },
                    dataType: 'json',
                    success: function(data) {

                        // Establecer niveles
                        if (data.niveles) {
                            var niveles = data.niveles.split(',');
                            $('input[name="nivel[]"]').each(function() {
                                if (niveles.includes($(this).val())) {
                                    $(this).prop('checked', true);
                                } else {
                                    $(this).prop('checked', false);
                                }
                            });
                        }

                        // Establecer carreras
                        if (data.carreras) {
                            var carreras = data.carreras.split(',');
                            $('input[name="carreras[]"]').each(function() {
                                if (carreras.includes($(this).val())) {
                                    $(this).prop('checked', true);
                                } else {
                                    $(this).prop('checked', false);
                                }
                            });
                        }

                        $('#editModal').modal('show');
                        $('#nempleado-input').val(data.nempleado);
                        $('#nombre-input').val(data.nombre);
                        $('#activo-input').val(data.active);
                        $('#id_nivel-input').val(data.id_nivel);
                        $('#email-input').val(data.email);
                        $('#activo').val(data.activo);
                        $('#editModal').modal('show');
                    }
                });
            });

            $('#editar-profesor-form').submit(function(event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: './php/actualizarProfesor.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response === 'success') {
                            $('#editModal').modal('hide');
                            Swal.fire({
                                title: 'Profesor actualizado',
                                text: 'El docente ha sido actualizado correctamente',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error al actualizar',
                                text: 'No se pudo actualizar el profesor, intentalo más tarde',
                                icon: 'error',
                                showCancelButton: true,
                                showConfirmButton: false,
                                buttonsStyling: false,
                                reverseButtons: true
                            });
                        }
                    }
                });
            });

        });

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
                    url: "./php/buscarProfesores.php",
                    data: {
                        texto: textoBusqueda,
                        page: page, // Envía la página actual en la función
                    },
                    success: function(response) {
                        $(".table_id").html(response);
                    },
                    error: function() {
                        alert("Error al buscar el profesor.");
                    },
                });
            }

        });
        $('.btn-eliminar').click(function() {
            var nempleado = $(this).data('nempleado');
            Swal.fire({
                title: '¿Está seguro de que desea eliminar a este docente?',
                showCancelButton: true,
                confirmButtonText: 'Sí',
                cancelButtonText: 'Cancelar',
                icon: 'warning',
                customClass: {
                    confirmButton: 'btn btn-dark btn-opciones',
                    cancelButton: 'btn btn-danger mr-2'
                },
                buttonsStyling: false,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: './php/eliminarProfesor.php',
                        method: 'POST',
                        data: {
                            nempleado: nempleado
                        },
                        success: function(response) {
                            var jsonResponse = JSON.parse(response);
                            if (jsonResponse.status === 'success') {
                                Swal.fire({
                                    title: 'Profesor eliminado',
                                    text: 'El docente ha sido eliminado correctamente',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: jsonResponse.message,
                                    icon: 'error',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                            }
                        }
                    });
                }
            });
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
                "pageLength": 3,
                "lengthMenu": [3, 4, 5],
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
        showAlert('error', 'Profesor duplicado con número de empleado o email', 'Profesor ya existente');
    }
    if (strpos($errorUrl, "new=approved") == true) {
        showAlert('success', 'Se dio de alta al profesor', 'Profesor dado de alta');
    }
    if (strpos($errorUrl, "new=error") == true) {
        showAlert('error', 'Error al dar de alta al profesor', 'Error al dar de alta');
    }
    if (strpos($errorUrl, "delete=error") == true) {
        showAlert('error', 'Error al eliminar al profesor', 'Error al eliminar el profesor');
    }
    if (strpos($errorUrl, "delete=approved") == true) {
        showAlert('success', 'Se ha eliminado al profesor', 'Profesor eliminado');
    }
    if (strpos($errorUrl, "delete=sameuser") == true) {
        showAlert('error', 'No puedes borrar tu usuario', 'Usuario actual');
    }
    if (strpos($errorUrl, "delete=sameuser") == true) {
        showAlert('error', 'No puedes borrar tu usuario', 'Usuario actual');
    }



    ?>

</html>