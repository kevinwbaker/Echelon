<?php
$b3_conn = false;
$auth_user_here = false;
$pagination = false;
require 'inc.php';

if($mem->loggedIn()) { // if logged don't allow the user to register
	set_error('Logged in users cannot register');
	sendHome(); // send to the index/home page
}


if(!isset($_REQUEST['key'])) { 
	// if key does not exists
	$step = 1; // the user must input a matching key and email address

} else { // if key is sent
	
	// clean vars of unwanted materials
	$key = cleanvar($_REQUEST['key']);
	$email = cleanvar($_REQUEST['email']);
	
	// check the new email address is a valid email address
	if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {  
		set_error('That email is not valid.');
	}
	
	// query db to see if key and email are valid
	$valid_key = $dbl->verifyRegKey($key, $email, $key_expire);
	if($valid_key == true) { // if the key sent is a valid one 
		$step = 2;
	} else {
		$step = 1;
		set_error('The key or email you submitted are not valid.');
	}

}

// basic page setup
$page = "register";
$page_title = "Register";
require 'inc/header.php';
if($step == 1) : // if not key is sent ask for one
?>
<fieldset>
	<legend>How can I get an account?</legend>
	<p class="reg"><strong>To register you must contact the site admins.</strong> Registration links are created by the site admins. <br>If you forgot your password, you may ask the site admins to create a new account for you.</p>
	
</fieldset>

<?php else : ?>
<div class="container">
  <div class="col-sm-9 col-lg-7 mx-auto">
    <div class="card card-signin my-5">
      <div class="card-header">
      <h5 class="">Register</h5>

	<small>To finish your registration you need to setup your account. Please fill in all these boxes with corrent information.</small>
    </div>

    <div class="card-body">
	<form action="actions/setup-user.php" method="post" id="reg-setup">
        <?php errors(); ?>
<div class="col justify-center">        
        <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="uname-check" title="The name you will use to login.">Username</label>
            <div class="col-sm-8">
            <input type="text" name="username" id="uname-check" class="form-control" required autofocus>
        </div></div>
            <div class="form-group row">
				<label class="col-sm-4 col-form-label" for="display">Display Name</label>
					<div class="col-sm-8"><input class="form-control" type="text" name="display" id="display"/></div>
            </div>
            <hr>
            <div class="form-group row">                
                <label class="col-sm-4 col-form-label" for="pw1">Password</label>
                <div class="col-sm-8"><input class="form-control" type="password" name="pw1" id="pw1"></div>
            </div>
            <div class="form-group row">
                <label for="pw2" class="col-sm-4 col-form-label">Verify Password</label>
                <div class="col-sm-8"><input class="form-control" type="password" name="pw2" id="pw2"></div>
            </div>
            <small id="passwordHelpInline" class="text-muted">Your password must be 8-20 characters long and must not contain spaces, special characters, or emoji.</small>
</div>
			
		<input type="hidden" name="key" value="<?php echo $key; ?>" id="key" />
		<input type="hidden" name="email" value="<?php echo $email; ?>" />
			
		<button class="btn btn-primary float-right my-2" type="submit">Register</button>
	
	</form>
</div></div></div></div>
<?php endif; ?>

<?php require 'inc/footer.php'; ?>