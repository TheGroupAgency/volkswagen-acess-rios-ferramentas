<?php

class TireController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$vehicleList = Tire::select('vehicle')
			->groupBy('vehicle')
			->orderBy('vehicle', 'ASC')
			->get();

		$vehicleSelect = array();
		foreach($vehicleList as $vehicle) {
			$vehicleSelect[$vehicle->vehicle] = $vehicle->vehicle;
		}

		return Response::view('tire.index', array(
			'vehicleSelect' => $vehicleSelect
		));
	}

	/**
	 * Get a year list of specific vehicle
	 * @return json
	 */
	public function getVehicleTireSize() {
		$vehicle = Input::get('vehicle');
		$result = Tire::select('size')
			->where('vehicle', $vehicle)
			->groupBy('size')
			->orderBy('size', 'ASC')
			->get()
			->lists('size');

		return Response::json($result);
	}

	/**
	 * Get a tire list of specific vehicle/year/model
	 * @return json
	 */
	public function getVehicleTire() {
		$vehicle = Input::get('vehicle');
		$size = Input::get('size');
		
		$result = Tire::select('model', 'manufacturer_model', 'measure', 'image')
			->where('vehicle', $vehicle)
			->where('size', $size)
			->orderBy('id', 'ASC')
			->get();
			
		foreach($result as $tire) {
			$tire->model = nl2br($tire->model);
		}

		return Response::json($result);
	}
}
