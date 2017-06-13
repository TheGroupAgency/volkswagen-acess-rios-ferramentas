$(document).ready(function () {
    var yearSelect = $("select[name=year]");
    var vehicleSelect = $("select[name=vehicle]");
    var motorSelect = $("select[name=motor]");
    var tableResult = $(".table-result");
    var divResult = $(".result");
    var sendOil = $('#sendOil');
    
    vehicleSelect.change(function () {
    	divResult.hide();
        var vehicle = $(this).val();
		
		if (vehicle == "") {
			yearSelect.empty().html("<option value=''>Primeiro selecione um veículo</option>");
			motorSelect.empty().html("<option value=''>Primeiro selecione um veículo</option>");
			return;
		}

		var selectedModel = vehicleList.filter(function( obj ) {
			return obj.vehicle == vehicle;
		});
		selectedModel = selectedModel[0];

		yearSelect.empty().html("<option value=''>Selecione o ano</option>");
		motorSelect.empty().html("<option value=''>Selecione o ano</option>");
		
		for(var i = 0; i < selectedModel.yearList.length; i++) {
			yearSelect.append("<option value='" + selectedModel.yearList[i].year + "'>" + selectedModel.yearList[i].year + "</option>");
		}
    });
    
    yearSelect.change(function () {
        divResult.hide();
       var vehicle = vehicleSelect.val();
       var year = $(this).val()

		if (vehicle == "" || year == "") {
			return false;
		}
		
		var motorList = [];
		
		for (var i = 0; i < vehicleList.length; i++) {
		    var obj = vehicleList[i];
		    if (obj.vehicle != vehicle) continue;
		    
		    for (var x = 0; x < obj.yearList.length; x++) {
		        if (obj.yearList[x].year == year) {
		        	for (var z = 0; z < obj.yearList[x].motor.length; z++) {
		        		var motor = obj.yearList[x].motor[z];
		        		if (motorList.indexOf(motor) == -1) {
		        			motorList.push(motor);
		        		}
		        	}
		        	
		            
		        }
		    }
		}
		
		motorSelect.empty().html("<option value=''>Selecione o motor</option>");
		for(var i = 0; i < motorList.length; i++) {
			motorSelect.append("<option value='" + motorList[i] + "'>" + motorList[i] + "</option>");
		}
		
    });
    
    motorSelect.change(function () {
    	var vehicle = vehicleSelect.val();
		var year = yearSelect.val();
		var motor = $(this).val();
		
		if (vehicle == "" || year == "" || motor == "") {
			divResult.hide();
			return false;
		}
		
		$.ajax({
			method: "POST",
			url: BASE_URL + "/oil/api/getOil",
			data: { _token: _token, vehicle: vehicle, year: year, motor: motor},
			success: function (oilList) {
				tableResult.empty();
    	
		    	for (var i = 0; i < oilList.length; i++) {
		    		var cloned = $(".pieceClone").clone();
		    		cloned.removeClass('pieceClone');
		    		
		    		var html = cloned.prop('outerHTML');
		    		html = html.replace('%PIECE_NAME%', oilList[i].oil_name);
		    		html = html.replace('%VW_STANDARD%', oilList[i].vw_standard);
		    		
		    		$('body > div > input[type="text"]:nth-child(5)').val(oilList[i].oil_name); 
		    		$('body > div > input[type="text"]:nth-child(6)').val(oilList[i].vw_standard);


		    		if (oilList[i].pdf != null) {
		    			html = html.replace('%PDF_FILE%', '<p><a href="' + BASE_URL + '/' + oilList[i].pdf + '" target="_blank">Outras opções no mercado</a>');
		    		}
		    		else {
		    			html = html.replace('%PDF_FILE%', '');
		    		}
		    		
		    		if (oilList[i].observation != null) {
		    			html = html.replace('%OBSERVATION%', '<p class="observation">' + oilList[i].observation + '</p>');
		    		}
		    		else {
		    			html = html.replace('%OBSERVATION%', '');
		    		}
		    		
		    		
		    		tableResult.append(html);
		    	}
		    	
		    	divResult.show();
			}
		});
    });


    sendOil.click(function(){ 



var nomes = [];
var codigos = []; 

	$('.pieceItem').each(function(i, value){ 

			var nome = $("p:first", value).text();
			var codigo = $("p:nth-child(2)", value).text().replace('Cód. ','');

			if(nome.indexOf('%PIECE_NAME%') < 0) { 
				
				nomes.push(nome); 
				codigos.push(codigo); 

				}
    	});
	
    	
	    var dataOil = {

    		"ModeloVeiculo": vehicleSelect.val(),
    		"AnoVeiculo": parseInt(yearSelect.val()),
    		"autenticacao": { 
    			"usuario": "user_oleosfluidos",
    			"senha": "Hq97ryj&74w94rth46hrye"
    		},
    		"Oleo":{ 

    			nomes,
    			codigos,
    			 "combustivel": $('#motor').val()

    		}

    	}; 



/*
var dataOil = {
    "ModeloVeiculo" : "UP!",
    "AnoVeiculo" : 2017,
    "autenticacao" : {
        "usuario" : "user_oleosfluidos",
        "senha" : "Hq97ryj&74w94rth46hrye"
    },
    "Oleo" : {
        "nomes" : ["Volkswagen Maxi Performance - Norma VW 508 88", "Volkswagen Maxi Performance - Norma VW 508 88"],
        "codigos" : ["SAE 5W-40", "SAE 5W-40"],
        "combustivel": $('#motor').val()
    }
};
*/

		$.ajax({
			method: "POST",
			url: "/crypt",
			data: { _token: _token, data: JSON.stringify(dataOil)},
			success: function (response) {
				console.log(response); 

				sendData(response); 
			} 
		});
    });
});


