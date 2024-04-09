$(function () {
  $('#btn_logout').on('click', function (event) {
    event.preventDefault()
    $.ajax({
      url: '../app/main.php',
      method: 'GET',
      data: {
        action: 'logout'
      },
      success: function (response) {
        const result = JSON.parse(response)
        console.log(result)
        if (result.status) {
          console.log(result)
          chargeRoot(result.html)
          addEvent()
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
