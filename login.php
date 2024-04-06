<?php

session_start();

include("./php/conexion.php");
include("./php/encrypt.php"); // Incluir las funciones de encriptación y desencriptación

if (isset($_COOKIE['user'])) {
    $usuario = $_COOKIE['user'];

    // Buscar el usuario en la base de datos (tanto en profesores como en alumnos)
    $consulta = "SELECT * FROM usuarios WHERE matricula='$usuario' OR nempleado='$usuario'";

    $resultado = $conn->query($consulta);

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

if (!empty($_POST['submit'])) {
    $usuario = $_POST['user'] ?? '';
    $contrasenia = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember']);
    
    if (empty($usuario) || empty($contrasenia)) {
        header("location:./index.php?error=empty");
        exit();
    } else {
        if (!preg_match("/^[0-9]*$/", $usuario) || !preg_match("/[A-Za-z0-9]+/", $contrasenia)) {
            header("location:./index.php?singup=error");
            exit();
        } else {
            $consulta = "SELECT * FROM usuarios WHERE matricula='$usuario' OR nempleado='$usuario'";
            $resultado = $conn->query($consulta);
            $fila = $resultado->fetch_assoc();
        
            if ($fila) {
                $password_de_bd = $fila['contraseña'];
                $password_desencriptada = decrypt($password_de_bd); // Desencriptar la contraseña de la base de datos
                if ($contrasenia == $password_desencriptada) {
                    $_SESSION['user'] = $usuario;
                    $_SESSION['nombre'] = $fila['nombre'];
                    $_SESSION['nivel_acceso'] = $fila['nivel_acceso'];
                
                    if ($fila['nivel_acceso'] == 1) { // Profesor
                        $_SESSION['nempleado'] = $fila['nempleado'];
                        
                        // Realizar una nueva consulta para obtener el valor de 'id_nivel' de la tabla 'profesores'
                        $consulta_profesor = "SELECT * FROM profesores WHERE nempleado='$usuario'";
                        $resultado_profesor = $conn->query($consulta_profesor);
                        $fila_profesor = $resultado_profesor->fetch_assoc();
                
                        if ($fila_profesor) {
                            $idnivel = $fila_profesor['id_nivel'];
                            $_SESSION['idnivel'] = $idnivel;
                        } else {
                            header("location:./index.php?singup=error");
                            die();
                        }
            
                        if ($rememberMe) {
                            setcookie('user', $usuario, time() + (86400 * 7), "/"); // 86400 = 1 día, la cookie expira en 7 días
                        }
                        if ($idnivel == 1) {
                            header("location:./jefe/indexJefe.php");
                            die();
                        }
                        if ($idnivel == 3) {
                            header("location:./psicologos/indexPsicologo.php");
                            die();
                        } else if ($idnivel == 2) {
                            header("location:./tutores/indexTutor.php");
                            die();
                        } else if ($idnivel == 4) {
                            header("location:./profesores/indexProfesor.php");
                            die();
                        } else if ($idnivel == 5) {
                            header("location:./director/indexDirector.php");
                            die();
                        } else {
                            header("location:./index.php?singup=error");
                            die();
                        }
                    } else { // Alumno
                        $_SESSION['matricula'] = $fila['matricula'];
                        $_SESSION['nivel_acceso'] = null;
                        header("location:./alumnos/indexAlumnos.php");
                        die();
                    }
                } else {
                    header("location:./index.php?singup=error");
                    die();
                }
            } else {
                header("location:./index.php?singup=usuariodesconocido");
                die();
            }
            
        }
    }
}
