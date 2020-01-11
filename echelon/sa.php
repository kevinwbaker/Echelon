<?php
$page = "sa";
$page_title = "Site Adminisration";

if(isset($_GET['t'])) {
	if($_GET['t'] == 'perms' OR $_GET['t'] == 'perms-group' OR $_GET['t'] == 'perms-add')
		$auth_name = 'edit_perms';
		
	elseif($_GET['t'] == 'user')
		$auth_name = 'siteadmin';
		
	elseif($_GET['t'] == 'edituser')
		$auth_name = 'edit_user';
		
} else {
	$auth_name = 'siteadmin';
	
}

## Require the inc files and start up class ##
require 'inc.php';

// If this is a view a user in more detail page
if($_GET['t'] == 'user') :
	$id = $_GET['id'];
	if(!isID($id)) {
		set_error('Invalid data sent. Request aborted.');
		send('sa.php');
	}
	
	## Get a users details
	$result = $dbl->getUserDetails($id);
	if(!$result) { // there was no user matching the sent id // throw error and sedn to SA page
		set_error("That user doesn't exist, please select a real user");
		send('sa.php');
		exit;
	} else {
		## Setup information vars ##
		$username = $result[0];
		$display = $result[1];
		$email = $result[2];
		$ip = $result[3];
		$group = $result[4];
		$admin_id = $result[5];
		$first_seen = $result[6];
		$last_seen = $result[7];
		$admin_name = $result[8];
	}
	
	$ech_logs = $dbl->getEchLogs($id, NULL, 'admin'); // get the echelon logs created by this user
	
	$token_del = genFormToken('del'.$id);

	$is_view_user = true;
endif; // end 

// if this is an edit user page
if($_GET['t'] == 'edituser') :
	if(!isID($_GET['id'])) {
		set_error('Invalid data sent. Request aborted.');
		send('sa.php');
	} else
		$uid = $_GET['id'];
	
	## Get a users details
	$result = $dbl->getUserDetailsEdit($uid);
	if(!$result) { // there was no user matching the sent id // throw error and sedn to SA page
		set_error('No user matches that id.');
		send('sa.php');
		exit;
	} else {
		## Setup information vars ##
		$u_username = $result[0];
		$u_display = $result[1];
		$u_email = $result[2];
		$u_group_id = $result[3];
	}
	
	// setup form token
	$ad_edit_user_token = genFormToken('adedituser');
	
	// get the names and id of all B3 Groups for select menu
	$ech_groups = $dbl->getGroups();
	
	// set referance var
	$is_edit_user = true;

endif;

## Permissions Setup ##
if($_GET['t'] == 'perms') :

	$is_permissions = true; // helper var
	$page = "perms";
	$page_title = "Echelon Group Management";

endif;

if($_GET['t'] == 'perms-group') :
	
	$group_id = cleanvar($_GET['id']);
	$group_id = (int)$group_id;
	$is_perms_group = true; // helper var
	
	$group_info = $dbl->getGroupInfo($group_id);

	$group_name = $group_info[0];
	$group_perms = $group_info[1];
	$page = "perms";
	$page_title = $group_name." Group";
	

endif;

if($_GET['t'] == 'perms-add') :

	$is_perms_group_add = true;

endif;

## Require Header ##	
require 'inc/header.php';

if($is_edit_user) : 

	#echo echUserLink($uid, $u_display, null, '&laquo; Go Back');
?>
<div class="container my-2">
<div class="card card-signin my-2">
<h5 class="card-header">Edit <?php echo $u_display; ?></h5>
<div class="card-body">
	
		<form action="actions/user-edit.php" method="post">
			<div class="col justify-center">
            <div class="form-group row">
			<label class="col-sm-4 col-form-label" for="display">Display Name:</label>
				<div class="col-sm-8"><input type="text" class="form-control" name="display" id="display" value="<?php echo $u_display; ?>"></div>
			</div>
            <div class="form-group row">
			<label class="col-sm-4 col-form-label" for="username">Username:</label>
				<div class="col-sm-8"><input class="form-control" type="text" name="username" id="username" value="<?php echo $u_username; ?>"></div>
            </div>
            <div class="form-group row">
			<label class="col-sm-4 col-form-label" for="email">Email Address:</label>
				<div class="col-sm-8"><input class="form-control" type="text" name="email" id="email" value="<?php echo $u_email; ?>"></div>
            </div>
            <div class="form-group row">
			<label class="col-sm-4 col-form-label" for="group">Group</label>
            <div class="col-sm-8">
				<select class="form-control" name="group" id="group">
					<?php foreach($ech_groups as $group) :
						if($group['id'] == $u_group_id)
							echo '<option value="'.$group['id'].'" selected="selected">'.$group['display'].'</option>';
						else
							echo '<option value="'.$group['id'].'">'.$group['display'].'</option>';
					endforeach; ?>
				</select>
			</div>
            </div>
            </div>
			<input type="hidden" name="token" value="<?php echo $ad_edit_user_token; ?>" />
			<input type="hidden" name="id" value="<?php echo $uid; ?>" />
				
			<button class="btn btn-primary float-right" type="submit" name="ad-edit-user" value="Edit <?php echo $u_display; ?>">Save Settings</button>
			
		</form>
		</div>
    </div>
