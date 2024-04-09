$(function () {
  $('#form').on('submit', (event) => {
    event.preventDefault()

    const formData = new FormData($('#form')[0])

    $.ajax({
      url: '../app/main.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        const result = JSON.parse(response)
        if (result.status) {
          chargeRoot(result.html)
        } else {
          console.log('Error loading the view')
        }
      },
      error: function (error) {
        console.error('Error in AJAX request', error)
      }
    })
  })
})
