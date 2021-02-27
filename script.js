$(document).ready(() => {
	$('#documentacao').on('click', () => {
		//$('#pagina').load('documentacao.html')
		/*
		$.get('documentacao.html', data => {
		$('#pagina').html(data)
		})*/

		$.post('documentacao.html', data => {
		$('#pagina').html(data)
		})
	})

	

	$('#suporte').on('click', () => {
		//$('#pagina').load('suporte.html')
		/*
		$.get('suporte.html', data => {
		$('#pagina').html(data)
		})*/

		$.post('suporte.html', data => {
		$('#pagina').html(data)
		})
	})

	//ajax
	$('#competencia').on('change', e => {
		
		let competencia = $(e.target).val()

		$.ajax({
			type: 'GET',
			url:  'app.php',
			data: `competencia=${competencia}`, //x-www-form-urlencoded
			dataType: 'json',
			success: dados => {
				$('#numeroVendas').html(dados.numeroVendas) 
				$('#totalVendas').html(dados.totalVendas)
				$('#cli').html(dados.cli)
				$('#inativo').html(dados.inativo)
				$('#despesa').html(dados.despesa)
				$('#reclamacoes').html(dados.reclamacoes)
				$('#elogio').html(dados.elogio)
				$('#sugestao').html(dados.sugestao)
			},
			error: erro => {console.log(erro)}
		})
	
	})	

	
})