<?php

class OilController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$vehicleList = Oil::select('vehicle')
			->groupBy('vehicle')
			->orderBy('vehicle', 'ASC')
			->whereNotNull('vehicle')
			->get();
		
		$vehicleSelect = array();
		foreach($vehicleList as $vehicle) {
			$yearList = array();
			$tempYearList = Oil::select('year')->where('vehicle', $vehicle->vehicle)->groupBy('year')->orderBy('year', 'ASC')->get();
			foreach($tempYearList as $year) {
				$newYear = ['year' => $year->year, 'motor' => []];
				$newYear['motor'] = Oil::select('motor')->where('vehicle', $vehicle->vehicle)->where('year', $year->year)->groupBy('motor')->orderBy('motor', 'ASC')->lists('motor');
				$yearList[] = $newYear;
			}
			$vehicle->yearList = $yearList;
			$vehicleSelect[$vehicle->vehicle] = $vehicle->vehicle;
		}
		
		
		// $pieceList = $this->getData();
		return Response::view('oil.index', array(
			'vehicleSelect' => $vehicleSelect, 
			// 'pieceList'      => $pieceList, 
			'vehicleList'   => $vehicleList
		));
	}
	
	public function getOil() {
		$vehicle = Input::get('vehicle');
		$year = Input::get('year');
		$motor = Input::get('motor');
		
		$result = Oil::select('oil_name', 'vw_standard', 'pdf', 'observation')
			->where('vehicle', $vehicle)
			->where('year', $year)
			->where('motor', $motor)
			->orderBy('id', 'ASC')
			->get();

		return Response::json($result);
	}

	public function sendOil(){ 

	//	error_reporting(E_ALL);

	$dataOil =  Input::get('dataOil');
			

	include 'Crypt/TripleDES.php';
 
 //instância objeto TripleDes
     $des = new Crypt_TripleDES();
 

 //gera chave randomica criptograda encodada para hexadecimal 
 	$randomKey = openssl_random_pseudo_bytes(24); 

 	//define a chave gerada como chave do objeto TripleDes

    $des->setKey($randomKey); 
    $des->setIV($randomKey); 
 	$des->encrypt($dataOil);


	$content = $des->encrypt(utf8_encode($dataOil));	
	$datadec = $des->decrypt($des->encrypt($dataOil));


    $path_cert = file_get_contents(dirname(__FILE__).'\localhost2.cer');
    //recupera chave gerada pelo certificado público ,
	$public_key = openssl_pkey_get_public($path_cert);
	
	//$data = file_get_contents($argv[1]);

	$decKey = $edc = null; 
	$sealed = $e = NULL;
	$desealed = $des = NULL;

	//criptografa a chave gerada para o tripleDes usando a chave pública do certificado
	openssl_public_encrypt($randomKey, $sealed, $public_key, OPENSSL_PKCS1_OAEP_PADDING); 

	///////////////////
	//criptografa a chave gerada para o tripleDes usando a chave pública do certificado para testes
	/////////////

	//	openssl_public_encrypt($randomKey, $sealed, "-----BEGIN PUBLIC KEY-----
	//MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAihP3H3KuW59FJo4hCL7p
	//wTJ4VH5OCFSg65MQj/D+eTKDkiiGhteP2ZD76qz6Jq0j8j2fZaT86g4IyrpRq1DD
	//QIUyWaDVABbg0VRr4ksuaJqn8M3+0VUVRh6FwUDuL8mp81ajoKop4zsl7urU0rak
	//G4DkCXHUPRZCNP9BaODlux4iS5xeSIXj7b+IYoJWgf7EKNytkPHvu6uUNtjtPcH3
	//SDmoWNXkECrC0cUNKvtrzA7IrEjijXjHJOnJOi0av8pfnXIJt1tW5nAnja+9yQ+l
	//8egeJKlE25HbcsbJ8S9alwbtw3z8JfS1xLqMbopSRoB3VozGQNMxjHfi3iNFKBSb
	//nQIDAQAB
	//-----END PUBLIC KEY-----", OPENSSL_PKCS1_OAEP_PADDING); 

	///////////////////
	//decriptografa a chave gerada para o tripleDes usando a chave privada do certificado para testes
	/////////////
	
	//openssl_private_decrypt($sealed, $desealed, "-----BEGIN RSA PRIVATE KEY-----
	//MIIEpAIBAAKCAQEAihP3H3KuW59FJo4hCL7pwTJ4VH5OCFSg65MQj/D+eTKDkiiG
	//hteP2ZD76qz6Jq0j8j2fZaT86g4IyrpRq1DDQIUyWaDVABbg0VRr4ksuaJqn8M3+
	//0VUVRh6FwUDuL8mp81ajoKop4zsl7urU0rakG4DkCXHUPRZCNP9BaODlux4iS5xe
	//SIXj7b+IYoJWgf7EKNytkPHvu6uUNtjtPcH3SDmoWNXkECrC0cUNKvtrzA7IrEji
	//jXjHJOnJOi0av8pfnXIJt1tW5nAnja+9yQ+l8egeJKlE25HbcsbJ8S9alwbtw3z8
	//JfS1xLqMbopSRoB3VozGQNMxjHfi3iNFKBSbnQIDAQABAoIBAFmZ2yNtJ/gMwrLi
	//SE2Elevo9GzgCYMeZ853AKhnmrrLLNYLtkCKYa6thswis7GlaU4o7ubmybiYotvr
	//TFP/dLTJuoKu+4mzTvCH9/pBDzySH6kEA/eTXtrBs8pUeDxKgR6HemcoBUaPe24u
	//tSyHmLbATTpTw4Zgi1YYzjlgt7NURnPk2QiAuwoQy7hVF2rhULK8Wq0DO1Rbfo1V
	//O02DN+3hOB+GxsRfQasbIF7DvLfRW6v1zaN2DX59j0ibjaxEoftaqFq39SApdWzd
	//+8kMxqXQXhKZuvcYPSLQAY8e1dFjc72oLKpF0JSc9nWXVp9g5pX54ysu24NVWp4E
	//W797kAECgYEAxAppPwkD4XQrqK6nY1FCaZ0Y/0nN+44vrzDGBHGLe7u5cp/l8esi
	//NuQegK9qneQB7FcUCj+eW9HG1PSv648l2QyJtmLMC5zoEt1kyPmw2mYrnK9jp3YD
	//eceokNZMDqEwW2p6WtqS/3fkdKyCOZFZrsI4gYtmU58lpwDh70XmyuECgYEAtE8x
	//gNRAsCiafA9ZBnQBiHZ/N8Gpx1iv+4IBhWLa89ZRXZdtTrsNhL0gzBYG6Uk93O1G
	//muWey7TsjPyccsEx088lXZp7KmVHOoBnmwEEKpdZFlcUOI1QbUF62ZFZu2SjfrFz
	//QRAiXn8HuUJuHWLzglNpTk85lovAPFNXVRcCxD0CgYEAnVSNqp0dlC6ba0Df7YEl
	//f4UorpkYyq4N7teB08ccXi0GY8uXy1MdnlftcvFU62o1cOthlegAu0fb/sRw+udj
	//RJIfY1Re1WMTjTBI1lItcNlWXuUTYS4BFBb8XWDeoU8TdAo8L/YCyyv5GPnpeTIB
	//e5M7rhul/65VlXXL3S+ITYECgYAv13duFGNZmlxrIHNhp/QkLjTRKXyP3TNMi5E1
	//wCYSXsJWD/C73BvZJYmHPSz2Ry236ek6/kQlDstZGUm1lRdQeP0UGgImHJtKpXlY
	//AmwFlQTZTZ+a5bv6UU50XgGuHCTZX+IR2GMWKaR1/m9TMscjxFgDpDfGfywxlXdt
	//p15fjQKBgQCjnNGBS7QsaVZHiTJmYCy3P4tldLq0fSoNy+g+8DRXhcgSLzqWaAdG
	//AWE6lZE3SHsupl78J1hfWDr5u3lmhmuNnnb9Isyo3rAGsARrlI1DygeclcugIJd5
	//aOqjF8OddCpYRCiIGlIlVDCUi+QChFWbS2hU6VZ/d7TuiIq63VVSgQ==
	//-----END RSA PRIVATE KEY-----", OPENSSL_PKCS1_OAEP_PADDING); 

	$payload = base64_encode($sealed);
	$token = base64_encode($e[0]);
	$j = new stdClass();


	//inclui no objeto de retorno chave simétrica baseada na chave pública
	$j->key = $payload;

	//retorna conteúdo criptografado usando a chave siméstrica decriptada para o TriplDes.
	$j->value = base64_encode($content);
		
	//Adicinando a resposta json a chave simetrica decriptografada	
	//	$j->decryptKey = base64_encode($desealed);
	
	//Adicinando a resposta json quantidade de bytes da chave simetrica
	//	$j->decryptBytes = strlen($desealed);	
	
	///////////////////
	//decriptografando o conteudo através da gerada usando a chave privado do certificado para testes (simetrica) e decriptografa no tripleDes 
	/////////////	
	//	$desDec = new Crypt_TripleDES();
	
	//	Configurando a chave simetrica no metódo tripleDES para decriptografar
	//  $desDec->setKey($desealed); 
	
	//	Configurando o conteúdo à ser decriptografado no TripleDES
	// 	$contentDec = $desDec->decrypt($content);
	
	//	Adicionando a resposta json o conteudo decriptografado
	//	$j->contentDec = $contentDec;

	$json = json_encode($j);

		file_put_contents("sealed.json", $json);
		return Response::json($json); 
	}
	
	/**
	 * Format data for fill the table with all pieces
	 * @return array
	 */
	private function getData()
	{
		// check if is on cache and doesnt need to update
		// if (Cache::has('oilFrontData')) {
		// 	return Cache::get('oilFrontData');
		// }

		$result = array();
		// get piece list by name
		$pieceList = Oil::select('vw_standard', 'oil_name', 'oil_code', 'motor', 'pdf', 'observation')
			->whereNotNull('vehicle')
			->groupBy('oil_name', 'oil_code')
			->get();

		foreach($pieceList as $piece) {
			$newPieceCode = [];
			$newPieceCode['vw_standard'] = nl2br($piece->vw_standard);
			$newPieceCode['oil_code'] = nl2br($piece->oil_code);
			$newPieceCode['oil_name'] = nl2br($piece->oil_name);
			$newPieceCode['motor'] = nl2br($piece->motor);
			$newPieceCode['pdf'] = nl2br($piece->pdf);
			$newPieceCode['observation'] = nl2br($piece->observation);
			$newPieceCode['cars'] = [];

			// get cars with this piece
			$carList = Oil::select('vehicle', 'id')
				->whereNotNull('vehicle')
				->where('oil_name', $piece->oil_name)
				->where('oil_code', $piece->oil_code)
				->groupBy('vehicle')
				->orderBy('vehicle', 'ASC')
				->get();

			foreach($carList as $car) {
				$newCar = array();
				$newCar['name'] = $car->vehicle;

				// get years from this car
				// DB::enableQueryLog();
				$yearList = Oil::select('year')
					->where('oil_code', $piece->oil_code)
					->where('vehicle', $car->vehicle)
					->get()
					->lists('year');
				// print_r(DB::getQueryLog());
				// exit;

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

			// $newPiece[] = $newPieceCode;
			$result[] = $newPieceCode;
		} // end foreach code
		// $result[] = $newPieceCode;

		Cache::forever('oilFrontData', $result);
		return $result;
	}
}
