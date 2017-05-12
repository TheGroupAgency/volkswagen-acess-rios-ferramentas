function dateFormatter(value, row, index) {
	var utc = moment.utc(value).toDate();
	var localTime = moment(utc).format('DD/MM/YYYY HH:mm:ss');
	return localTime;
}
function operateFormatter(value, row, index) {
	var html = "";
	if (row.current == 1) {
		html = "<i title='Ativo' class='fa fa-flag fa-fw'></i>";
	}
	else {
		html = "<a href='javascript:void(0)' class='rollback' title='Voltar para esta versão'><i class='fa fa-undo fa-fw'></i></a>"
	}

	return html;
}


window.operateEvents = {
	'click .rollback': function (e, value, row, index) {
	    swal(
		{		
			title: "Você tem certeza que deseja voltar para essa versão?",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Sim, voltar!",
			cancelButtonText: "Cancelar",
			closeOnConfirm: false 
			
		}, 
		function(){
			console.log("OK");
			$.ajax({
				method: "POST",
				url: apiRollback,
				data: { _token: _token, id: row.id },
				success: function (data) {
					if (data == 1) {
						swal("Atualizado!", "Planilha marcada como ativa com sucesso.", "success");
					}
					else {
			    		swal("Falha ao voltar para esta planilha", "error");
					}
					$("#table-result").bootstrapTable('refresh', { silent: true });
			    }
			});
		});
	}
}