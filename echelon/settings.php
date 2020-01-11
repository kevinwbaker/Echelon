<?php
$page = "settings";
$page_title = "Settings";
$auth_name = 'manage_settings';
$b3_conn = true;
require 'inc.php';

// get a list of main Echelon settings from the config table
$settings = $dbl->getSettings('cosmos');

$token_settings = genFormToken('settings');

require 'inc/header.php';
?>
<div class="container">
<div class="card card-signin my-2">
<h5 class="card-header">Echelon Settings</h5>
    <div class="card-body">
	<form action="actions/settings.php" method="post" id="settings-f">

        <h6>General</h6>
        <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="name">Site Name</label>
                    <div class="col-sm-8"><input class="form-control" type="text" name="name" id="name" value="<?php echo $settings['name']; ?>"></div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="email">Echelon Admin Email</label>
                    <div class="col-sm-8"><input class="form-control" type="text" name="email" value="<?php echo $settings['email']; ?>"></div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="admin_name">Name of Site Admin</label>
                    <div class="col-sm-8"><input class="form-control" type="text" name="admin_name" value="<?php echo $settings['admin_name']; ?>"></div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="limit_rows">Max. rows in tables</label>
                    <div class="col-sm-8"><input class="form-control int" type="text" name="limit_rows" value="<?php echo $settings['limit_rows']; ?>"></div>
            </div>            
        </div>
        

        <h6 class="my-4">E-Mail Messages and Announcements on Homepage</h6>
        <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="newsfeed">Annoucements</label>
                    <div class="col-sm-8"><textarea class="form-control" rows="4" name="newsfeed"><?php echo $settings['newsfeed']; ?></textarea>
                    <small>This field supports HTML.</small>	
                    </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="email_header">Text to start all emails:</label>
                    <div class="col-sm-8"><textarea class="form-control" rows="4" name="email_header"><?php echo $settings['email_header']; ?></textarea></div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="email_footer">Text to end all emails:</label>
                    <div class="col-sm-8"><textarea class="form-control" rows="4" name="email_footer"><?php echo $settings['email_footer']; ?></textarea></div>
            </div>
            <small>There are some variables that can be used in the email templates, <strong>%name%</strong> is replaced with the users name, and <strong>%ech_name%</strong> is replaced with the name of the website (eg. your clan name).</small>	
        </div>        

        <h6 class="my-4">Time Settings</h6>
        <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="time_format">PHP Time Format</label>
                    <div class="col-sm-8"><input class="form-control" type="text" name="time_format" value="<?php echo $settings['time_format']; ?>">
                    <small>Time format field is the PHP <a class="external" href="http://php.net/manual/en/function.date.php" title="PHP time format setup">time format</a>.</small>
                    </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="time_zone">PHP Time Zone</label>
                    <div class="col-sm-8"><input class="form-control" type="text" name="time_zone" value="<?php echo $settings['time_zone']; ?>">
                    <small class="tip">Timezone field uses PHP <a class="external" href="http://php.net/manual/en/timezones.php" title="PHP time zone lisiting">time zones</a>.</small>
                    </div>
            </div>
        </div>

        <h6>Security Settings</h6>
        <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="min_pw_len">Minimum password length for users</label>
                <div class="col-sm-8">
                <input type="text" name="min_pw_len" value="<?php echo $settings['min_pw_len']; ?>" class="form-control int">
                <small>Minimum length for Echelon user passwords</small>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="user_key_expire">Days a reg. key is active</label>
                <div class="col-sm-8">
                <input type="text" name="user_key_expire" value="<?php echo $settings['user_key_expire']; ?>" class="form-control int">
                <small>Number of days a registration key will remain valid after the time it was created</small>
                </div>
            </div>
               
            <div class="form-group row">
            <label class="col-sm-4" for="https">SSL connection required</label>
            
            <div class="col">
            <label class="my-1 switch">
                <input type="checkbox" name="https"<?php if($settings['https'] == 1) echo ' checked="checked"'; ?>>
                <span class="slider round"></span>
            </label>
            </div>
            <small class="my-1">Forces HTTPS, only enable if you have an SSL certificate.</small>
            </div>  

            <div class="form-group row">
            <label class="col-sm-4" for="allow_ie">Allow Internet Explorer</label>
            
            <div class="col">
            <label class="my-1 switch">
                <input type="checkbox" name="allow_ie"<?php if($settings['allow_ie'] == 1) echo ' checked="checked"'; ?>>
                <span class="slider round"></span>
            </label>
            </div>
            </div>  
			<?php if(!$no_games) : ?>
			
            <div class="form-group row">
				<label class="col-sm-4 col-form-label" for="pw_req_level">Require password</label>

                <div class="col">
                <label class="my-2 switch"> 
                    <input type="checkbox" name="pw_req_level"<?php if($settings['pw_req_level'] == 1) echo ' checked="checked"'; ?>>
                    <span class="slider round"></span>
                    <select class="mx-5 float-left" name="pw_req_level_group">
					<?php
						$b3_groups = $db->getB3Groups();
						foreach($b3_groups as $group) :
							$gid = $group['id'];
							$gname = $group['name'];
							if($settings['pw_req_level_group'] == $gid)
								echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
							else
								echo '<option value="'.$gid.'">'.$gname.'</option>';
						endforeach;
					?>
				</select>
                </label>
                </div></div></div>
				

			<?php endif; ?>

            <hr>
            <h6 class="my-4">Verify Yourself</h6>
            <div class="col justify-center">
                <div class="form-group row">
                    <label class="col-sm-4 col-form-label" for="verify-pw">Password</label>
                        <div class="col-sm-8"><input class="form-control" type="password" name="password" id="verify-pw"></div>
                </div>
            </div>  
		
		<input type="hidden" name="token" value="<?php echo $token_settings; ?>" />
		<button class="btn btn-primary float-right" type="submit" value="Save Echelon Settings" name="settings-sub">Save Settings</button>
		
	</form>
</div></div></div>

	
<?php require 'inc/footer.php'; ?>