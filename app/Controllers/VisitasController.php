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
 * Description of HotelesController
 *
 * @author sapc_
 */
class HotelesController {

    //put your code here
    use CustomFunctions;

    public function getHoteles() {

        $listado = Hoteles::join('info_ciudades', 'hoteles.id_ciudad', '=', 'info_ciudades.ide_c')
                ->select('hoteles.*', 'info_ciudades.ciudad', 'info_ciudades.departamento')
                ->get();

        return new JsonResponse($listado);
    }

    public function addHotel(ServerRequest $req) {

        $requestData = $req->getBody()->getContents();

        $data = json_decode($requestData);

        if ($data) {

            // validaciones
            $hotelValidacion = v::attribute('nit', v::stringType()->notEmpty())
                    ->attribute('nombre', v::stringType()->notEmpty())
                    ->attribute('direccion', v::stringType()->notEmpty())
                    ->attribute('id_ciudad', v::intType()->notEmpty())
                    ->attribute('habitaciones', v::intType()->notEmpty());

            try {
                // se compruebas las validaciones
                $hotelValidacion->assert($data);
                // se comprueba si ya existe el hotel
                $hotel = Hoteles::where('nit', $data->nit)->first();

                if ($hotel) {
                    $res = array('res' => false, 'error' => 'exist', 'message' => 'Hotel ya se encuentra registrado');
                } else {
                    // se segistra
                    $hotel = new Hoteles();

                    $hotel->nit = $data->nit;
                    $hotel->nombre = $data->nombre;
                    $hotel->direccion = $data->direccion;
                    $hotel->id_ciudad = $data->id_ciudad;
                    $hotel->habitaciones = $data->habitaciones;

                    $hotel->save();

                    $res = array('res' => true, 'hotel' => 'save', 'message' => 'Hotel ha sido registrado');
                }
            } catch (NestedValidationException $exception) {
                $res = array('res' => false, 'error' => 'validation', 'message' => $exception->getMessages());
            } catch (QueryException $exception) {
                $res = array('res' => false, 'error' => 'query', 'message' => $exception->getMessage());
            } catch (\Exception $exception) {
                $res = array('res' => false, 'error' => 'exception', 'message' => $exception->getMessages());
            }
        } else {
            $res = array('res' => false, 'error' => 'data', 'message' => 'parameters not sent');
        }

        return new JsonResponse($res);
    }

    public function updateHotel(ServerRequest $req) {

        $requestData = $req->getBody()->getContents();

        $data = json_decode($requestData);

        if ($data) {

            // validaciones
            $hotelValidacion = v::attribute('id_hotel', v::intType()->notEmpty())
                    ->attribute('nombre', v::stringType()->notEmpty())
                    ->attribute('nit', v::stringType()->notEmpty())
                    ->attribute('direccion', v::stringType()->notEmpty())
                    ->attribute('id_ciudad', v::intType()->notEmpty())
                    ->attribute('habitaciones', v::intType()->notEmpty());

            try {
                // se compruebas las validaciones
                $hotelValidacion->assert($data);
                //se consulta el hotel
                $hotel = Hoteles::findOrFail($data->id_hotel);
                //si cambia el nit
                if ($hotel->nit != $data->nit) {
                    // se comprueba si ya existe el hotel
                    $nitHotel = Hoteles::where('nit', $data->nit)->first();
                    if ($nitHotel) {
                        $res = array('res' => false, 'error' => 'exist', 'message' => 'En nit que esta tratando de actualizar ya se encuentra registrado');
                        return new JsonResponse($res);
                    }
                }

                //habitaciones asignadas al hotel
                $habitacionesHotel = $this->totalHabitaciones($hotel->totalHabitaciones);

                if ($data->habitaciones < $habitacionesHotel) {
                    $res = array('res' => false, 'message' => 'No puede establecer una cantidad de habitaciones menor a las asignadas actualmente');
                } else {
                    // se actualiza
                    $hotel->nit = $data->nit;
                    $hotel->nombre = $data->nombre;
                    $hotel->direccion = $data->direccion;
                    $hotel->id_ciudad = $data->id_ciudad;
                    $hotel->habitaciones = $data->habitaciones;

                    $hotel->update();

                    $res = array('res' => true, 'hotel' => 'save', 'message' => 'Hotel ha sido actualizado correctamente');
                }
            } catch (NestedValidationException $exception) {
                $res = array('res' => false, 'error' => 'valdation', 'message' => $exception->getMessages());
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exc) {
                $res = array('res' => false, 'message' => 'No se encontro el registro');
            } catch (QueryException $exception) {
                $res = array('res' => false, 'error' => 'query', 'message' => $exception->getMessage());
            } catch (\Exception $exception) {
                $res = array('res' => false, 'error' => 'exception', 'message' => $exception->getMessages());
            }
        } else {
            $res = array('res' => false, 'error' => 'data', 'message' => 'parameters not sent');
        }

        return new JsonResponse($res);
    }

    public function getHotel(ServerRequest $request) {
        $id = $request->getAttribute('id');

        $res = Hoteles::where('id', $id)
                ->join('info_ciudades', 'hoteles.id_ciudad', '=', 'info_ciudades.ide_c' )
                ->select('hoteles.*', 'info_ciudades.ciudad', 'info_ciudades.id_departamento', 'info_ciudades.departamento')
                ->first();

        return new JsonResponse($res);
    }

    
    public function getDetallesHotel(ServerRequest $request) {
        $id = $request->getAttribute('id');

        $res = Hoteles::where('id', $id)
                ->join('info_ciudades', 'hoteles.id_ciudad', '=', 'info_ciudades.ide_c' )
                ->select('hoteles.*', 'info_ciudades.ciudad', 'info_ciudades.id_departamento', 'info_ciudades.departamento')
                ->first();
        
        $habi = HabitacionesHoteles::where('id_hotel',$id)
                ->join('acomodacionhabitaciones', 'habitacioneshoteles.id_acomodacion_h', '=', 'acomodacionhabitaciones.id')
                ->join('acomodaciones', 'acomodaciones.id', '=', 'acomodacionhabitaciones.id_acomodacion')
                ->join('habitaciones', 'habitaciones.id', '=', 'acomodacionhabitaciones.id_habitacion')
                ->select('habitacioneshoteles.*', 'habitaciones.nombre as habitacion', 'acomodaciones.nombre as acomodacion')
                ->get();
        
        return new JsonResponse(array('hotel'=>$res,'habitaciones'=>$habi));
    }
}