</div>
	

<?php elseif($is_view_user) : ?>
	

<div class="col-lg-11 mx-auto my-2">
<div class="card my-2">
<h5 class="card-header">Echelon User Details for  <?php echo $display; ?>
<span class="float-right"><span class="float-right"><?php echo delUserLink($id, $token_del)?></span><?php echo editUserLink($id, $name); ?></span></h5>
<div class="card-body table table-hover table-sm table-responsive">

	
	<table class="user-table" width="100%">
		<tbody>
			<tr>
				<th>Name</th>
					<td><?php echo  tableClean($username); ?></td>
				<th>Display Name</th>
					<td><?php echo $display; ?></td>
			</tr>
			<tr>
				<th>Email</th>
					<td><?php echo emailLink($email, $display); ?></td>
				<th>IP Address</th>
					<td><?php echo ipLink($ip); ?></td>
			</tr>
			<tr>
				<th>First Seen</th>
					<td><?php echo date($tformat, $first_seen); ?></td>
				<th>Last Seen</th>
					<td><?php echo date($tformat, $last_seen); ?></td>
			</tr>
			<tr>
				<th>Creator</th>
					<td colspan="3"><?php echo echUserLink($admin_id, $admin_name); ?></td>
			</tr>
		</tbody>
	</table>
    <hr>

<h5 class="my-3" >Echelon Logs</h5>
<div class="table table-hover table-sm table-responsive">

	<table width="100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Type</th>
				<th>Message</th>
				<th>Time Added</th>
				<th>Client</th>
			</tr>
		</thead>
		<tfoot>
			<tr><th colspan="5"></th></tr>
		</tfoot>
		<tbody>
			<?php 
            displayEchLog($ech_logs, 'admin'); 
            if ($ech_logs==0) { 
                echo '<tr><td colspan="6">There are no echelon actions logged for this user.</td></tr></tr>'; 
            }
            ?>
		</tbody>
	</table>
	
</div></div></div>    
<?php elseif($is_permissions) : ?>
	
	
<div class="col-lg-11 mx-auto my-2">
<div class="card my-2">
<h5 class="card-header">Echelon Groups
<small><a href="sa.php?t=perms-add" title="Add a new Echelon group" class="my-auto float-sm-right">Add Group &raquo;</a></small>
</h5>
<div class="card-body table table-hover table-sm table-responsive">

<table width="100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Group Name</th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
		
		<tbody>
			<?php
				$ech_list_groups = $dbl->getGroups();
				
				$num_rows = count($ech_list_groups);
				
				if($num_rows > 0) :
					foreach($ech_list_groups as $group):
						$id = $group['id'];
						$name = $group['display'];
						
						
						$name_link = echGroupLink($id, $name);
						
						// setup heredoc (table data)			
						$data = <<<EOD
						<tr class="$alter">
							<td>$id</td>
							<td><strong>$name_link</strong></td>
							<td>&nbsp;</td>
						</tr>
EOD;

						echo $data;
					endforeach;
				else:
				
					echo '<tr><td colspan="3">There are no groups in the Echelon database. <a href="sa.php?t=perms-add" title="Add a new group to Echelon">Add Group</a></td></tr>';
				
				endif;
			
			?>
		</tbody>
	</table>
</div></div></div>	
<?php elseif($is_perms_group) : ?>

	<a href="sa.php?t=perms" title="Go back to permissions management homepage" class="float-left">&laquo; Permissions</a><br />
