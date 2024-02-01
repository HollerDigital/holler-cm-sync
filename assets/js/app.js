jQuery(document).ready(function($) {
		
		 $('#holler-signup').submit(function(event) {
      // stop the form from submitting the normal way and refreshing the page
      event.preventDefault();
      
      var data = {
  			'action': 'holler_cm_subscribe_email',
  			'email': $('input[name=email]').val(),
  			'name': $('input[name=name]').val()
  		};
      console.log('Sending', data)
      // process the form
          jQuery.ajax({
              type: "post",
              dataType: "json",
              url: my_ajax_object.ajax_url,
              data: data,
          }).done(function(data) {
            console.log('return', data)
            // here we will handle errors and validation messages
            if (data.success !== true) {
                console.log('small fail');
                //$('.holler-signup').toggle();
                $('.msg').html('<p>Error Please try again</p>')
                $('.msg').show();
            } else {
              console.log("All Good")
                // ALL GOOD! just show the success message!
                $('.holler-signup').toggle();
                $('.msg').html('<p>Success! Please check your email and confirm your subscription</p>')
                $('.msg').show();
            }
          })
          .fail(function(data) {
              console.log('full fail', data);
              //$('.holler-signup').toggle();
              $('.msg').html('<p>Error Please try again</p>')
              $('.msg').show();
          });
          
		
		});
		
	});
	
	
/*
function validateEmail($email) {
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    return emailReg.test( $email );
  }

  $('.email__field').bind('input propertychange', function(e){
    //console.log("init");
    var emailaddress = $(this).val();
    if( emailaddress.length > 0 && validateEmail(emailaddress)) {
      $('.newsletter__form__extra').addClass("is__active");
    }
  });
  
  $( ".form__field__input" ).on('focus', function(e) {
    $(this).parent().parent().addClass("focused")
  });
  
  $( ".form__field__input" ).on('focusout', function(e) {
    var value = $(this).val();
    if(value.length <= 0 ){
      $(this).parent().parent().removeClass("focused")
    }
  });
*/

