jQuery(function($) {

  $('#pbs_media_manager_lookup_form button').click(function(event) {
    event.preventDefault();
    var endpoint = $('#pbs_media_manager_lookup_form select').val();
    var id = $('#pbs_media_manager_lookup_form input').val();
 
    var $button = $(this);

    $button.html('Processing...');
    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        'action': 'pbs_media_manager_lookup',
        'nonce' : pbs_media_manager_lookup_nonce, 
        'endpoint' : endpoint,
        'id' : id
      },
      dataType: 'json'
    }).always(function(response) {
      console.log(response);
      $button.html('Complete');
      var output = JSON.stringify(response, null, 2)
      $('#pbs_media_manager_lookup_form .mm_response').html(output); 
    });
  });
 	
});
