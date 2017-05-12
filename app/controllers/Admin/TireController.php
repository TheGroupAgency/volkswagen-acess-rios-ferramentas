<?php namespace Admin;

use Excel;

class TireController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$viewData = array(
			'type'		=> 'Pneus',
			'api' 		=> array(
				'paginate'	=> route('admin.api.sheet.paginate', 'tire')
			),
			'example'	=> asset('assets/examples/tire.zip')
		);
		return \Response::view('admin.sheetList', $viewData);
	}

	/**
	 * Proccess the zip file
	 * @return  boolean|string(error)
	 */
	public static function processFile($file) {
		$success = false;
		$error = null;
		$zip = new \ZipArchive;
		$extractFolder = storage_path() . "/temp/" . uniqid();
		
		// unzip file
		if (!$zip->open($file) === TRUE) return "Falha ao extrair o arquivo";
		$zip->extractTo($extractFolder);
		$zip->close();

		// search for the xls file
		$xlsFile = searchFileByExtension($extractFolder, array("xls", "xlsx"), true);
		if (empty($xlsFile)) return "Excel não encontrado";

		// search for the images folder
		// if (!is_dir($extractFolder . "/images")) return "Pasta 'images' não encontrada";

		Excel::selectSheetsByIndex(0)->load($extractFolder . "/" . $xlsFile, function($reader) use (&$success, $extractFolder, &$error) {
			$reader->noHeading();
			$reader->skip(1);
			$results = $reader->get();

			$imageList = array();
			$partialImagePath = "upload/" . date("Y_m");
			$imagePath = public_path() . "/" . $partialImagePath;

			// create temporary table
			\DB::statement('DROP TABLE IF EXISTS tire_temp');
			\DB::statement('CREATE TABLE tire_temp LIKE tire');

			\DB::beginTransaction();
			try {
				foreach ($results as $row) {
					// upload the image if its not been uploaded yet
					$image = null;
					
					if (trim($row[7]) != "") {
						if (!array_key_exists($row[7], $imageList)) {
							$imageExtractPath = $extractFolder . "/images/" . $row[7];

							if (!\File::exists($imageExtractPath)) {
								throw new \Exception("Imagem " . $row[7] . " não encontrada", 10);
							}
							else {
								$newFileName = getAvailableFileName($row[7], $imagePath);
								if (!\File::isDirectory($imagePath)) {
									\File::makeDirectory($imagePath);
								}
								\File::move($imageExtractPath, $imagePath . "/" . $newFileName);
								$imageList[$row[7]] = $partialImagePath . "/" . $newFileName;
							}
						}
					}
					$insertImage = trim($row[7]) != "" ? $imageList[$row[7]] : null;
					\DB::table('tire_temp')->insert(array (
						'vehicle'				=> $row[0],
						'size'					=> $row[1],
						'measure'				=> $row[2],
						'manufacturer'			=> $row[3],
						'model'					=> $row[4],
						'manufacturer_model'	=> $row[5],
						'code'					=> $row[6],
						'image'					=> $insertImage
					));
				}

				\DB::commit();
				$success = true;
			}
			catch (\Exception $e) {
				if ($e->getCode() == 10) {
					$error = $e->getMessage();
				}
				\Log::error($e);
				\DB::rollback();
			}
		});

		// delete the unziped folder
		\File::deleteDirectory($extractFolder);

		if ($success) {
			// change table with new data
			\Schema::drop('tire');
			\Schema::rename('tire_temp', 'tire');
			return true;
		}
		else {
			if (empty($error)) {
				return "Falha ao salvar no banco de dados, verifique a planilha";
			}
			else {
				return $error;
			}
		}
	}

	/**
	 * Upload received from frontend
	 * @return Response
	 */
	public function upload() {
		$fileInput = \Input::file('sheetFile');
		$uploadResponse = SheetController::saveFile($fileInput, "zip");

		if ($uploadResponse === false) {
			return \Redirect::route('admin.tire')
				->with('msgType', 'error')
				->with('msg', 'Arquivo no formato inválido');
		}

		$response = TireController::processFile($uploadResponse['filePath']);

		if ($response === true) {
			// save excel file in database
			\Sheet::where('type', 'TIRE')->update(array('current' => 0));
			$newSheet = new \Sheet;
			$newSheet->type = 'TIRE';
			$newSheet->current = 1;
			$newSheet->original_filename = $uploadResponse['originalFileName'];
			$newSheet->path = 'data_upload/' . $uploadResponse['newFileName'];
			$newSheet->save();

			return \Redirect::route('admin.tire')
				->with('msgType', 'success')
				->with('msg', 'Planilha enviada com sucesso');
		}
		else {
			\File::delete($uploadResponse['filePath']);
			return \Redirect::route('admin.tire')
				->with('msgType', 'error')
				->with('msg', $response);
		}
	}
}
