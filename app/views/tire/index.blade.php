<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Volkswagen - Pneus</title>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('assets/components/bootstrap/css/bootstrap.min.css') }}" >
		
		<link rel="stylesheet" href="{{ asset('assets/css/global.css') }}" >
		<link rel="stylesheet" href="{{ asset('assets/css/tire.css') }}" >

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

		<script type="text/javascript">
			var TIRE_URL = '{{ route('tire.index') }}';
			var _token = '{{ csrf_token() }}';
			var BASE_URL = '{{ route('home') }}';
		</script>
	</head>
	<body>
		<div class="container">
			<h1>Descubra o Pneu ideal para seu Volkswagen</h1>

			<div class="form-group">
				<label for="vehicle">Veículo</label>
				{{ Form::select('vehicle', array_merge(array('' => 'Selecione um veículo'), $vehicleSelect), null, array('class' => 'form-control', 'id' => 'vehicle')) }}
			</div>

			<div class="form-group size">
				<label for="size">Aro</label>
				{{ Form::select('size', array('' => 'Selecione um veículo'), null, array('class' => 'form-control', 'id' => 'size')) }}
			</div>
			
			<div class="item-tire item-clone">
				<p><strong>Modelo: </strong>%MODEL%</p>
				<p><strong>Medida: </strong>%MEASURE%</p>
				%IMAGE%
			</div>
			
			<div class="result">
				<div id="piece-list">
				</div>
				
				<p><strong>Consulte disponibilidade</strong></p>
				<p>
					<a class="btn btn-primary" target="_blank" href="http://app.volkswagen.com.br/dcc/pt/dealers.html"> Encontre uma concessionária</a>
						<a class="btn btn-default" id="sendTire"> Agendar serviço </a>
				</p>
			</div>
		</div>
		<!-- jQuery -->
		<script src="{{ asset('assets/js/jquery-1.12.0.min.js') }}"></script>

		<!-- Bootstrap JavaScript -->
		<script src="{{ asset('assets/components/bootstrap/js/bootstrap.min.js') }}"></script>
		
		<script src="{{ asset('assets/js/functions.js') }}"></script>
		<script src="{{ asset('assets/js/tire.js') }}"></script>
	</body>
</html>