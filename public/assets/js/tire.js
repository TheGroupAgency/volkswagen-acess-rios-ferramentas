$(document).ready(function () {
	var vehicleSelect = $("#vehicle");
	var sizeSelect = $("#size");
	var pieceList = $("#piece-list");
	var sendTire = $('#sendTire');


	vehicleSelect.change(function () {
		$(".result").hide();
		pieceList.find('.item:not(.item-clone)').remove();
		var vehicleValue = $(this).val();

		if (vehicleValue == "") {
			sizeSelect.empty().append("<option value=''>Selecione um veículo</option>");
			$("#year").trigger('change');
			return false;
		}

		sizeSelect.empty().append("<option value=''>Carregando...</option>");

		$.ajax({
			method: "POST",
			url: TIRE_URL + "/api/getVehicleTireSize",
			data: { _token: _token, vehicle: vehicleValue },
			success: function (data) {
				sizeSelect.empty().append("<option value=''>Selecione um aro</option>");
				$.each(data, function (key, year) {
					sizeSelect.append("<option value='" + year + "'>" + year + "</option>");
				});
		    }
		});
	});

	sizeSelect.change(function () {

		pieceList.find('.item:not(.item-clone)').remove();
		var vehicleValue = vehicleSelect.val();
		var sizeValue = sizeSelect.val();
		
		pieceList.empty();

		$.ajax({
			method: "POST",
			url: TIRE_URL + "/api/getVehicleTire",
			data: { _token: _token, vehicle: vehicleValue, size: sizeValue},
			success: function (data) {
				$(".result").show();
				$.each(data, function (key, piece) {
					
					var clone = $(".item-clone").clone();
					clone.removeClass('item-clone');
					
					var html = clone.prop('outerHTML');

					if (piece.image != null) {
						html = html.replace('%IMAGE%', '<img class="img-responsive img-tire" src="' + BASE_URL + "/" + piece.image + '">');
					}
					else {
						html = html.replace('%IMAGE%', '');
					}
					
					html = html.replace('%MODEL%', piece.manufacturer_model);
					html = html.replace('%MEASURE%', piece.measure);
					pieceList.append(html);
				});
		    }
		});
	});


    sendTire.click(function(){ 

    	

    	var modelo = [];

    	var medida = []; 

		$('.item-tire').each(function(i, value){ 

			var text = $("p:first", value).text();

			//console.log(text + ' /' + $("p:nth-child(2)", value).text()); 


			if(text.indexOf('%MODEL%') < 0) { 
				
				modelo.push($("p:first", value).text().replace('Modelo: ','')); 
				medida.push($("p:nth-child(2)", value).text().replace('Medida: ','')); 

				}
				
    	});


    	var dataTire = { 

    		"ModeloVeiculo": vehicleSelect.val(),
    		"AnoVeiculo": 0,
    		"autenticacao": { 
    			"usuario": "user_pneus",
    			"senha": "HJdc023rr47hG@(rhd4"
    		},
    		"Pneu":{ 

    			"aro":$('#size').val(),
    			modelo,
    			medida
    			
    		}

    	}
/*
    var dataTire= 	{
    "ModeloVeiculo " : "Amarok",
    "AnoVeiculo" : 0,
    "autenticacao" : {
        "usuario" : "user_pneus",
        "senha" : "HJdc023rr47hG@(rhd4"
    },
    "Pneu" : {
        "aro" : 19,
        "modelo" : ["Bridgestone Dueler H/T 684", " Pirelli Scorpion Verde All Season"],
        "medida" : ["255/55", "255/55"]
    }
}
*/
console.log(dataTire)

		$.ajax({
			method: "POST",
			url: "/crypt",
			data: { _token: _token, data: JSON.stringify(dataTire)},
			success: function (response) {
					console.log(response); 

				sendData(response); 
			} 
		});
    });
})

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