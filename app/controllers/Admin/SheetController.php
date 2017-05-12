<?php namespace Admin;

class SheetController extends \BaseController {

	/**
	 * Display a listing of the resource (used for ajax query)
	 *
	 * @return Response
	 */
	public function paginate($type)
	{
		$type = strtoupper($type);
		$limit = \Input::get('limit');
		$offset = \Input::get('offset');
		$sort = \Input::get('sort');
		$order = \Input::get('order');
		
		$dataResponse = array();
		$dataResponse["total"] = \Sheet::where('type', $type)->count();
		$dataResult = \Sheet::where('type', $type)->take($limit)->skip($offset)->orderBy($sort, $order)->get();
		$dataResponse["rows"] = $dataResult;
		return \Response::json($dataResponse);
	}

	/**
	 * Rollback to an old sheet version 
	 */
	public function rollback() {
		$id = \Input::get('id');
		
		$sheet = \Sheet::find($id);
		if (empty($sheet) || $sheet->current == 1) {
			return 0;
		}
		$success = false;

		switch($sheet->type) {
			case "ECONOMY":
				$success = EconomyController::processXls(storage_path() . "/" . $sheet->path);
				break;
			case "TIRE":
				$success = TireController::processFile(storage_path() . "/" . $sheet->path);
			case "OIL":
				$success = OilController::processFile(storage_path() . "/" . $sheet->path);
		}

		if ($success === true) {
			// save excel file in database
			\Sheet::where('type', $sheet->type)->update(array('current' => 0));
			$sheet->current = 1;
			$sheet->save();

			return 1;
		}
		else {
			return 0;
		}
	}

	/**
	 * Check if the file is valid and upload
	 * @return  mixed
	 */
	public static function saveFile($fileInput, $type = "excel") {
		if (empty($fileInput)) return false;

		$allowedMimeType = array(
			'excel' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'),
			'zip'	=> array('application/zip', 'application/octet-stream')
		);

		if (!in_array($fileInput->getMimeType(), $allowedMimeType[$type])) return false;

		$newFileName = uniqid() . "." . $fileInput->getClientOriginalExtension();

		$fileMove = $fileInput->move(storage_path() . "/data_upload", $newFileName);
		$filePath = $fileMove->getPathname();

		return array(
			'originalFileName'	=> $fileInput->getClientOriginalName(),
			'newFileName'		=> $newFileName,
			'filePath'			=> storage_path() . "/data_upload/" . $newFileName
		);
	}
}
