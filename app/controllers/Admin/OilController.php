<?php namespace Admin;

use Excel;

class OilController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$viewData = array(
			'type'		=> 'Óleos',
			'api' 		=> array(
				'paginate'	=> route('admin.api.sheet.paginate', 'oil')
			),
			'example'	=> asset('assets/examples/oil.zip')
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

			$fileList = array();
			$partialFilePath = "upload/" . date("Y_m");
			$filePath = public_path() . "/" . $partialFilePath;

			// create temporary table
			\DB::statement('DROP TABLE IF EXISTS oil_temp');
			\DB::statement('CREATE TABLE oil_temp LIKE oil');

			\DB::beginTransaction();
			try {
				foreach ($results as $row) {
					// upload the file if its not been uploaded yet
					$file = null;
					
					if (trim($row[7]) != "") {
						if (!array_key_exists($row[7], $fileList)) {
							$fileExtractPath = $extractFolder . "/pdfs/" . $row[7];

							if (!\File::exists($fileExtractPath)) {
								throw new \Exception("Arquivo " . $row[7] . " não encontrado", 10);
							}
							else {
								
								$newFileName = getAvailableFileName($row[7], $filePath);
								if (!\File::isDirectory($filePath)) {
									\File::makeDirectory($filePath);
								}
								\File::move($fileExtractPath, $filePath . "/" . $newFileName);
								$fileList[$row[7]] = $partialFilePath . "/" . $newFileName;
								$file = $fileList[$row[7]];
							}
						}
						else {
							$file = $fileList[$row[7]];
						}
					}

					$years = transformYearStringInArray($row[3]);

					// save one record per year
					foreach($years as $year) {
						\DB::table('oil_temp')->insert(array (
					       'vehicle'  => $row[2],
					       'year'     => $year,
					       'vw_standard' => $row[1],
					       'oil_name' => $row[0],
					       'oil_code' => $row[5],
					       'pdf'      => $file,
					       'motor'    => $row[4],
					       'observation'    => $row[6]
				       	));
					}
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
			\Schema::drop('oil');
			\Schema::rename('oil_temp', 'oil');
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
			return \Redirect::route('admin.oil')
				->with('msgType', 'error')
				->with('msg', 'Arquivo no formato inválido');
		}

		$response = OilController::processFile($uploadResponse['filePath']);

		if ($response === true) {
			// save excel file in database
			\Sheet::where('type', 'OIL')->update(array('current' => 0));
			$newSheet = new \Sheet;
			$newSheet->type = 'OIL';
			$newSheet->current = 1;
			$newSheet->original_filename = $uploadResponse['originalFileName'];
			$newSheet->path = 'data_upload/' . $uploadResponse['newFileName'];
			$newSheet->save();

			return \Redirect::route('admin.oil')
				->with('msgType', 'success')
				->with('msg', 'Planilha enviada com sucesso');
		}
		else {
			\File::delete($uploadResponse['filePath']);
			return \Redirect::route('admin.oil')
				->with('msgType', 'error')
				->with('msg', $response);
		}
	}
}