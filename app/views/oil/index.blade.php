<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Volkswagen - Óleos</title>

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('assets/components/bootstrap/css/bootstrap.min.css') }}" >

	<link rel="stylesheet" href="{{ asset('assets/css/global.css') }}" >
	<link rel="stylesheet" href="{{ asset('assets/css/oil.css') }}" >

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
			<![endif]-->

			<script type="text/javascript">
			// var TIRE_URL = '{{ route('tire.index') }}';
			var _token = '{{ csrf_token() }}';
			var vehicleList = {{ json_encode($vehicleList) }};
			var BASE_URL = '{{ route('home') }}';
		</script>
	</head>
	<body>
		<div class="container">
			<h1>Descubra o óleo ideal para o seu Volkswagen</h1>

			<!-- TABELA -->
			<div class="form-group">
				<label for="vehicle">Veículo</label>
				{{ Form::select('vehicle', array_merge(array('' => 'Selecione um veículo'), $vehicleSelect), null, array('class' => 'form-control', 'id' => 'vehicle')) }}
			</div>

			<div class="form-group year">
				<label for="year">Ano / Modelo</label>
				{{ Form::select('year', array('' => 'Selecione um veículo'), null, array('class' => 'form-control', 'id' => 'year')) }}
			</div>
			
			<div class="form-group motor">
				<label for="motor">Motorização</label>
				{{ Form::select('motor', array('' => 'Selecione um veículo'), null, array('class' => 'form-control', 'id' => 'motor')) }}
			</div>
			<input type="text" name="oilList">
			<input type="text" name="codeOilList">
			
			<div class="pieceClone pieceItem">
				<p><strong>%PIECE_NAME%</strong></p>
				<p>%VW_STANDARD%</p>
				%PDF_FILE%
				%OBSERVATION%
			</div>
			
			<div class="result">
				<p><strong>O Óleo ideal para seu Volkswagen é:</strong></p>
				<div class="table-result"></div>
				
				<p><strong>Consulte disponibilidade</strong></p>
				<p>
					<a class="btn btn-primary" target="_blank" href="http://app.volkswagen.com.br/dcc/pt/dealers.html"> Encontre uma concessionária</a>
					<a class="btn btn-info" target="_blank" id="sendOil"> Agendar serviço </a>
				</p>
			</div>
		</div>
		<!-- jQuery -->
		<script src="{{ asset('assets/js/jquery-1.12.0.min.js') }}"></script>

		<!-- Bootstrap JavaScript -->
		<script src="{{ asset('assets/components/bootstrap/js/bootstrap.min.js') }}"></script>
		
		<script src="{{ asset('assets/js/functions.js') }}"></script>
		<script src="{{ asset('assets/js/oil.js') }}"></script>
	</body>
	</html>