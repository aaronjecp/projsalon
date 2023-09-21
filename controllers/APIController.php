<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();

        echo json_encode($servicios);
    }

    public static function guardar() {

        // Almacena la cita y devuelve el ID
        $cita = new Cita($_POST);

        $resultado = $cita->guardar();

        $id = $resultado['id'];

        
        // Almacena la cita y los servicios

        // Separar por comas con la siguiente sentencia y luego almacena los servicios con el id de las citas

        $idServicios = explode(",", $_POST['servicios']);

        foreach($idServicios as $idServicio){

            

            $args = [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];

            $citaServicio = new CitaServicio($args);
            $resultado = $citaServicio->guardar();
        }

        // retornamos una respuesta
        

        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar() {

        // video 542 udemy seccion 56
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $cita = Cita::find($id);
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }

}