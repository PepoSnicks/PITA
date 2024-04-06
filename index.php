<?php

session_start();
ob_start(); // Agrega esta línea

include("./php/conexion.php");

if (isset($_COOKIE['user'])) {
    $usuario = $_COOKIE['user'];

    // Buscar el usuario en la base de datos (tanto en profesores como en alumnos)
    $consulta = "SELECT * FROM usuarios WHERE matricula='$usuario' OR nempleado='$usuario'";

    $resultado = $conexion->query($consulta);

    if ($fila = $resultado->fetch_assoc()) {
        $_SESSION['user'] = $usuario;
        $_SESSION['nombre'] = $fila['nombre'];
        $_SESSION['nivel_acceso'] = $fila['nivel_acceso'];

        if ($fila['nivel_acceso'] == 1) { // Profesor
            $_SESSION['nempleado'] = $fila['nempleado'];
        } else { // Alumno
            $_SESSION['matricula'] = $fila['matricula'];
        }
    }
}

if (isset($_SESSION['idnivel'])) {
    // Redirigir al usuario según el nivel de acceso
    $idnivel = $_SESSION['idnivel'];
    echo "Redireccionando al nivel de acceso: " . $idnivel . "<br>";
    if ($idnivel == 3) {
        ob_end_clean(); // Agrega esta línea
        header("location:./psicologos/indexPsicologo.php");
        die();
    } else if ($idnivel == 1) {
        ob_end_clean(); // Agrega esta línea
        header("location:./jefe/indexJefe.php");
        die();
    } else if ($idnivel == 2) {
        ob_end_clean(); // Agrega esta línea
        header("location:./tutores/indexTutor.php");
        die();
    } else if ($idnivel == 4) {
        ob_end_clean(); // Agrega esta línea
        header("location:./profesores/indexProfesor.php");
        die();
    } else if ($idnivel == 5) {
        ob_end_clean(); // Agrega esta línea
        header("location:./director/indexDirector.php");
        die();
    }
} elseif (isset($_SESSION['matricula'])) {
    header("location:./alumnos/indexAlumnos.php");
    die();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <style>
        body {
            background: url('imagenes/IndexPITA.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="./css/icon.png" type="image/png" id="icono">
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap'>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>

    <script>
        // Función para alternar la visibilidad de la contraseña
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById('password');
            var eyeIcon = document.getElementById('eye-icon');
    
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.name = 'eye-off-outline'; // ajusta el nombre según el ícono que estás utilizando
            } else {
                passwordInput.type = "password";
                eyeIcon.name = 'eye-outline'; // ajusta el nombre según el ícono que estás utilizando
            }
        }
    </script>
    <style>
        body {
    background-color: rgba(218, 50, 91, 0.5); /* Ajusta este valor para cambiar la transparencia */
    color: hsl(0deg 0% 30%);
    font-family: 'Poppins', sans-serif;
    user-select: none;
    overflow-y: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0; /* Asegura que no haya margen para ocupar toda la pantalla */
}

.screen-1 {
    background: rgba(213, 85, 97, 0); /* Ajusta este valor para cambiar la transparencia */
    padding: 2em;
    display: flex;
    flex-direction: column;
    border-radius: 30px;
    box-shadow: 0 0 2em hsl(231deg 10% 60%);
    gap: 2em;
}


        .logo {
            margin-top: -3em;
        }

        .email, .password {
            background: hsl(0deg 0% 100%);
            box-shadow: 0 0 2em hsl(231deg 10% 85%);
            padding: 1em;
            display: flex;
            flex-direction: column;
            gap: 0.5em;
            border-radius: 20px;
            color: hsl(0deg 0% 30%);
            margin-top: 1em;
        }

        .email input, .password input {
            outline: none;
            border: none;
            padding: 10px;
            font-size: 0.9em;
            position: relative;
            text-align: center;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-container ion-icon {
            font-size: 1.2em;
            margin-right: 10px;
            color: hsl(0deg 0% 30%);
            pointer-events: none;
        }

        .email input::placeholder, .password input::placeholder {
            color: hsl(0deg 0% 0%);
            font-size: 0.9em;
            padding-left: 0px;
            text-align: center;
        }

        .email input:focus::placeholder, .password input:focus::placeholder {
            color: transparent;
        }

        ion-icon {
            color: hsl(0deg 0% 30%);
            margin-bottom: -0.2em;
        }

        .show-hide {
            margin-right: -5em;
        }

        .login {
            padding: 1em;
            background: hsl(233deg 36% 38%);
            color: hsl(0 0 100);
            border: none;
            border-radius: 30px;
            font-weight: 600;
            
        }

        .footer {
            display: flex;
            font-size: 0.7em;
            color: hsl(0deg 0% 37%);
            gap: 14em;
            padding-bottom: 10em;
        }

        .footer span {
            cursor: pointer;
        }

        .btn-opciones:hover {
            background-color: #0096C7;
            border-color: #0096C7;
            font-weight: bold;
        }

        #icono {
            width: 0px;
            height: 32px;
            display: block;
            margin: 0 auto;
        }

        .logo-container, .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 100%;
        }

        @media (max-width: 575.98px) {
            .modal-content {
                max-width: 90%;
            }
        }

        .password {
            position: relative;
        }

        .password input {
            padding-right: 40px; /* Ajusta el espacio para el icono del ojo */
        }

        .show-hide {
            position: absolute;
            top: 70%;
            right: 100px; /* Ajusta la posición del icono del ojo según sea necesario */
            transform: translateY(-50%);
            cursor: pointer;
        }


        #passwordStrengthBar {
            width: 100%;
            height: 10px;
            background-color: #ddd;
            position: relative;
        }

        #passwordStrengthBar::after {
            content: '';
            height: 100%;
            width: 0;
            position: absolute;
            top: 0;
            left: 0;
            transition: width 0.5s;
            background-color: var(--strength-color);
        }
    </style>

    <title>Inicio de sesión</title>


    

