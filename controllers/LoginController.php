<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Classes\Email;

class LoginController {

    public static function login(Router $router) {
    
       
        $alertas = [];


        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $auth = new Usuario($_POST);
            
            $alertas = $auth->validarLogin();


            if(empty($alertas)){
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario) {
                    // Verificar el password

                   if($usuario->comprobarPasswordAndVerificado($auth->password)){
                    session_start();
                    $_SESSION['id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['login'] = true;

                    // Redireccionamiento

                    if($usuario->admin === "1") {
                        $_SESSION['admin'] = $usuario->admin ?? null;
                        header('Location: /admin');
                    } else {
                        header('Location: /cita');
                    }

                  

                   }

                } else {
                   Usuario::setAlerta('error', 'no encontrado');
                  
                }
            }

            $alertas = Usuario::getAlertas();

          // debuguear($auth);
           
        }

      $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout() {
        session_start();

      
        $_SESSION = [];

        header('Location: /');

    }
    public static function forget(Router $router) {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);
                if($usuario && $usuario->confirmado === "1"){

                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // TODO: Enviar email
                    $email = new Email($usuario->email,$usuario->nombre,$usuario->token );
                    $email->enviarInstrucciones();

                    Usuario::setAlerta('exito', 'Revisa tu email');

                } else {
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                   
                }
            }
            
        }

       $alertas = Usuario::getAlertas();

       $router->render('auth/forget', [
        'alertas' => $alertas

       ]);

    }
    public static function recover(Router $router) {
        //funcion para recuperar el password

        $alertas = [];
        $error = false;
        $token = s($_GET['token']);
       

        // sin el siguiente if se modifica el primer EMPTY que encuentra en la BD
        // por lo que se puede crear un cambio no deseado (vulnerabilidad)
        if(empty($token)) {
            header('Location: /');
        }
        
         // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);

            $alertas = $password->validarPassword();
           
            // Si pasamos la validacion entonces podemos hashear el password
           if(empty($alertas)) {
            $usuario->password = null;

            $usuario->password = $password->password;
            $usuario->hashPassword();
            $usuario->token = null;

            $resultado = $usuario->guardar();
            if($resultado) {
                header('Location: /');
            }

            
            debuguear($usuario);
           }
        }

        $alertas = Usuario::getAlertas();

      //  debuguear($usuario);
        $router->render('auth/recuperar-password',
        ['alertas' => $alertas,
        'error' => $error]
    );



    }
    public static function createaccount(Router $router) {

        $usuario = new Usuario();

        // Alertas vacias
        $alertas = [];

      
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){

           $usuario->sincronizar($_POST);
           $alertas = $usuario->validarNuevaCuenta();

           // Revisar que alerta este vacío
           if(empty($alertas)) {
            $resultado = $usuario->existeUsuario();
            if($resultado->num_rows) {
                $alertas = Usuario::getAlertas();
            }
            else {
                // Hashear el password
                $usuario->hashPassword();

                // Generar un Token único
                $usuario->crearToken();

                // Enviar el email
                $email = new Email($usuario->email, $usuario->nombre, 
                $usuario->token);

                $email->enviarConfirmacion();

                // Crear el usuario
                $resultado = $usuario->guardar();

                if($resultado) {
                   header('Location: /mensaje');
                }

            }

           }
               
        }
        
        $router->render('auth/create-account', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);

    }

    public static function mensaje(Router $router) {
        $router->render('auth/mensaje');
    }
    public static function confirmar(Router $router) {
        $alertas = [];

        $token = s($_GET['token']);

       $usuario = Usuario::where('token', $token);

        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');
        } else {
            // Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;

            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta confirmada');
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta',
        [
            'alertas' => $alertas
        ]);
    }



}