var sendData = function(data){ 

		$.ajax({
			method: "POST",		
			url: "https://vwcads.volkswagen.com.br/agendamento/api/seguranca/autenticar",
			//data: {"key":"gflM3k7lW5wHrMaxlpWhbP3w2fj\/FPDL7e0t0FJLDVL++\/hKeww1nBdykdD9bws4IzTjOfNjhdqvwffZZmE27ZnumwiqB0\/tsx9dRvOfE5BJ52psoHAmWW\/gQes2FEoqvrPx\/KgiXnCD\/SxYJs7xt1lFcajjr4GcQLJ12o0ZplyR43fG8wPdlob1nbzgn7XRL4pMa9bYX1t0sgkG\/t\/2PAMw9574\/yFgtS+e35NfRraU+dJxs6T\/HaV7tmPzwLK89dQu\/LRmgXdi36ClLhhnHwC0xu4kzMCwPvN+hUBmSm23c4NayZZDM3vx6RrLAPrSzkG9AvFAyzuidIuNSJlsrA==","content":"tz7\/mGl8Pz7ZwDWvuvqkfHELxZcHDa0JjNR8hCKeQrYGNJebrkTM\/+x9vAnzUg1Wd8GACqYzX\/Bj7MeCkjDnGKKjKyo\/DlmuCNGAWSKyYQuWJsIrDsyCUdvRNeRUBxixM1ju5xFF0VEmckldaVsTxUrvXbtGFjZIQd9cEMTfjghLxxbLgIO\/sz7Gt8PBBZUWtC1mde2PFJsiXEASjzgj72XC7NehkVDcHKKZRruZ6xIh5NV+bPA3SabT\/sq19T9FtHuuy24jhZoYhI5f2\/DC3Lxx4IcZQMgf66be3uEq2CEJqV1S0Z33Fm\/0wkwhUk6SDHzYFrqDPMffKkcxpQdLRLxCayJ4yYgkJJhnnS9nnCk="},
			data: JSON.parse(data),
			success: function (response) {
				if(response.processamento){
					var url = response.url + '?id=' + response.identificador; 
					window.location.href = url; 
					//sendData(response); 	
				}else{
					alert("Ocorreu uma falha ao iniciar o seu agendamento, tente novamente.")
				}
			},
			error:function(err){ 

				console.log(err)

			}
		});


}