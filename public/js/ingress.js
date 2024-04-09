$(document).ready(function () {
  $('#form').submit(function (event) {
    event.preventDefault()

    const formData = $(this).serialize()

    $.ajax({
      url: '../app/main.php',
      method: 'GET',
      data: formData,
      success: function (response) {
        const result = JSON.parse(response)
        if (result.status) {
          chargeRoot(result.html)
        } else {
          console.log('error al cargar la vista')
        }
      },
      error: function (error) {
        console.error('Error en la solicitud AJAX', error)
      }
    })
  })
})