<div class="col-lg-11 mx-auto my-2">
<div class="card my-2">
<h5 class="card-header">Permissions for the <?php echo $group_name; ?> Group</h5>
<div class="card-body">	

		
		<form action="actions/perms-edit.php?gid=<?php echo $group_id; ?>" method="post">
		
		<table id="perms" width="100%">
		<tbody>
		<?php
			$perms_token = genFormToken('perm-group-edit');
		
			$perms = $dbl->getPermissions(); // gets a comprehensive list of Echelon groups
		
			$perms_list = array();
			$perms_list = explode(",", $group_perms);
			
			$perms_count = count($perms);
			$rows = ceil($perms_count/5) + 1;
			$ir = 1;
			$in = 0;
			
			while($ir < $rows) :
			
				echo '<tr>';
			
				$i = 1;
			
				while($i <= 5) :
				
					$p_id = $perms[$in]['id'];
					$p_name = $perms[$in]['name'];
					$p_desc = $perms[$in]['desc'];
					
					if(in_array($p_id, $perms_list))
						$checked = 'checked="checked" ';
					else
						$checked =  NULL;
					
					if($p_name != 'pbss') {
						$p_name_read = preg_replace('#_#', ' ', $p_name);
						$p_name_read = ucwords($p_name_read);
					} else
						$p_name_read = 'PBSS';
					
					if($p_id != "") :
						echo '<td class="perm-td"><label class="col-sm-8 my-2" for="'. $p_name .'">' . $p_name_read . '</label><div class="col"><label class="switch" for="'. $p_name .'"><input id="'.$p_name.'" type="checkbox" name="' . $p_name . '" ' . $checked . ' /><span class="slider round"></span></label></div>'; 
						tooltip($p_desc);
						echo '</td>';						
					endif;
					
					$in++;
					$i++;
				
				endwhile;
				
				echo '</tr>';
				
				$ir++;
				
			endwhile;
		?>
		</tbody>
		</table>
		
			
			<input type="hidden" name="token" value="<?php echo $perms_token; ?>" />
            <button class="btn btn-primary float-right" type="submit" name="server-settings-sub">Save Changes</button>
            <button class="harddel btn float-right disabled mx-2" type="submit" name="server-settings-sub">Delete Group (not working yet)</button>

		
		</form>
</div></div></div>		
	
<?php elseif($is_perms_group_add) : ?>
	
<div class="col-lg-11 mx-auto my-2">
<div class="card my-2">
<h5 class="card-header">Add Echelon Group</h5>
<div class="card-body">	
	
	<form action="actions/perms-edit.php?t=add" method="post">
	<div class="form-group row">
		<label class="col-sm-4 col-form-label" for="g-name">Name of Group</label>
        <div class="col-sm-8">
			<input class="form-control" type="text" name="g-name" id="g-name" />
		</div></div>
		<fieldset class="none" id="perms-fs">
		
		<h6 class="my-2">Group Permissions</h6>
		
		<table id="perms" width="100%">
		<tbody>
		<?php
		
			$add_g_token = genFormToken('perm-group-add');
		
			$perms = $dbl->getPermissions(); // gets a comprehensive list of Echelon groups AKA PREMISSIONS
			
			$perms_count = count($perms);
			$rows = ceil($perms_count/5) + 1;
			$ir = 1;
			$in = 0;
			
			while($ir < $rows) :
			
				echo '<tr>';
			
				$i = 1;
			
				while($i <= 5) :
				
					$p_id = $perms[$in]['id'];
					$p_name = $perms[$in]['name'];
					$p_desc = $perms[$in]['desc'];
					
					$p_name_read = preg_replace('#_#', ' ', $p_name);
					$p_name_read = ucwords($p_name_read);
					
					if($p_id != "") :
                        echo '<td class="perm-td"><label class="col-sm-8 my-2" for="'. $p_name .'">' . $p_name_read . '</label><div class="col"><label class="switch" for="'. $p_name .'"><input id="'.$p_name.'" type="checkbox" name="' . $p_name . '"/><span class="slider round"></span></label></div>'; 
						tooltip($p_desc);
						echo '</td>';						
					endif;
					
					$in++;
					$i++;
				
				endwhile;
				
				echo '</tr>';
				
				$ir++;
				
			endwhile;
		
		?>
		</tbody>
		</table>
		
		</fieldset>
		
		<br />
		<input type="hidden" name="token" value="<?php echo $add_g_token; ?>" />
        <button class="btn btn-primary float-right" type="submit" value="Add Group">Add Group</button>
	
	</form>
