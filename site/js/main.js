$(function() {

    $('#login-form-link').click(function(e) {
		$("#login-form").delay(100).fadeIn(100);
 		$("#register-form").fadeOut(100);
		$('#register-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});

	$('#register-form-link').click(function(e) {
		$("#register-form").delay(100).fadeIn(100);
 		$("#login-form").fadeOut(100);
		$('#login-form-link').removeClass('active');
		$(this).addClass('active');
		e.preventDefault();
	});

});

function validateEmail($email) {
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,})?$/;
  	return emailReg.test( $email );
}

function validatePassword($pwd) {
	var emailReg = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
  	return emailReg.test( $pwd );
}

function validateName($name) {
	var emailReg = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/;
  	return emailReg.test( $name );
}