</head>
<body>
    <div class="login-container container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-12">
                <div class="login-box screen-1">
                    <div class="logo-container">
                    <svg class="logo" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="300" height="300" viewBox="0 0 640 480" xml:space="preserve">
    <g transform="matrix(3.31 0 0 3.31 320.4 240.4)">
        <!-- Reemplaza el círculo con la imagen -->
        <image xlink:href="imagenes/uptex.jpeg" width="-10080" height="120" x="-85" y="-40"></image>
    </g>
</svg>

                    </div>
                    <form method="POST" action="login.php">
                        <div class="email input-container">
                            <label for="user" class="form-label"><ion-icon name="person"></ion-icon> Usuario</label>
                            <input type="text" class="form-control" id="user" placeholder="Ingrese su usuario" name="user">
                        </div>
                        
                        <div class="password input-container">
                            <label for="password" class="form-label"><ion-icon name="lock-closed"></ion-icon> Contraseña</label>
                            <input type="password" class="form-control" id="password" placeholder="Ingrese su contraseña" name="password">
                            <span class="show-hide" onclick="togglePasswordVisibility()">
                                <ion-icon id="eye-icon" name="eye-outline"></ion-icon>
                            </span>
                        </div>                        
                                                                    
                        <br>
                        <div class="d-flex justify-content-center"> <!-- Cambié 'p' a 'div' y añadí las clases 'd-flex justify-content-center' aquí -->
                            <input class="login btn btn-dark" type="submit" value="Iniciar sesión" name="submit">
                        </div>
                        <br>
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#registroAlumnoModal"><b>Registrarte</b></button>
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#recuperarModal"><b>Recuperar contraseña</b></button>
                        <br>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro de Alumno -->
