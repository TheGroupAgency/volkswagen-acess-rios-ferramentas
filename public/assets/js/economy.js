$(document).ready(function () {
	var sendEconomy = $('#sendEconomy'); 


	// format yearList to int
	

	for (var a = 0; a < pieceList.length; a++) {
		
		var piece = pieceList[a];
		for (var b = 0; b < piece.codes.length; b++) {
			var pieceCode = piece.codes[b];
			for (var c = 0; c < pieceCode.cars.length; c++) {
				var pieceCar = pieceCode.cars[c];
				for (var d = 0; d < pieceCar.yearList.length; d++) {
					pieceCar.yearList[d] = parseInt(pieceCar.yearList[d]);
				}
			}
		}
	}
	
	$("select[name=model_code]").change(function () {
		$(".result").hide();
		$(".notFound").hide();
		
		var carModel = $(this).val();
		var yearSelect = $("select[name=year]");

		if (carModel == "") {
			yearSelect.empty().html("<option value=''>Primeiro selecione um veículo</option>");
			return;
		}

		var selectedModel = carModelList.filter(function( obj ) {
			return obj.car_model == carModel;
		});
		selectedModel = selectedModel[0];

		yearSelect.empty().html("<option value=''>Selecione o ano</option>");
		
		for(var i = 0; i < selectedModel.yearList.length; i++) {
			yearSelect.append("<option value='" + selectedModel.yearList[i] + "'>" + selectedModel.yearList[i] + "</option>");
		}
	});

	$("select[name=year]").change(function () {
		$(".result").hide();
		$(".notFound").hide();

		var carModel = $("select[name=model_code]").val();
		var carYear = $(this).val()

		if (carModel == "" || carYear == "") {
			return false;
		}
		
		var carPieceList = [];
		for (var i = 0; i < pieceList.length; i++) {
			var piece = pieceList[i];
			
			for (var x = 0; x < piece.codes.length; x++) {
				var pieceCode = piece.codes[x];
				
				for (var y = 0; y < pieceCode.cars.length; y++) {
					var car = pieceCode.cars[y];
					// console.log(car.yearList);
					// console.log(carYear);
					if (car.name == carModel && car.yearList.indexOf(parseInt(carYear)) != -1) {
						carPieceList.push({
							name: piece.name,
							economy_code: pieceCode.economy_code,
							economy_price_public: pieceCode.economy_price_public,
							unit_type: pieceCode.unit_type,
							show_price: pieceCode.show_price
						});
					}// end car check
				} // end for cars
			} // end for code
		} // end for piece
		
		var divResult = $(".result");
		var divPieceList = divResult.find(".pieceList");
		var divNotFound = $(".notFound");
			
		if (carPieceList.length > 0) {
			divPieceList.empty();
			divNotFound.hide();
			
			var cloneDiv = $(".pieceClone");
			
			
			divResult.find(".carNameReplace").first().html(carModel);
			
			for (var i = 0; i < carPieceList.length; i++) {
				var piece = carPieceList[i];
				var newDiv = cloneDiv.clone();
				
				var html = newDiv.html();
				html = html.replace("%PIECE_NAME%", piece.name);
				html = html.replace("%PIECE_CODE%", piece.economy_code);
				
				if (piece.show_price == 1) {
					html = html.replace("%PIECE_PRICE%", "<p>R$ " + piece.economy_price_public.replace(".", ",") + " " + piece.unit_type);
				}
				else {
					html = html.replace("%PIECE_PRICE%", "");
				}
				
				newDiv.html(html);
				newDiv.show();
				divPieceList.append(newDiv);
			}
			
			divResult.show();
		}
		else {
			divNotFound.show();
			divResult.hide();
		}
		return false;

		$(".table-piece").each(function () {
			// check if table piece has the model with selected year
			var carModelFind = $(this).find(".car-item[data-model='" + carModel + "']");
			if (carModelFind.length == 0 || carModelFind.data('year').indexOf(carYear) == -1) {
				$(this).hide();
				return;
			}

			var colName = $(this).find(".col-car-name");
			if (colName.html().trim() == "") {
				colName.html($(this).data('name'))
			}

			$(this).find(".car-item[data-model!='" + carModel + "']").hide();
		});
	});

	    sendEconomy.click(function(){ 


	   var nome = []; 
	   var codigo = []; 

    	$('.pieceItem').each(function(i, value){ 

			var text = $("p:first", value).text();

			if(text.indexOf('%PIECE_NAME%') < 0) { 
				
				nome.push($("p:first", value).text()); 
				codigo.push($("p:nth-child(2)", value).text().replace('Cód. ','')); 

				}
    	});


    	var dataParts = { 

    		"ModeloVeiculo": $('#vehicle').val(),
    		"AnoVeiculo": parseInt($("select[name=year]").val()),
    		"autenticacao": { 
    			"usuario": "user_pecaseconomy",
    			"senha": "HNdh*qe8fhHJFD427824yrh"
    		},
    		"Pecas":{ 

    			nome,
    			codigo
    		}

    	}

/*
    	var dataParts = {
    "veiculo" : "Polo",
    "anoModelo" : 2012,
    "autenticacao" : {
        "usuario" : "user_pecaseconomy",
        "senha" : "HNdh*qe8fhHJFD427824yrh"
    },
    "Pecas" : {
        "nome" : ["Aditivo do radiador", "Filtro de combustível", "Pastilha de freio dianteira", "Pastilha de freio dianteira"],
        "codigo" : ["G -JZW-012-R2", "JZW-201-511", "JZW-698-151", "JZW-698-151-AF"]
    }
}*/

console.log(dataParts)

		$.ajax({
			method: "POST",
			url: "/crypt",
			data: { _token: _token, data: JSON.stringify(dataParts)},
			success: function (response) {
				console.log(response)

				sendData(response)
			} 
		});
    });
});

