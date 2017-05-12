<?php

class EconomyController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$carModelList = Economy::select('car_model')->groupBy('car_model')->orderBy('car_model', 'ASC')->get();
		
		$carModelSelect = array();
		foreach($carModelList as $carModel) {
			$carModel->yearList = Economy::select('year')->where('car_model', $carModel->car_model)->groupBy('year')->orderBy('year', 'ASC')->get()->lists('year');
			$carModelSelect[$carModel->car_model] = $carModel->car_model;
		}
		
		$pieceList = $this->getData();


		return Response::view('economy.index', array(
			'carModelSelect' => $carModelSelect, 
			'pieceList'      => $pieceList, 
			'carModelList'   => $carModelList
		));
	}

	/**
	 * Format data for fill the table with all pieces
	 * @return array
	 */
	private function getData()
	{
		// check if is on cache and doesnt need to update
		if (Cache::has('economyFrontData')) {
			return Cache::get('economyFrontData');
		}

		$result = array();
		// get piece list by name
		$pieceNameList = Economy::select('name')
			->groupBy('name')
			->get();

		foreach($pieceNameList as $pieceName) {
			$newPiece = array();
			$newPiece['name'] = nl2br($pieceName->name);
			// get piece list by code
			$pieceList = Economy::select('name', 'economy_code', 'economy_price_public', 'unit_type', 'show_price')
				->where('name', $pieceName->name)
				->groupBy('economy_code')
				->get();

			foreach($pieceList as $piece) {
				$newPieceCode = [];
				$newPieceCode['show_price'] = $piece->show_price;
				$newPieceCode['economy_code'] = $piece->economy_code;
				$newPieceCode['economy_price_public'] = $piece->show_price == 1 ? $piece->economy_price_public : null;
				$newPieceCode['unit_type'] = $piece->unit_type;
				$newPieceCode['cars'] = [];

				// get cars with this piece
				$carList = Economy::select('car_model')
					->where('economy_code', $piece->economy_code)
					->groupBy('car_model')
					->orderBy('car_model', 'ASC')
					->get();

				foreach($carList as $car) {
					$newCar = array();
					$newCar['name'] = $car->car_model;

					// get years from this car
					$yearList = Economy::select('year')
						->where('economy_code', $piece->economy_code)
						->where('car_model', $car->car_model)
						->get()
						->lists('year');

					$newCar['yearList'] = $yearList;
					
					// format array from years to string
					if (count($yearList) == 1) {
						$newCar['year'] = $yearList[0];
					}
					else {
						$isSequential = true;
						foreach($yearList as $key => $year) {
							if ($key == 0) continue;
							if ($year != $yearList[0]+$key) {
								$isSequential = false;
								break;
							}
						}
						if ($isSequential) {
							$newCar['year'] = $yearList[0] . "-" . $yearList[count($yearList)-1];
						}
						else {
							$newCar['year'] = implode(', ', $yearList);
						}
					}
					$newPieceCode['cars'][] = $newCar;
				} // end foreach cars

				$newPiece['codes'][] = $newPieceCode;
			} // end foreach code
			$result[] = $newPiece;
		} // end foreach name

		Cache::forever('economyFrontData', $result);
		return $result;
	}
}