<div class="modal fade" id="registroAlumnoModal" tabindex="-1" role="dialog" aria-labelledby="agregarProfesorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered justify-content-center modal-container" role="document">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="modal-header">
                    <h3 class="modal-title text-danger" id="registroAlumnoModal">¡Regístrate como alumno!</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="registroAlumnoForm">
                        <form method="POST" action="./php/nuevoAlumno.php">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nombre" class="text-danger">Nombre:</label>
                                        <input type="text" placeholder="Alexis Téllez Almazán" class="form-control rounded-pill" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="matricula" class="text-danger">Matrícula:</label>
                                        <input type="number" placeholder="13191205123" class="form-control rounded-pill" id="matricula" name="matricula" min="1" max="99999999999" step="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="grupo" class="text-danger">Grupo:</label>
                                        <input type="number" placeholder="0 ó 1" class="form-control rounded-pill" id="grupo" name="grupo" min="0" max="1" step="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="generacion" class="text-danger">Generación:</label>
                                        <input type="number" placeholder="2023" class="form-control rounded-pill" id="generacion" name="generacion" min="2023" max="2030" step="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="email2" class="text-danger">Correo electrónico:</label>
                                        <input type="email" placeholder="tu@correo.com" class="form-control rounded-pill" id="email2" name="email2" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contraseña" class="text-danger">Contraseña temporal:</label>
                                        <input type="password" placeholder="123456" class="form-control rounded-pill" id="contraseña" name="contraseña" required>
                                        <div class="progress mt-2">
                                            <div id="passwordStrengthBar" class="progress-bar bg-danger rounded-pill"></div>
                                        </div>
                                        <span id="passwordStrengthMessage" class="mt-1 d-block"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="turno" class="text-danger">Turno:</label>
                                        <select class="form-control rounded-pill" id="turno" name="turno" required>
                                            <option value="" disabled selected>Selecciona tu turno</option>
                                            <option value="Matutino">Matutino</option>
                                            <option value="Vespertino">Vespertino</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="id_carrera" class="text-danger">Carrera:</label>
                                        <select class="form-control rounded-pill" id="id_carrera" name="id_carrera" required>
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
                                        <label for="periodo_inscripcion" class="text-danger">Periodo de inscripción:</label>
                                        <select class="form-control rounded-pill" id="periodo_inscripcion" name="periodo_inscripcion" required>
                                            <option value="" disabled selected>Selecciona un periodo</option>
                                            <option value="1">Enero - Abril</option>
                                            <option value="2">Mayo - Agosto</option>
                                            <option value="3">Septiembre - Diciembre</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Campos ocultos -->
                            <input type="hidden" name="strikes" value="0">
                            <input type="hidden" name="active" value="1">
                            <input type="hidden" name="imagen_de_perfil" value="NULL">
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-opciones btn-danger rounded-pill" id="registroAlumnoBtn">¡Registrarte Ahora!</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal de recuperar contraseña -->
<div class="modal fade" id="recuperarModal" tabindex="-1" role="dialog" aria-labelledby="recuperarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered justify-content-center modal-container" role="document">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="recuperarModalLabel">Recuperar contraseña</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="recuperarForm" method="POST" class="text-center" action="./php/recuperarPassword.php">
                        <div class="form-group">
                            <label for="email" class="text-danger">Correo electrónico:</label>
                            <input type="email" class="form-control rounded-pill" id="email" name="email" required>
                            <span id="emailError" class="text-danger" style="display:none;"></span>
                        </div>
                        <button type="submit" class="btn btn-danger btn-rounded"><i class="bi bi-lock"></i> Enviar correo de verificación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de verificación de código -->
