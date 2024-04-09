$(function () {
  $('.download').on('click', function (event) {
    event.preventDefault()
    const filePath = $(this).attr('target')
    const fileName = $(this).attr('name')
    const form = $(
      '<form action="../app/main.php" method="post" target="_blank"></form>'
    )
    form.append('<input type="hidden" name="action" value="download">')
    form.append(`<input type="hidden" name="filename" value="${fileName}">`)
    form.append(`<input type="hidden" name="filepath" value="${filePath}">`)

    // Adjuntar el formulario al cuerpo del documento y enviarlo
    $(document.body).append(form)
    form.submit()

    // Eliminar el formulario después de enviar la solicitud
    form.remove()
  })

  $('#btn_download').on('click', function (event) {
    event.preventDefault()
    const form = $(
      '<form action="../app/main.php" method="post" target="_blank"></form>'
    )
    form.append('<input type="hidden" name="action" value="downloadXLSX">')

    // Adjuntar el formulario al cuerpo del documento y enviarlo
    $(document.body).append(form)
    form.submit()

    // Eliminar el formulario después de enviar la solicitud
    form.remove()
  })
})
