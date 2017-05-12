<?php namespace Admin;

use Excel;

class EconomyController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$viewData = array(
			'type' => 'Economy',
			'api'  => array(
				'paginate'	=> route('admin.api.sheet.paginate', 'economy')
			),
			'example'	=> asset('assets/examples/economy.xlsx')
		);
		return \Response::view('admin.sheetList', $viewData);
	}

	/**
	 * Process a XLS file upload
	 *
	 * @return  boolean
	 */
	public static function processXls($filePath, $showPrice) {
		$success = false;
		Excel::selectSheetsByIndex(0)->load($filePath, function($reader) use (&$success, $showPrice) {
			$reader->noHeading();
			$reader->skip(1);
			$results = $reader->get();

			// create temporary table
			\DB::statement('DROP TABLE IF EXISTS economy_temp');
			\DB::statement('CREATE TABLE economy_temp LIKE economy');

			\DB::beginTransaction();
			try {
				foreach ($results as $row) {
					if (empty($row[0])) continue;
					$years = transformYearStringInArray($row[2]);
					
					// save one record per year
					foreach ($years as $year) {
						\DB::table('economy_temp')->insert(array (
							'name'                    => $row[0],
							'car_model'               => $row[1],
							'year'                    => $year,
							'economy_code'            => $row[3],
							'economy_price_public'    => number_format($row[4], 2),
							'unit_type'				  => $row[5],
							'show_price'			  => $showPrice
						));
					} // end foreach year
				} // end foreach result
				
				\DB::commit();
				$success = true;
			}
			catch (\Exception $e) {
				\Log::error($e);
				\DB::rollback();
			}
		});

		if ($success) {
			// change table with new data
			\Schema::drop('economy');
			\Schema::rename('economy_temp', 'economy');

			// remove cached table
			\Cache::forget('economyFrontData');
		}

		return $success;
	}

	/**
	 * Upload received from frontend
	 * @return Response
	 */
	public function upload() {
		$fileInput = \Input::file('sheetFile');
		$uploadResponse = SheetController::saveFile($fileInput, "excel");

		if ($uploadResponse === false) {
			return \Redirect::route('admin.economy')
				->with('msgType', 'error')
				->with('msg', 'Arquivo no formato inválido');
		}
		
		$showPrice = \Input::has('showPrice') ? 1 : 0;

		$success = EconomyController::processXls($uploadResponse['filePath'], $showPrice);

		if ($success) {
			// save excel file in database
			\Sheet::where('type', 'ECONOMY')->update(array('current' => 0));
			$newSheet = new \Sheet;
			$newSheet->type = 'ECONOMY';
			$newSheet->current = 1;
			$newSheet->original_filename = $uploadResponse['originalFileName'];
			$newSheet->path = 'data_upload/' . $uploadResponse['newFileName'];
			$newSheet->save();

			return \Redirect::route('admin.economy')
				->with('msgType', 'success')
				->with('msg', 'Planilha enviada com sucesso');
		}
		else {
			\File::delete($uploadResponse['filePath']);
			return \Redirect::route('admin.economy')
				->with('msgType', 'error')
				->with('msg', 'Falha ao salvar no banco, verifique se a planilha está no formato correto.');
		}
	}
}