<div class="modal fade" id="verificarCodigoModal" tabindex="-1" role="dialog" aria-labelledby="verificarCodigoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
        <div class="modal-content">
            <div class="container-fluid">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="verificarCodigoModalLabel">Verificar código</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" class="text-center" action="./php/verificarCodigo.php?email=<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>">
                        <div class="form-group">
                            <label for="codigo" class="text-danger">Código de verificación:</label>
                            <input type="text" id="codigo" name="codigo" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="nueva_password" class="text-danger">Nueva contraseña:</label>
                            <input type="password" id="nueva_password" name="nueva_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmar_nueva_password" class="text-danger">Confirmar nueva contraseña:</label>
                            <input type="password" id="confirmar_nueva_password" name="confirmar_nueva_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <button type="button" id="mostrar_ocultar_password" class="btn btn-dark">Mostrar/Ocultar contraseña</button>
                        </div>
                        <input type="submit" value="Actualizar contraseña" class="btn btn-danger btn-rounded">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


    <script>
        $(document).ready(function() {
            $("form[action='./php/recuperarPassword.php']").on("submit", function(e) {
                e.preventDefault();

                const emailField = $("#email");
                const formData = new FormData(this);

                $.ajax({
                    type: "POST",
                    url: "./php/recuperarPassword.php",
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            window.location.href = './index.php?showVerificarCodigoModal=true&email=' + emailField.val();
                        } else {
                            emailField.css("border-color", "red");
                            emailField.focus();
                            Swal.fire({
                                icon: 'error',
                                title: 'Correo electrónico no encontrado',
                                text: response.message,
                                confirmButtonText: 'Entendido',
                                allowOutsideClick: true,
                                confirmButtonColor: '#ff7f7f'
                            });
                        }
                    }
                });
            });
                // Función para verificar si las contraseñas coinciden antes de enviar el formulario
    $("form[action='./php/verificarCodigo.php?email=<?php echo isset($_GET['email']) ? $_GET['email'] : ''; ?>']").on("submit", function(e) {
        const nueva_password = $("#nueva_password");
        const confirmar_nueva_password = $("#confirmar_nueva_password");

        if (nueva_password.val() !== confirmar_nueva_password.val()) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Las contraseñas no coinciden',
                text: 'Por favor, asegúrate de que las contraseñas ingresadas coincidan.',
                confirmButtonText: 'Entendido',
                allowOutsideClick: true,
                confirmButtonColor: '#ff7f7f'
            });
        }
    });

    // Función para mostrar/ocultar la contraseña
    $("#mostrar_ocultar_password").on("click", function() {
        const nueva_password = $("#nueva_password");
        const confirmar_nueva_password = $("#confirmar_nueva_password");

        if (nueva_password.attr("type") === "password") {
            nueva_password.attr("type", "text");
            confirmar_nueva_password.attr("type", "text");
        } else {
            nueva_password.attr("type", "password");
            confirmar_nueva_password.attr("type", "password");
        }
    });
        });
    </script>
    <?php
    if (isset($_GET['showVerificarCodigoModal']) && $_GET['showVerificarCodigoModal'] == 'true') {
        echo "<script>
            $(document).ready(function() {
                $('#verificarCodigoModal').modal('show');
            });
        </script>";
    }
    ?>

    <script>
        document.getElementById('registroAlumnoBtn').addEventListener('click', function(event) {
            event.preventDefault();
            submitForm();
        });

        document.getElementById('contraseña').addEventListener('input', function() {
            const passwordStrengthBar = document.getElementById('passwordStrengthBar');
            const passwordStrengthMessage = document.getElementById('passwordStrengthMessage');
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

        async function submitForm() {
            const passwordStrength = isPasswordSecure(document.getElementById('contraseña').value);
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
            }
            const formData = new FormData(document.querySelector('#registroAlumnoForm form'));

            const response = await fetch('php/nuevoAlumno.php', { // Reemplaza 'your_php_file.php' con la ruta de tu archivo PHP
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.message === 'approved') {
                Swal.fire({
                    title: '¡Bienvenid@ a PITA!',
                    text: 'Ya puedes acceder al menú principal',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    allowOutsideClick: true
                }).then(() => {
                    location.reload(); // Recarga la página para limpiar el formulario
                });
            } else {
                let message = '';

                switch (data.message) {
                    case 'duplicated':
                        message = 'El usuario ya existe, por favor revisa tu email o matrícula';
                        break;
                    case 'weak_password':
                        message = 'La contraseña es insegura, revisa que tu contraseña cumpla lo el minimo nivel que es "débil"';
                        break;
                    case 'error':
                    default:
                        message = 'Error al registrar el usuario';
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
    </script>
</body>


<?php
$errorUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

function showAlert($icon, $text, $title, $redirect = false)
{
    $timer = 4000;
    $redirectScript = $redirect ? "window.location.href = './index.php';" : "window.history.back();";

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
                    {$redirectScript}
                });
            }
            showAlert();
        </script>";
}

if (strpos($errorUrl, "singup=empty") == true) {
    showAlert('error', 'No se han recibido datos', 'Llena todos los campos', false);
}
if (strpos($errorUrl, "singup=error") == true) {
    showAlert('error', 'Error en la contraseña o usuario', 'Error al iniciar sesión', false);
}
if (strpos($errorUrl, "singup=usuariodesconocido") == true) {
    showAlert('error', 'Error en la contraseña o usuario', 'Por favor vuelve a intentarlo', false);
}
if (strpos($errorUrl, "reset_password=success") == true) {
    showAlert('success', 'Contraseña actualizada', 'Puedes ingresar con la nueva contraseña', true);
}
if (strpos($errorUrl, "reset_password=error") == true) {
    showAlert('error', 'Código incorrecto', 'El código ingresado no coincide', false);
}
if (strpos($errorUrl, "email_not_found=true") == true) {
    showAlert('error', 'El correo electrónico ingresado no se encuentra en nuestra base de datos', 'Correo electrónico no encontrado', false);
}
?>

</html>