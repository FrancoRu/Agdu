$(function () {
	let counter = 1

	const rootChildren = $('#rootChildren')
	addFields(rootChildren)
	function sendFormData(formData) {
		$.ajax({
			url: '../app/main.php',
			method: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				try {
					const dataParse = JSON.parse(response)
					counter = 1
					if (dataParse.status === 200) {
						const CUIL = $('#CUIL').text().split(':')
						alert(
							`Formulario de beneficiario con CUIL ${
								CUIL[1] ?? ''
							} cargado correctamente`
						)
						window.location.href = 'https://agdu.org.ar/beneficios'
					} else {
						console.log(dataParse)
						chargeRoot(dataParse.html)
					}
				} catch (error) {
					console.error('Error al parsear la respuesta JSON:', error)
				} finally {
					chargeEvent()
				}
			},
			error: function (xhr, status, error) {
				console.error('Error al cargar la vista:', status, error)
				chargeEvent()
			},
		})
	}

	// Función para procesar el envío del formulario
	function handleSubmit(event) {
		event.preventDefault()
		const formData = new FormData($('#form')[0])
		sendFormData(formData)
	}

	// Configurar el evento de envío del formulario
	function chargeEvent() {
		$('#form').off('submit').on('submit', handleSubmit)
	}

	function addFields(rootChildren) {
		$.ajax({
			url: `../app/main.php?action=children&index=${counter}`,
			method: 'GET',
			success: function (data) {
				try {
					const dataParse = JSON.parse(data)
					rootChildren.append(dataParse.html)
					const inputFile = $(`#formFile-${counter}`)
					const inputName = $(`#name-${counter}`)
					const inputEduc = $(`#education-${counter}`)

					rootChildren
						.off('change', `[id^="formFile-"]`)
						.on('change', `[id^="formFile-"]`, function () {
							addFields(rootChildren)
						})

					rootChildren
						.off('change', `[id^="name-"]`)
						.on('change', `[id^="name-"]`, function () {
							if (inputName.val().length > 0) {
								inputFile.prop({
									disabled: false,
									required: 'required',
								})
								inputEduc.prop({
									disabled: false,
								})
							} else {
								inputFile.prop({ disabled: true })
								inputEduc.prop({
									disabled: true,
								})
								if (inputFile.prop('disabled')) {
									inputFile.removeAttr('required')
								}
							}
						})
					counter++
				} catch (error) {
					console.error('Error al parsear la respuesta JSON:', error)
				} finally {
					chargeEvent()
				}
			},
			error: function (xhr, status, error) {
				console.error('Error al cargar la vista:', status, error)
				chargeEvent()
			},
		})
	}

	chargeEvent()
})
