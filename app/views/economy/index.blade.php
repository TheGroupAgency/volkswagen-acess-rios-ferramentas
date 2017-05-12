<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Volkswagen - Linha Economy</title>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('assets/components/bootstrap/css/bootstrap.min.css') }}" >
		
		<link rel="stylesheet" href="{{ asset('assets/css/global.css') }}" >
		<link rel="stylesheet" href="{{ asset('assets/css/economy.css') }}" >

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

		<script type="text/javascript">
			var ECONOMY_URL = '{{ route('economy.index') }}';
			var carModelList = {{ json_encode($carModelList) }};
			var pieceList = {{ json_encode($pieceList) }};
			var _token = '{{ csrf_token() }}';
		</script>
	</head>
	<body>
		<div class="container">
			<h1>Verifique a disponibilidade de peças Economy para seu Volkswagen</h1>
			<form action="" method="POST" role="form">
				<div class="form-group">
					<label for="">Veículo</label>
					{{ Form::select('model_code', array_merge(array('' => 'Selecione um veículo'), $carModelSelect), null, array('class' => 'form-control', 'id' => 'vehicle')) }}
				</div>

				<div class="form-group yearSelectGroup">
					<label for="">Ano / Modelo</label>
					{{ Form::select('year', array('' => 'Primeiro selecione um veículo'), null, array('class' => 'form-control')) }}
				</div>
				<!-- <button type="submit" class="btn btn-primary">Submit</button> -->
			</form>

			<div class="result">
				Peças Economy disponíveis para seu <span class="carName carNameReplace">Golf</span>:
				
				<div class="pieceClone pieceItem">
					<p><strong>%PIECE_NAME%</strong></p>
					<p>Cód. %PIECE_CODE%</p>
					%PIECE_PRICE%
				</div>
				<div class="pieceList">
					
				</div>
				
				<p><strong>Consulte disponibilidade</strong></p>
				<p>
					<a class="btn btn-primary btn-search" target="_blank" href="http://app.volkswagen.com.br/dcc/pt/dealers.html"> Encontre uma concessionária</a>
						<a class="btn btn-primary btn-default" id="sendEconomy"> Agendar serviço</a>
				</p>
			</div>
			
			<div class="notFound">
				Nenhuma peça encontrada.
			</div>
		</div>
		<!-- jQuery -->
		<script src="{{ asset('assets/js/jquery-1.12.0.min.js') }}"></script>

		<!-- Bootstrap JavaScript -->
		<script src="{{ asset('assets/components/bootstrap/js/bootstrap.min.js') }}"></script>
		
		<script src="{{ asset('assets/js/functions.js') }}"></script>
		<script src="{{ asset('assets/js/economy.js') }}"></script>
	</body>
</html>