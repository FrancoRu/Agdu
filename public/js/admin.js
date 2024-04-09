$(function () {
  $.ajax({
    url: '../app/main.php',
    method: 'POST',
    data: {
      action: 'panel'
    },
    success: function (response) {
      const result = JSON.parse(response)
      chargeRoot(result.html)
    },
    error: function (error) {
      console.error('Error en la solicitud AJAX', error)
    }
  })
})
