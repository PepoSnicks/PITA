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

$por_pagina = 5; // Aquí puedes cambiar el número de registros por página

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

    <title>Horario</title>
    <link rel="stylesheet" type="text/css" href="./css/navbarStyle.css">
    <link rel="icon" type="image/png" href="./css/icon.png">

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

.color-box {
            width: 20px;
            height: 20px;
            margin-right: 5px;
            border: 1px solid black;
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
    <div class="col-12 text-center">
        <br>
        <h1 class="text-center" style="color: #ffffff;"><i class="bi bi-calendar3" style="color: #088C4F;"></i> <b>Horario de profesores</b></h1>
    </div>
    <br>
    <br>

    <div class="container">
        <div class="row ">
            <div class="col-lg-12">
                <div class="container">
                    <div class="col-12 text-center">
                        <br>
                        <div class="container text-center">
                            <h4><strong> <i class="bi bi-search" style="color: #088C4F;"></i> Busca un profesor por su nombre o número de profesor</strong></h4>
                            <br>
                            <div class="row justify-content-center">
                                <div class="col-8">
                                    <div class="row ">
                                        <!-- Columna izquierda -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="nombreProfesor"><strong> <i class="bi bi-search" style="color: #088C4F;"></i> Nombre del profesor:</strong></label>
                                                <input type="text" class="form-control" name="nombreProfesor" id="nombreProfesor" oninput="buscarProfesorPorNombre(this.value);" list="resultado_busqueda" onblur="actualizarCampos(this);" onkeydown="handleEnterKey(event, this);">
                                                <datalist id="resultado_busqueda"></datalist>
                                            </div>
                                            <div class="form-group">
                                                <label for="nempleado"><strong> <i class="bi bi-search" style="color: #088C4F;"></i> Número del profesor:</strong></label>
                                                <input type="number" class="form-control" name="nempleado" id="nempleado" oninput="document.getElementById('nempleado_hidden').value = this.value;" required onkeydown="handleEnterKey(event, this);">
                                            </div>
                                        </div>
                                        <!-- Columna derecha -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="profesorNombre"><strong>Nombre del profesor que se busco:</strong></label>
                                                <br>
                                                <span id="profesorNombre"></span> <!-- Utilizamos un span en lugar de un div para mostrar el nombre del profesor -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-light">
                                        <thead style="background-color: #8B1D35;">
                                                <tr class="text-center">
                                                    <th style="color: white;">Hora</th>
                                                    <th style="color: white;">Lunes</th>
                                                    <th style="color: white;">Martes</th>
                                                    <th style="color: white;">Miércoles</th>
                                                    <th style="color: white;">Jueves</th>
                                                    <th style="color: white;">Viernes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">07:00 - 08:00</th>
                                                    <td id="dia1_07"></td>
                                                    <td id="dia2_07"></td>
                                                    <td id="dia3_07"></td>
                                                    <td id="dia4_07"></td>
                                                    <td id="dia5_07"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">08:00 - 09:00</th>
                                                    <td id="dia1_08"></td>
                                                    <td id="dia2_08"></td>
                                                    <td id="dia3_08"></td>
                                                    <td id="dia4_08"></td>
                                                    <td id="dia5_08"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">09:00 - 10:00</th>
                                                    <td id="dia1_09"></td>
                                                    <td id="dia2_09"></td>
                                                    <td id="dia3_09"></td>
                                                    <td id="dia4_09"></td>
                                                    <td id="dia5_09"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">10:00 - 11:00</th>
                                                    <td id="dia1_10"></td>
                                                    <td id="dia2_10"></td>
                                                    <td id="dia3_10"></td>
                                                    <td id="dia4_10"></td>
                                                    <td id="dia5_10"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">11:00 - 12:00</th>
                                                    <td id="dia1_11"></td>
                                                    <td id="dia2_11"></td>
                                                    <td id="dia3_11"></td>
                                                    <td id="dia4_11"></td>
                                                    <td id="dia5_11"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">12:00 - 13:00</th>
                                                    <td id="dia1_12"></td>
                                                    <td id="dia2_12"></td>
                                                    <td id="dia3_12"></td>
                                                    <td id="dia4_12"></td>
                                                    <td id="dia5_12"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">13:00 - 14:00</th>
                                                    <td id="dia1_13"></td>
                                                    <td id="dia2_13"></td>
                                                    <td id="dia3_13"></td>
                                                    <td id="dia4_13"></td>
                                                    <td id="dia5_13"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">14:00 - 15:00</th>
                                                    <td id="dia1_14"></td>
                                                    <td id="dia2_14"></td>
                                                    <td id="dia3_14"></td>
                                                    <td id="dia4_14"></td>
                                                    <td id="dia5_14"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">15:00 - 16:00</th>
                                                    <td id="dia1_15"></td>
                                                    <td id="dia2_15"></td>
                                                    <td id="dia3_15"></td>
                                                    <td id="dia4_15"></td>
                                                    <td id="dia5_15"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">16:00 - 17:00</th>
                                                    <td id="dia1_16"></td>
                                                    <td id="dia2_16"></td>
                                                    <td id="dia3_16"></td>
                                                    <td id="dia4_16"></td>
                                                    <td id="dia5_16"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">17:00 - 18:00</th>
                                                    <td id="dia1_17"></td>
                                                    <td id="dia2_17"></td>
                                                    <td id="dia3_17"></td>
                                                    <td id="dia4_17"></td>
                                                    <td id="dia5_17"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">18:00 - 19:00</th>
                                                    <td id="dia1_18"></td>
                                                    <td id="dia2_18"></td>
                                                    <td id="dia3_18"></td>
                                                    <td id="dia4_18"></td>
                                                    <td id="dia5_18"></td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">19:00 - 20:00</th>
                                                    <td id="dia1_19"></td>
                                                    <td id="dia2_19"></td>
                                                    <td id="dia3_19"></td>
                                                    <td id="dia4_19"></td>
                                                    <td id="dia5_19"></td>
                                                </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h5><strong>Tipos de horario</strong></h5>
                        <div class="d-inline-flex">
                            <div class="color-box" style="background-color: #98BBF5;"></div> <span>Tutorías</span>
                        </div>
                        <div class="d-inline-flex">
                            <div class="color-box" style="background-color: #F7CAC9;"></div> <span>Psicológicas</span>
                        </div>
                        <div class="d-inline-flex">
                            <div class="color-box" style="background-color: #B2DFDB;"></div> <span>Académicas</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row justify-content-left">
                    <div class="col-12">
                        <div class="col-12 text-center">
                            <br>
                            <br>
                            <br>
                            <h3><i class="bi bi-calendar3" style="color: #088C4F;"></i> <b>Asignar horario a un profesores</b></h3>
                            <br>
                        </div>
                        <form method="post" id="horario-form">
                            <div class="row justify-content-center">
                                <h4><strong> <i class="bi bi-search" style="color: #088C4F;"></i> Busca un profesor por su nombre o número de profesor</strong></h4>
                                <br>
                                <br>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="nombreProfesor"><strong> <i class="bi bi-search" style="color: #088C4F;"></i> Nombre del profesor:</strong></label>
                                        <input type="text" class="form-control" name="nombreProfesor" id="nombreProfesor" list="resultado_busqueda" oninput="buscarProfesorPorNombre(this.value);" required>
                                        <datalist id="resultado_busqueda"></datalist>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="nempleado"><strong> <i class="bi bi-search" style="color: #088C4F;"></i> Número del profesor:</strong></label>
                                        <input type="number" class="form-control" name="nempleado_hidden" id="nempleado_hidden" list="resultado_busqueda_numero" oninput="buscarProfesorPorNumero(this.value);" required>
                                        <datalist id="resultado_busqueda_numero"></datalist>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-light">
                                <thead style="background-color: #8B1D35;">
                                        <tr class="text-center">
                                            <th style="color: white;">Lunes</th>
                                            <th style="color: white;">Martes</th>
                                            <th style="color: white;">Miércoles</th>
                                            <th style="color: white;">Jueves</th>
                                            <th style="color: white;">Viernes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <p class="text-center">Horario día lunes</p>
                                                <div class="text-center" id="lunes_bloques">
                                                    <!-- Aquí se agregarán los bloques de horas -->
                                                </div>
                                                <br>
                                                <div class="text-center">

                                                    <button class="btn btn-dark btn-opciones btn-sm" type="button" onclick="agregarBloque('lunes')"><i class="bi bi-plus-lg"></i></button>
                                                </div>

                                            </td>
                                            <td>
                                                <p class="text-center">Horario día martes</p>
                                                <div class="text-center" id="martes_bloques">
                                                    <!-- Aquí se agregarán los bloques de horas -->
                                                </div>
                                                <br>
                                                <div class="text-center">

                                                    <button class="btn btn-dark btn-opciones btn-sm" type="button" onclick="agregarBloque('martes')"><i class="bi bi-plus-lg"></i></button>
                                                </div>

                                            </td>
                                            <td>
                                                <p class="text-center">Horario día miércoles</p>
                                                <div class="text-center" id="miercoles_bloques">
                                                    <!-- Aquí se agregarán los bloques de horas -->
                                                </div>
                                                <br>
                                                <div class="text-center">

                                                    <button class="btn btn-dark btn-opciones btn-sm" type="button" onclick="agregarBloque('miercoles')"><i class="bi bi-plus-lg"></i></button>
                                                </div>

                                            </td>
                                            <td>
                                                <p class="text-center">Horario día jueves</p>
                                                <div class="text-center" id="jueves_bloques">
                                                    <!-- Aquí se agregarán los bloques de horas -->
                                                </div>
                                                <br>
                                                <div class="text-center">
                                                    <button class="btn btn-dark btn-opciones btn-sm" type="button" onclick="agregarBloque('jueves')"><i class="bi bi-plus-lg"></i></button>
                                                </div>

                                            </td>
                                            <td>
                                                <p class="text-center">Horario día viernes</p>
                                                <div class="text-center" id="viernes_bloques">
                                                    <!-- Aquí se agregarán los bloques de horas -->
                                                </div>
                                                <br>
                                                <div class="text-center">
                                                    <button class="btn btn-dark btn-opciones btn-sm" type="button" onclick="agregarBloque('viernes')"><i class="bi bi-plus-lg"></i></button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="text-center">
                                <button type="button" class="btn btn-dark btn-opciones mt-sm-0 mt-4" style="background-color: #8B1D35;" onclick="submitFormWithConfirmation();">Asignar horario</button>
                            </div>
                            <br>
                            <br>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    agregarBloque('lunes');
                    agregarBloque('martes');
                    agregarBloque('miercoles');
                    agregarBloque('jueves');
                    agregarBloque('viernes');
                });

                function agregarBloque(dia) {
                    var diaMap = {
                        'lunes': 1,
                        'martes': 2,
                        'miercoles': 3,
                        'jueves': 4,
                        'viernes': 5
                    };

                    var diaNumero = diaMap[dia];

                    var bloques = document.getElementById(dia + '_bloques');

                    var bloque = document.createElement('div');
                    bloque.classList.add('bloque-horas');

                    var eliminarBloqueBoton = document.createElement('button');
                    eliminarBloqueBoton.classList.add('btn', 'btn-danger', 'btn-sm', 'ml-2');
                    eliminarBloqueBoton.innerHTML = '<i class="bi bi-x-lg"></i>';
                    eliminarBloqueBoton.onclick = function() {
                        eliminarBloque(bloque);
                    }

                    // Agregar un salto de línea antes del botón de eliminar
                    var saltoDeLinea = document.createElement('br');
                    bloque.appendChild(saltoDeLinea);

                    bloque.appendChild(eliminarBloqueBoton);

                    var inicio = document.createElement('select');
                    inicio.name = 'dia' + diaNumero + '_inicio[]';
                    inicio.onchange = function() {
                        inicio.classList.add('select-bloque');
                        inicioHidden.value = this.value;
                    }
                    inicio.innerHTML = `
    <option value="07:00">07:00</option>
    <option value="08:00">08:00</option>
    <option value="09:00">09:00</option>
    <option value="10:00">10:00</option>
    <option value="11:00">11:00</option>
    <option value="12:00">12:00</option>
    <option value="13:00">13:00</option>
    <option value="14:00">14:00</option>
    <option value="15:00">15:00</option>
    <option value="16:00">16:00</option>
    <option value="17:00">17:00</option>
    <option value="18:00">18:00</option>
    <option value="19:00">19:00</option>
    <option value="20:00">20:00</option>
  `;

                    var a = document.createElement('span');
                    a.innerHTML = ' a ';

                    var fin = document.createElement('select');
                    fin.name = 'dia' + diaNumero + '_fin[]';
                    fin.onchange = function() {
                        fin.classList.add('select-bloque');
                        finHidden.value = this.value;
                    }
                    fin.innerHTML = `
    <option value="07:00">07:00</option>
    <option value="08:00">08:00</option>
    <option value="09:00">09:00</option>
    <option value="10:00">10:00</option>
    <option value="11:00">11:00</option>
    <option value="12:00">12:00</option>
    <option value="13:00">13:00</option>
    <option value="14:00">14:00</option>
    <option value="15:00">15:00</option>
    <option value="16:00">16:00</option>
    <option value="17:00">17:00</option>
    <option value="18:00">18:00</option>
    <option value="19:00">19:00</option>
    <option value="20:00">20:00</option>
  `;

                    var inicioHidden = document.createElement('input');
                    inicioHidden.type = 'hidden';
                    inicioHidden.name = 'dia' + diaNumero + '_inicio_hidden[]';

                    var finHidden = document.createElement('input');
                    finHidden.type = 'hidden';
                    finHidden.name = 'dia' + diaNumero + '_fin_hidden[]';

                    var tipo = document.createElement('select');
                    tipo.name = 'dia' + diaNumero + '_tipo[]';
                    tipo.classList.add('select-bloque');
                    tipo.innerHTML = `
    <option value="" disabled selected>Tipo de hora</option>
    <option value="1">Tutorías</option>
    <option value="2">Psicológicas</option>
    <option value="4">Académicas</option>
  `;

                    bloque.appendChild(inicio);
                    bloque.appendChild(a); // Agrega el elemento 'a' entre inicio y fin

                    bloque.appendChild(fin);
                    bloque.appendChild(tipo);
                    bloque.appendChild(inicioHidden);
                    bloque.appendChild(finHidden);

                    bloques.appendChild(bloque);
                }

                function eliminarBloque(bloque) {
                    bloque.remove();
                }


                // Agrega esta función para manejar el evento de la tecla "Enter"
                function handleEnterKey(event, inputElement) {
                    if (event.keyCode === 13) { // 13 es el código de la tecla "Enter"
                        event.preventDefault(); // Evita la acción predeterminada (como enviar el formulario)
                        actualizarCampos(inputElement);
                        cargarProfesor();
                    }
                }

                function actualizarCampos(inputElement) {
                    const nempleadoInput = document.getElementById("nempleado");
                    const nombreProfesorInput = document.getElementById("nombreProfesor");
                    const resultadoBusqueda = document.getElementById("resultado_busqueda");

                    const opcionSeleccionada = Array.from(resultadoBusqueda.options).find(option => option.value === inputElement.value);

                    if (opcionSeleccionada) {
                        nempleadoInput.value = opcionSeleccionada.getAttribute('data-nempleado');
                        nombreProfesorInput.value = opcionSeleccionada.textContent;
                    } else {
                        nempleadoInput.value = "";
                    }
                    cargarProfesor(); // Llama a cargarProfesor al final de la función actualizarCampos
                }



                function cargarProfesor() {
                    const nempleado = document.getElementById("nempleado").value;
                    const nombreProfesor = document.getElementById("nombreProfesor").value;

                    // Si ambos campos están vacíos, limpiar el horario y el nombre del profesor mostrado
                    if (!nempleado && !nombreProfesor) {
                        limpiarTablaHorario();
                        document.getElementById('profesorNombre').innerHTML = '';
                        return;
                    }

                    const xhr = new XMLHttpRequest();

                    xhr.open("POST", "php/cargarProfesor.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            const profesor = JSON.parse(xhr.responseText);
                            console.log("Profesor: ", profesor);
                            if (Object.keys(profesor).length === 0) {
                                document.getElementById('profesorNombre').innerHTML = 'Profesor no encontrado';
                            } else {
                                mostrarProfesor(profesor);
                                cargarHorarioProfesor(profesor);
                            }
                        }
                    };

                    xhr.send("nempleado=" + encodeURIComponent(nempleado) + "&nombreProfesor=" + encodeURIComponent(nombreProfesor));
                }


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


                document.getElementById('nempleado').addEventListener('input', function() {
                    if (this.value) {
                        cargarProfesor();
                    } else {
                        document.getElementById('profesorNombre').innerHTML = '';
                    }
                });

                document.getElementById('nombreProfesor').addEventListener('input', function() {
                    if (this.value) {
                        buscarProfesorPorNombre(this.value);
                    } else {
                        document.getElementById('profesorNombre').innerHTML = '';
                    }
                });


                $(document).ready(function() {
                    $('#nempleado').on('change', function() {
                        if ($(this).val()) {
                            cargarProfesor();
                        }
                    });
                });

                function cargarHorarioProfesor(profesor) {
                    console.log("Cargando horario para: ", profesor.nempleado); // Agrega esta línea

                    // Limpia el horario del profesor anterior
                    limpiarTablaHorario();

                    $.ajax({
                        url: './php/obtenerHorarioProfesor.php',
                        type: 'POST',
                        data: {
                            nempleado: parseInt(profesor.nempleado) // Aquí se convierte a número entero
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response) {
                                llenarTablaHorario(response);
                            }
                        },
                        error: function() {
                            alert('Error al cargar el horario del profesor');
                        }
                    });
                }



                function mostrarProfesor(profesor) {
                    document.getElementById('profesorNombre').innerHTML = profesor.nombre;
                }


                function llenarTablaHorario(horario) {
                    horario.forEach(function(item) {
                        var dia_semana = item['dia_semana'];
                        var hora_inicio = parseInt(item['hora_inicio'].substring(0, 2));
                        var hora_fin = parseInt(item['hora_fin'].substring(0, 2));
                        var tipo_hora = parseInt(item['tipo_hora']);

                        var color = getColorFromTipoHora(tipo_hora);

                        // Itera sobre todas las horas desde la hora de inicio hasta la hora de finalización
                        for (var hora_actual = hora_inicio; hora_actual < hora_fin; hora_actual++) {
                            var id_celda = 'dia' + dia_semana + '_' + hora_actual.toString().padStart(2, '0');
                            $('#' + id_celda).css('background-color', color);
                        }
                    });
                }

                function limpiarTablaHorario() {
                    // Itera sobre todos los días de la semana (1 a 5)
                    for (let dia = 1; dia <= 5; dia++) {
                        // Itera sobre todas las horas del día (7 a 22)
                        for (let hora = 7; hora <= 22; hora++) {
                            const id_celda = 'dia' + dia + '_' + hora.toString().padStart(2, '0');
                            $('#' + id_celda).css('background-color', '');
                        }
                    }
                }


                function getColorFromTipoHora(tipo_hora) {
                    switch (tipo_hora) {
                        case 1: // Tutorías
                            return '#98BBF5'; // Amarillo
                        case 2: // Psicológicas
                            return '#F7CAC9'; // Azul claro
                        case 4: // Académicas
                            return '#B2DFDB'; // Verde
                        default:
                            return ''; // Sin color
                    }
                }


                function submitFormWithConfirmation() {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: 'Se enviara el horario a el profesor asignado, revisa bien los campos',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'Cancelar',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-dark btn-opciones  mr-2',
                            cancelButton: 'btn btn-danger mr-2'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.querySelector('form').submit();
                        }
                    });
                }


                function submitFormWithAjax() {
                    var formData = new FormData(document.getElementById("horario-form"));
                    $.ajax({
                        type: "POST",
                        url: "./php/guardarHorario.php",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(response) {
                            if (response.result === "success") {
                                Swal.fire({
                                    icon: "success",
                                    title: response.message,
                                    showConfirmButton: false,
                                    text: response.text,
                                    timer: 2000,
                                    timerProgressBar: true
                                }).then(() => {
                                    location.reload();
                                });

                            } else if (response.result === "error") {
                                Swal.fire({
                                    icon: "error",
                                    title: response.message,
                                    text: response.text,
                                    showConfirmButton: false,
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                icon: "error",
                                title: "Error al guardar el horario del profesor",
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });
                        }
                    });
                }

                function submitFormWithConfirmation() {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Se asignará el horario que has puesto al profesor",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#343A40',
                        cancelButtonColor: '#ff7f7f',
                        confirmButtonText: 'Sí, asignar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitFormWithAjax();
                        }
                    });
                }
            </script>

</body>

</html>