</div></div></div>	
	
<?php else : ?>
<div class="container">
<div class="card my-2">
<div class="card-header">
    <h5 class="my-auto">Echelon Users</h5></div>
    <div class="card-body table table-hover table-sm table-responsive">
    <table width="100%">
	<thead>
		<tr>
			<th>ID</th>
			<th>Name</th>
			<th>Group</th>
			<th>Email</th>
			<th>IP Address</th>
			<th>First Seen</th>
			<th>Last Seen</th>
			<th>Actions</th>
            <th></th>
		</tr>
	</thead>
	<tfoot>

	</tfoot>
	<tbody>
	<?php
		$users_data = $dbl->getUsers();
		
		foreach($users_data['data'] as $users): // get data from query and loop
			$id = $users['id'];
			$name = $users['display'];
			$group = $users['namep'];
			$email = $users['email'];
			
			$time_add = date($tformat, $users['first_seen']);
			$time_edit = date($tformat, $users['last_seen']);
			$ip = ipLink($users['ip']);
			$email_link = emailLink($email, $name);

			$token_del = genFormToken('del'.$id);
			$name_link = echUserLink($id, $name);
			$user_img_link = echUserLink($id, '<img src="images/user_view.png" alt="view" />', $name);
			$user_edit_link = editUserLink($id, $name);
			$user_del_link = delUserLink($id, $token_del);
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr>
				<td>$id</td>
				<td><strong>$name_link</strong></td>
				<td>$group</td>
				<td>$email_link</td>
				<td>$ip</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
				<td class="actions">
					$user_del_link
					$user_edit_link
					$user_img_link
                    </div> <!-- First part of div in functions.php: delUserLink, used to align items-->
				</td>
			</tr>
EOD;

		echo $data;
		endforeach;
	?>
	</tbody>
</table>
</div>
</div>
<?php
	$ech_groups = $dbl->getGroups();
	$add_user_token = genFormToken('adduser');
?>
<div class="card my-2">
<div class="card-header">
<h5 class="my-auto">Add Echelon User</h5></div>
    <div class="card-body">    
	<form action="actions/user-add.php" method="post" id="add-user-form">
    <div class="col justify-center">
		<div class="form-group row">
        <label class="col-sm-4 col-form-label" for="au-email">Email of User</label>
			<div class="col-sm-8">
            <input class="form-control" type="text" name="email" id="au-email" value="" required>
            </div></div>
            
                    <div class="form-group row">
		<label class="col-sm-4 col-form-label" for="au-comment">Comment</label>
        <div class="col-sm-8">
			<input class="form-control" type="text" name="comment" id="au-comment"  placeholder="Optional">
		</div></div>	
                    <div class="form-group row">
			<label class="col-sm-4 col-form-label" for="group">User Group</label>
            <div class="col-sm-8">
				<select class="form-control" name="group">
					<?php foreach($ech_groups as $group) :
						echo '<option value="'.$group['id'].'">'.$group['display'].'</option>';
					endforeach; ?>
				</select>

		<input type="hidden" name="token" value="<?php echo $add_user_token; ?>" />
		</div></div>
		<button name="add-user" id="add-user" class="btn btn-primary float-right my-2" value="Add User" type="submit">Add User</button>                    
        </div>
	</form>
</div></div>



<div class="card my-2">
<div class="card-header">
<h5 class="my-auto">Valid Registration Keys</h5></div>    
<div class="card-body table table-hover table-sm table-responsive">   
<table width="100%">
	<thead>
		<tr>
			<th>Registration Key</th>
			<th>Email <small>(assoc. with key)</small></th>
			<th>Admin</th>
			<th>Comment</th>
			<th>Added</th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
		$counter = 1;
		$keys_data = $dbl->getKeys($key_expire);
		
		$num_rows = $keys_data['num_rows'];
		
		if($num_rows > 0) :
		
		foreach($keys_data['data'] as $reg_keys): // get data from query and loop
		
			$reg_key = $reg_keys['reg_key']; // the reg key
			$comment = cleanvar($reg_keys['comment']); // comment about key
			$time_add = date($tformat, $reg_keys['time_add']);
			$email = emailLink($reg_keys['email'], '');
			$admin_link = echUserLink($reg_keys['admin_id'], 0); #$reg_keys['admin_id']; #echUserLink($reg_keys['admin_id'], $reg_keys['admin']);			
			
			$token_keydel = genFormToken('keydel'.$reg_key);
			
			if($mem->id == $admin_id) // if the current user is the person who create the key allow the user to edit the key's comment
				$edit_comment = '<img src="" alt="[Edit]" title="Edit this comment" class="edit-key-comment" />';
			else
				$edit_comment = '';
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td class="key">$reg_key</td>
				<td>$email</td>
				<td>$admin_link</td>
				<td><span class="comment">$comment</span> $edit_comment</td>
				<td><em>$time_add</em></td>
				<td class="actions">
					<form action="actions/key-edit.php" method="post" id="regkey-del-$counter">
						<input type="hidden" value="$token_keydel" name="token" />
						<input type="hidden" value="$reg_key" name="key" />
						<input type="hidden" value="del" name="t" />
						<input type="submit" name="keydel" value="Delete" class="action del harddel" title="Delete this registraion key" />
					</form>
				</td>
			</tr>