function showFullTable() {
	// console.log("Showing full table");
	// // set all visible back
	// $(".table-piece").show();
	// $(".car-item").show();
	// $(".col-name-secondary").html("");

	// $(".col-car-year-model").show();
	// $(".col-car-year").show();
	// $(".col-car-model").show();

	// replaceBootstrapCol($(".col-car-name"), 6, 3);
	// replaceBootstrapCol($(".col-car-code"), 6, 3);
}

var sendData = function(data){ 

console.log('Enviado para VW');

console.log(data)
		$.ajax({
			method: "POST",		
			url: "https://vwcads.volkswagen.com.br/agendamento/api/seguranca/autenticar",
			//data: {"key":"gflM3k7lW5wHrMaxlpWhbP3w2fj\/FPDL7e0t0FJLDVL++\/hKeww1nBdykdD9bws4IzTjOfNjhdqvwffZZmE27ZnumwiqB0\/tsx9dRvOfE5BJ52psoHAmWW\/gQes2FEoqvrPx\/KgiXnCD\/SxYJs7xt1lFcajjr4GcQLJ12o0ZplyR43fG8wPdlob1nbzgn7XRL4pMa9bYX1t0sgkG\/t\/2PAMw9574\/yFgtS+e35NfRraU+dJxs6T\/HaV7tmPzwLK89dQu\/LRmgXdi36ClLhhnHwC0xu4kzMCwPvN+hUBmSm23c4NayZZDM3vx6RrLAPrSzkG9AvFAyzuidIuNSJlsrA==","content":"tz7\/mGl8Pz7ZwDWvuvqkfHELxZcHDa0JjNR8hCKeQrYGNJebrkTM\/+x9vAnzUg1Wd8GACqYzX\/Bj7MeCkjDnGKKjKyo\/DlmuCNGAWSKyYQuWJsIrDsyCUdvRNeRUBxixM1ju5xFF0VEmckldaVsTxUrvXbtGFjZIQd9cEMTfjghLxxbLgIO\/sz7Gt8PBBZUWtC1mde2PFJsiXEASjzgj72XC7NehkVDcHKKZRruZ6xIh5NV+bPA3SabT\/sq19T9FtHuuy24jhZoYhI5f2\/DC3Lxx4IcZQMgf66be3uEq2CEJqV1S0Z33Fm\/0wkwhUk6SDHzYFrqDPMffKkcxpQdLRLxCayJ4yYgkJJhnnS9nnCk="},
			data: JSON.parse(data),
			success: function (response) {
				console.log(response);
				var url = response.url + '/' + response.identificador; 
				window.location.href = url; 
				//sendData(response); 
			},
			error:function(err){ 

				console.log(err)

			}
		});


}