<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use App\Models\Hoteles;
use App\Models\HabitacionesHoteles;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Response\JsonResponse;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use Illuminate\Database\QueryException;
use App\Traits\CustomFunctions;

/**
 * Description of AdminController
 *
 * @author sapc_
 */
class AdminController {

    //put your code here
    use CustomFunctions;

    public function indexAction(ServerRequest $request) {
        $id = $request->getAttribute('id');

        $listado = HabitacionesHoteles::where('id_hotel',$id)
                ->join('acomodacionhabitaciones', 'habitacioneshoteles.id_acomodacion_h', '=', 'acomodacionhabitaciones.id')
                ->join('acomodaciones', 'acomodaciones.id', '=', 'acomodacionhabitaciones.id_acomodacion')
                ->join('habitaciones', 'habitaciones.id', '=', 'acomodacionhabitaciones.id_habitacion')
                ->select('habitacioneshoteles.*', 'habitaciones.nombre as habitacion', 'acomodaciones.nombre as acomodacion')
                ->get();

        return new JsonResponse($listado);
    }

    public function addHabitacionHotel(ServerRequest $req) {

        $requestData = $req->getBody()->getContents();

        $data = json_decode($requestData);

        if ($data) {

            // validaciones
            $hotelValidacion = v::attribute('hotel', v::intType()->notEmpty())
                    ->attribute('acomodacion', v::intType()->notEmpty())
                    ->attribute('cantidad', v::intType()->notEmpty());

            try {
                // se compruebas las validaciones
                $hotelValidacion->assert($data);
                // se comprueba si ya existe el hotel
                $hotel = Hoteles::findOrFail($data->hotel);

                if ($hotel) {
                    // se comprueba si ya existe la acomodacion en el hotel
                    $acomodacion = HabitacionesHoteles::where('id_acomodacion_h', $data->acomodacion)
                            ->where('id_hotel', $data->hotel)
                            ->first();

                    $habitacionesHotel = $this->totalHabitaciones($hotel->totalHabitaciones);

                    if ($acomodacion) {
                        $res = array('res' => false, 'error' => 'exist', 'message' => 'La habitacion ya se encuentra registrada en el hotel');
                    } elseif (($habitacionesHotel + $data->cantidad) > $hotel->habitaciones) {
                        $res = array('res' => false, 'error' => 'fullHab', 'message' => 'La cantidad de habitaciones supera las cantidad disponible en el hotel');
                    } else {

                        // se segistra
                        $habitacionH = new HabitacionesHoteles();

                        $habitacionH->id_hotel = $data->hotel;
                        $habitacionH->id_acomodacion_h = $data->acomodacion;
                        $habitacionH->cantidad = $data->cantidad;
                        $habitacionH->save();

                        $res = array('res' => true, 'habitacion' => 'save', 'message' => 'La habitacion ha sido registrada al hotel correctamente');
                    }
                } else {
                    $res = array('res' => false, 'error' => 'noexist', 'message' => 'Hotel no existe');
                }
            } catch (NestedValidationException $exception) {
                $res = array('res' => false, 'error' => 'valdation', 'message' => $exception->getMessages());
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exc) {
                $res = array('res' => false, 'message' => 'No se encontro el registro');
            } catch (QueryException $exception) {
                $res = array('res' => false, 'error' => 'query', 'message' => $exception->getMessage());
            } catch (\Exception $exception) {
                $res = array('res' => false, 'error' => 'exception', 'message' => $exception->getMessage());
            }
        } else {
            $res = array('res' => false, 'error' => 'data', 'message' => 'parameters not sent');
        }

        return new JsonResponse($res);
    }

    public function deleteHabitacionHotel(ServerRequest $request) {
        $id = $request->getAttribute('id');

        try {
            $habitacionH = HabitacionesHoteles::findOrFail($id);
            $habitacionH->delete();
            $res = array('res' => true, 'message' => 'Se elimino correctamente la habitacion del hotel');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exc) {
            $res = array('res' => false, 'error' => 'nofound','message' => 'No se encontro el registro');
        } catch (\Exception $exception) {
            $res = array('res' => false, 'error' => 'exception', 'message' => $exception->getMessage());
        }

        return new JsonResponse($res);
    }

}
