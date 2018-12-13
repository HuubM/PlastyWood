<div class="form-group">
	<input type="text" name="fname" id="fname" tabindex="1" class="form-control" placeholder="First name" value="">
</div>
<div class="form-group">
	<input type="text" name="lname" id="lname" tabindex="1" class="form-control" placeholder="Last name" value="">
</div>
<div class="form-group">
	<input type="email" name="email" id="email" tabindex="1" class="form-control" placeholder="Email Address" value="">
</div>
<div class="form-group">
	<input type="password" name="password" id="password2" tabindex="2" class="form-control" placeholder="Password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" >
</div>
<div class="form-group">
	<input type="password" name="confirm-password" id="confirm-password" tabindex="2" class="form-control" placeholder="Confirm Password"  required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}">
</div>
<div class="form-group">
	<div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<input type="button" name="register-submit" id="register-submit" tabindex="4" class="form-control btn btn-register" value="Register Now">
		</div>
	</div>
</div>

<div id="message">
  <h3>Password must contain the following:</h3>
  <p id="letter" class="invalid">A <b>lowercase</b> letter</p>
  <p id="capital" class="invalid">A <b>capital (uppercase)</b> letter</p>
  <p id="number" class="invalid">A <b>number</b></p>
  <p id="length" class="invalid">Minimum <b>8 characters</b></p>
</div>

<script>
var myInput = document.getElementById("password2");
var letter = document.getElementById("letter");
var capital = document.getElementById("capital");
var number = document.getElementById("number");
var length = document.getElementById("length");

// When the user clicks on the password field, show the message box
myInput.onfocus = function() {
  document.getElementById("message").style.display = "block";
}

// When the user clicks outside of the password field, hide the message box
myInput.onblur = function() {
  document.getElementById("message").style.display = "none";
}

// When the user starts to type something inside the password field
myInput.onkeyup = function() {
  // Validate lowercase letters
  var lowerCaseLetters = /[a-z]/g;
  if(myInput.value.match(lowerCaseLetters)) {
    letter.classList.remove("invalid");
    letter.classList.add("valid");
  } else {
    letter.classList.remove("valid");
    letter.classList.add("invalid");
}

  // Validate capital letters
  var upperCaseLetters = /[A-Z]/g;
  if(myInput.value.match(upperCaseLetters)) {
    capital.classList.remove("invalid");
    capital.classList.add("valid");
  } else {
    capital.classList.remove("valid");
    capital.classList.add("invalid");
  }

  // Validate numbers
  var numbers = /[0-9]/g;
  if(myInput.value.match(numbers)) {
    number.classList.remove("invalid");
    number.classList.add("valid");
  } else {
    number.classList.remove("valid");
    number.classList.add("invalid");
  }

  // Validate length
  if(myInput.value.length >= 8) {
    length.classList.remove("invalid");
    length.classList.add("valid");
  } else {
    length.classList.remove("valid");
    length.classList.add("invalid");
  }
}
</script>

<script type="text/javascript">

	$(function() {
		$("#register-submit").click(function(){
			if($("#fname").val() != "" && $("#lname").val() != "" && $("#email").val() != "" && validatePassword($("#password2").val()) && validateEmail($("#email").val())){
				if($("#password2").val() === $("#confirm-password").val()){
					$.ajax({
					  method: "POST",
					  url: "<?=registerfile?>",
					  data: { fname: $("#fname").val(), lname: $("#lname").val(), email: $("#email").val(), password: $("#password2").val() }
					}).done(function( msg ) {
					   	alert(msg);
					});
				}else{
					alert("Passwords do not match!");
				}
				
			}else{
				alert("Please fill all fields with valid data!");
			}
		});
	});

</script>

