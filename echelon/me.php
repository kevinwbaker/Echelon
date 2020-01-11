<?php
$page = "me";
$page_title = "My Account";
$auth_name = 'login';
require 'inc.php';

require 'inc/header.php';
?>
    <div class="container my-2">
        <div class="card">
        <h5 class="card-header">Manage your account</h5>
            <div class="card-body">
        
		<form action="actions/edit-me.php" method="post" id="edit-me">
        
        <h6>Account Details</h6>
        <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Username</label>
					<div class="col-sm-8"><input class="form-control" type="text" name="uname" value="<?php echo $_SESSION['username']; ?>" disabled="disabled" /></div>
            </div>
            <div class="form-group row">
				<label class="col-sm-4 col-form-label" for="display">Display Name</label>
					<div class="col-sm-8"><input class="form-control" type="text" name="name" value="<?php echo $mem->name; ?>" id="display"/></div>
            </div>
            <div class="form-group row">
				<label class="col-sm-4 col-form-label" for="email">Email</label>
					<div class="col-sm-8"><input class="form-control" type="text" name="email" value="<?php echo $mem->email; ?>" id="email"/></div>
            </div>
        </div>

			
        <h6 class="my-4">Change Password</h6>
        <div class="col justify-center">
            <div class="form-group row">                
                <label class="col-sm-4 col-form-label" for="pass1">New Password</label>
                <div class="col-sm-8"><input class="form-control" type="password" name="pass1" id="pass1" value=""/></div>
            </div>
            <div class="form-group row">
                <label for="pass2" class="col-sm-4 col-form-label">Verify Password</label>
                <div class="col-sm-8"><input class="form-control" type="password" name="pass2" id="pass2" value=""/></div>
            </div>
            <small id="passwordHelpInline" class="text-muted">Your password must be 8-20 characters long and must not contain spaces, special characters, or emoji.</small>
        </div>
        
        <h6 class="my-4">Echelon Preferences</h6>
        <div class="col justify-center">
            <div class="form-group row">                
                <label class="col-sm-4 col-form-label" for="timezone">Timezone</label>
                <div class="col-sm-8"><input class="form-control" type="text" name="timezone" id="timezone" placeholder="e.g. Europe/Berlin" value="<?php echo $_SESSION['timezone'];?>"/></div>
            </div>
            <small class="text-muted">Timezone field uses PHP <a class="external" href="http://php.net/manual/en/timezones.php" title="PHP time zone lisiting">time zones.</a></small>
        </div>
        
            </div>
            </div>
			
        <div class="card card-signin my-2">
            <div class="card-body">
                <h5 class="card-title">Verify Identity</h5>
                <div class="col justify-center">			
                    <div class="form-group row">
                        <label for="password" class="col-sm-4 col-form-label">Current Password</label>
                        <div class="col-sm-8"><input class="form-control" type="password" name="password" id="password" value=""/></div>
                    </div>
                    <button id="edit-me-submit" class="btn btn-primary float-right my-2" value="Login" type="submit">Save Changes</button>
                </div>
            </div>
        </div>
        </form>
    </div>


		
	
<?php require 'inc/footer.php'; ?>