EOD;

			echo $data;
			$counter++;
		endforeach;
		
		else:
		
			echo '<tr><td colspan="6">There are no registration keys active on file</td></tr></tr>';
		
		endif;	
	?>
	</tbody>
</table>
</div></div>



<div class="card my-2">
<h5 class="card-header" title="A list of people banned from accessing this website.">Echelon Blacklist</h5>            
<div class="card-body table table-hover table-sm table-responsive">
<table width="100%">
	<thead>
		<tr>
			<th>ID</th>
			<th>IP Address</th>
			<th>Active</th>
			<th>Comment</th>
			<th>Admin</th>
			<th>Added</th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="7"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
		$bl_data = $dbl->getBL();
		$num_rows = $bl_data['num_rows'];
		
		if($num_rows > 0) :
		
			foreach($bl_data['data'] as $bl): // get data from query and loop
				$id = $bl['id'];			
				$ip = $bl['ip'];
				$active = $bl['active'];
				$reason = $bl['reason'];	
				$time_add = $bl['time_add'];
				$admin = $bl['admin'];
				
				$time_add = date($tformat, $time_add);
				$ip = ipLink($ip);		
					
				
					
				$token = genFormToken('act'.$id);

				if($active == 1) {
					$active = 'Yes';
					$actions = '<form action="actions/blacklist.php" method="post">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="token" value="'.$token.'" />
                        <button type="submit" name="deact" value="De-active" class="btn btn-warning btn-sm">De-Activate</button>
						</form>';
				} else {
					$active = 'No';
					$actions = '<form action="actions/blacklist.php" method="post">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="token" value="'.$token.'" />
                        <button type="submit" name="react" value="Re-active" class="btn btn-info btn-sm">Re-Activate</button>
						</form>';
				}
				
				unset($token);
			
				if($admin == '')
					$admin = 'Auto Added';
				
				// setup heredoc (table data)			
				$data = <<<EOD
				<tr class="$alter">
					<td>$id</td>
					<td><strong>$ip</strong></td>
					<td>$active</td>
					<td>$reason</td>
					<td>$admin</td>
					<td><em>$time_add</em></td>
					<td>
						$actions
					</td>
				</tr>
EOD;

			echo $data;
			endforeach;
			
		else:
		
			echo '<tr><td colspan="7">There are no IPs on the blacklist</td></tr>';
		
		endif;
	?>
	</tbody>
</table>
<hr>

	<form action="actions/blacklist.php" method="post" id="add-bl-form">
    <h5 class="my-4">Add IP to Blacklist</h5>
    <div class="col justify-center">
    <div class="form-group row">
        <label class="col-sm-4 col-form-label" for="bl-ip">IP Address</label>
            <div class="col-sm-8"><input class="form-control" type="text" name="ip" id="bl-ip"></div>
            <?php $bl_token = genFormToken('addbl'); ?>
			<input type="hidden" name="token" value="<?php echo $bl_token; ?>" />
    </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="bl-reason">Reason</label>
                <div class="col-sm-8"><input class="form-control" name="reason" id="bl-reason"></div>
        </div>

		</div>
        <button id="add-user-step-2" class="btn btn-primary float-right mx-3" value="Ban IP Address" type="submit">Ban IP-Address</button>
	</form>

</div></div>
</div>

<?php
	endif; // end if on what kind of page this is
	require 'inc/footer.php'; 
?>