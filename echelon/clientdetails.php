<?php
$page = "clientdetails";
$page_title = "Client Details";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
require 'inc.php';

## Do Stuff ##
if($_GET['id'])
	$cid = $_GET['id'];

if(!isID($cid)) :
	set_error('The client id that you have supplied is invalid. Please supply a valid client id.');
	send('clients.php');
	exit;
endif;
	
if($cid == '') {
	set_error('No user specified, please select one');
	send('clients.php');
}

## Get Client information ##
$query = "SELECT c.ip, c.connections, c.guid, c.name, c.mask_level, c.greeting, c.time_add, c.time_edit, c.group_bits, g.name
		  FROM clients c LEFT JOIN groups g ON c.group_bits = g.id WHERE c.id = ? LIMIT 1";
$stmt = $db->mysql->prepare($query) or die('Database Error '. $db->mysql->error);
$stmt->bind_param('i', $cid);
$stmt->execute();
$stmt->bind_result($ip, $connections, $guid, $name, $mask_level, $greeting, $time_add, $time_edit, $group_bits, $user_group);
$stmt->fetch();
$stmt->close();

## Require Header ##
$page_title .= ' '.$name; // add the clinets name to the end of the title

require 'inc/header.php';
?>
<div class="container">
<div class="card my-2">
<div class="card-header">
<h5 class="my-auto">Client Information</h5></div>
<div class="card-body table table-hover table-sm table-responsive">
<table width="100%">
	<tbody>
		<tr>
			<th>Name</th>
				<td><?php echo tableClean($name); ?>&nbsp;&nbsp;&nbsp;
                <?php $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'? "https://" : "http://"; ?>
                    <input type="image" title="Copy client URL to clipboard" onclick="copyToClipboard('<?php echo $http.$_SERVER['SERVER_NAME'].PATH;?>clientdetails.php?id=<?php echo $cid; ?>&amp;game=<?php echo $game;?>')" src="images/link.png" width="16" height="16" alt="W">
                </td>
			<th>@ID</th>
				<td><?php echo $cid; ?></td>
		</tr>       
		<tr>
			<th>Level</th>
				<td><?php
					if($user_group == NULL)
						echo 'Un-registered';
					else
						echo $user_group; 
					?>
				</td>
			<th>Connections</th>
				<td><?php echo $connections; ?></td>
		</tr>
		<tr>
			<th>GUID</th>
				<td>
				<?php 
					$guid_len = strlen($guid);
					if($guid_len == 0) {
						echo '(There is no GUID availible)';
					
					} elseif($mem->reqLevel('view_full_guid')) { // if allowed to see the full guid
						if($guid_len == 32) 
							guidCheckLink($guid);
						else 
							echo $guid.' <span class="red" title="This guid is only 31 characters long, it should be 32 characters!">['. $guid_len .']</span>';
				
					} elseif($mem->reqLevel('view_half_guid')) { // if allowed to see the last 8 chars of guid
						
						if($guid_len == 32) {
							$half_guid = substr($guid, -8); // get the last 8 characters of the guid
							guidCheckLink($half_guid);
						} else
							echo $guid.' <span class="red" title="This guid is only 31 characters long, it should be 32 characters!">['. $guid_len .']</span>';
					
					} else { // if not allowed to see any part of the guid
						echo '(You do not have access to see the GUID)';
					}
				?>
				</td>
			<th>IP Address</th>
				<td>
					<?php
					$ip = tableClean($ip);
					if($mem->reqLevel('view_ip')) :
						if ($ip != "") { ?>
							<a href="clients.php?s=<?php echo $ip; ?>&amp;t=ip" title="Search for other users with this IP address"><?php echo $ip; ?></a>
								&nbsp;&nbsp;
							<a href="http://whois.domaintools.com/<?php echo $ip; ?>" title="Whois IP Search"><img src="images/id_card.png" width="16" height="16" alt="W" /></a>
								&nbsp;&nbsp;
							<a href="https://whatismyipaddress.com/ip/<?php echo $ip; ?>" title="Show Location of IP origin on map"><img src="images/globe.png" width="16" height="16" alt="L" /></a>
					<?php
						} else {
							echo "(No IP address available)";
						}
					else:	
						echo '(You do not have access to see the IP address)';
					endif; // if current user is allowed to see the player's IP address
					?>
				</td>
		</tr>
		<tr>
			<th>First Seen</th>
				<td><?php echo date($tformat, $time_add); ?></td>
			<th>Last Seen</th>
				<td><?php echo date($tformat, $time_edit); ?></td>
		</tr>
	</tbody>
</table>
</div></div></div>

<!-- Start Echelon Actions Panel -->

<div class="container">
<div class="card">
<div class="card-header">
<ul class="nav nav-pills nav-fill mx-2" role="tablist">
		<?php if($mem->reqLevel('ban')) { ?><li class="nav-item"><a class="nav-link active" id="bansection-tab"" data-toggle="pill" href="#bansection" role="tab" aria-controls="bansection" aria-selected="true"><h6 class="my-auto">Ban</h6></a></li><?php } ?>
		<?php if($mem->reqLevel('edit_client_level')) { ?><li class="nav-item"><a class="nav-link" id="setlevel-tab" data-toggle="pill" href="#setlevel" role="tab" aria-controls="setlevel" aria-selected="false"><h6 class="my-auto">Set Level</h6></a></li><?php } ?>
		<?php if($mem->reqLevel('edit_mask')) { ?><li class="nav-item"><a class="nav-link" id="cd-act-mask-tab" data-toggle="pill" href="#cd-act-mask" role="tab" aria-controls="cd-act-mask" aria-selected="false"><h6 class="my-auto">Mask Level</h6></a></li><?php } ?>
        <?php if($mem->reqLevel('greeting')) { ?><li class="nav-item"><a class="nav-link" id="setgreeting-tab" data-toggle="pill" href="#setgreeting" role="tab" aria-controls="setgreeting" aria-selected="false"><h6 class="my-auto">Greeting</h6></a></li><?php } ?>
        <?php if($mem->reqLevel('comment')) { ?><li class="nav-item"><a class="nav-link" id="addcomment-tab" data-toggle="pill" href="#addcomment" role="tab" aria-controls="addcomment" aria-selected="false"><h6 class="my-auto">Comment</h6></a></li><?php } ?>
		<?php 
			if(!$no_plugins_active)
				$plugins->displayCDFormTab();
		?>
</ul>
</div>
<div class="card-body row">
    <div class="col">
        <div class="tab-content" id="v-pills-tabContent">
  
        <?php
        if($mem->reqLevel('ban')) :
        $ban_token = genFormToken('ban');
		?>
        <div class="tab-pane fade show active" id="bansection" role="tabpanel" aria-labelledby="bansection-tab">
		<div class="col justify-center"> 
			<form action="actions/b3/ban.php" method="post">
                <?php
                if($mem->reqLevel('permban')) :
                ?>
                <div class="form-row my-1">                                
                            <label class="col-sm-4" for="pb">Permanent Ban?</label>                    
                            <div class="col">
                                <label class="my-1 switch">
                                    <input type="checkbox" name="pb" id="pb"> 
                                    <span class="slider round"></span>
                                </label>
                            </div>
                </div>
                <?php
                    endif; // end hide ban section to non authed
                ?>
                
                <div class="form-row" id="ban-duration">
                <label class="col-form-label col-sm-4" for="duration">Duration</label>
                            
                <div class="col-md-4" id="ban-duration">
                    <div class="input-group">
                            <input class="form-control int dur" type="number" name="duration" id="duration" min="0" step="1" data-bind="value:replyNumber">	
                            <select class="custom-select form-control" name="time">
                                <option value="m">Minutes</option>
                                <option value="h">Hours</option>
                                <option value="d">Days</option>
                                <option value="w">Weeks</option>
                                <option value="mn">Months</option>
                                <option value="y">Years</option>
                            </select>               
                    </div>
      
                </div>
                </div>
                   
                    
				<div class="form-row my-1">
                    <label class="col-form-label col-sm-4" for="reason">Reason</label>
                    <div class="col-md-4">
                        <input class="form-control" type="text" name="reason" id="reason">
                        </div>
                </div>
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="hidden" name="c-name" value="<?php echo $name; ?>" />
				<input type="hidden" name="c-ip" value="<?php echo $ip; ?>" />
				<input type="hidden" name="c-pbid" value="<?php echo $guid; ?>" />
				<input type="hidden" name="token" value="<?php echo $ban_token; ?>" />
				<button class="btn btn-danger float-right my-1" type="submit" name="ban-sub" value="Ban User" title="Banhammer dat snitch">Ban User</button>
			</form>
		</div>       
        </div><!-- Ban Section -->
		<?php
            endif; // end hide ban section to non authed
            $b3_groups = $db->getB3Groups();
            
            if($mem->reqLevel('edit_client_level')) :
			$level_token = genFormToken('level');
		?>
        <div class="tab-pane fade" id="setlevel" role="tabpanel" aria-labelledby="setlevel-tab">
        <div class="col justify-center">
			<form action="actions/b3/level.php" method="post">
            <div class="form-row">
				<label class="col-sm-4 col-form-label" for="level">Level</label>
                <div class="col-md-4">
					<select class="form-control" name="level" id="level">
						<?php
							foreach($b3_groups as $group) :
								$gid = $group['id'];
								$gname = $group['name'];
								if($group_bits == $gid)
									echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
								else
									echo '<option value="'.$gid.'">'.$gname.'</option>';
							endforeach;
						?>
					</select>
                    </div>
                </div>
					
				<div class="form-row my-1" id="level-pw">
					<label class="col-sm-4 col-form-label" for="password">Verify yourself</label>
                    <div class="col-md-4">
						<input class="form-control" type="password" name="password" id="password">
						</div>
				</div>
					
				<input type="hidden" name="old-level" value="<?php echo $group_bits; ?>" />
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="hidden" name="token" value="<?php echo $level_token; ?>" />
				<button class="btn btn-primary float-right" type="submit" name="level-sub" value="Change Level">Set Level</button>
			</form>
		</div>
        </div>
		<?php
			endif; // end if 
			if($mem->reqLevel('edit_mask')) : 
			$mask_lvl_token = genFormToken('mask');
		?>
        <div class="tab-pane fade" id="cd-act-mask" role="tabpanel" aria-labelledby="cd-act-mask-tab">
            <div class="col">
			<form action="actions/b3/level.php" method="post">
            <div class="form-row">
                <label class="col-sm-4 col-form-label" for="mlevel">Mask Level</label>
                <div class="col-md-4">
					<select class="form-control" name="level" id="mlevel">
						<?php
							foreach($b3_groups as $group) :
								$gid = $group['id'];
								$gname = $group['name'];
								if($mask_level == $gid)
									echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
								else
									echo '<option value="'.$gid.'">'.$gname.'</option>';
							endforeach;
						?>
					</select>
                    </div></div>
				<input type="hidden" name="old-level" value="<?php echo $group_bits; ?>" />
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<input type="hidden" name="token" value="<?php echo $mask_lvl_token; ?>" />
				<button class="btn btn-primary float-right my-1" type="submit" name="mlevel-sub" value="Change Mask">Set masked level</button>
			</form>
		</div>
        </div>
		<?php 
			endif;
			if($mem->reqLevel('greeting')) :
			$greeting_token = genFormToken('greeting');
		?>
        <div class="tab-pane fade" id="setgreeting" role="tabpanel" aria-labelledby="setgreeting-tab">
            <div class="col justify-content-center m-auto">

			<form action="actions/b3/greeting.php" method="post">
            <div class="form-row">
				<label class="col-form-label col-sm-4" for="greeting">Greeting Message</label>
                    <div class="col-md-4">
                        <textarea class="form-control" rows="3" name="greeting" id="greeting"><?php echo $greeting; ?></textarea>
                    </div>
                </div>	
				<input type="hidden" name="token" value="<?php echo $greeting_token; ?>" />
				<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				<button class="btn btn-primary float-right my-1" type="submit" name="greeting-sub" value="Edit Greeting">Set Greeting</button>
			</form>
		</div>
        </div>
		<?php
			endif;            
			if($mem->reqLevel('comment')) :
			$comment_token = genFormToken('comment');	
		?>
        <div class="tab-pane fade" id="addcomment" role="tabpanel" aria-labelledby="addcomment-tab">
            <div class="col justify-content-center">
                <form action="actions/b3/comment.php" method="post">
                    <div class="form-row">
                    <label class="col-form-label col-sm-4" for="comment">Comment</label>
                        <div class="col-md-4">
                            <textarea class="form-control" type="text" name="comment" id="comment" rows="3"></textarea>
                        </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $comment_token; ?>" />
                    <input type="hidden" name="cid" value="<?php echo $cid; ?>" />
                    <button class="btn btn-primary float-right my-1" type="submit" name="comment-sub">Add Comment</button>
                </form>
            </div>
        </div>
		<?php
			endif;
			## Plugins CD Form ##
			if(!$no_plugins_active)
				$plugins->displayCDForm($cid)
			
		?>
	</div><!-- end #actions-box -->
</div><!-- end #actions -->
</div>
</div>
</div>

<!-- Start Information Tables Panel -->

<div class="container my-2">
<div class="card">
<div class="card-header">
<ul class="nav nav-pills nav-fill" role="tablist">
<li class="nav-item">
<a class="nav-link active" id="penalties-tab" data-toggle="tab" href="#penalties" role="tab" aria-controls="penalties" aria-selected="true"><h6 class="my-auto">Penalties</h6></a>
</li>
<li class="nav-item">
<a class="nav-link" id="clientaliases-tab" href="#clientaliases" data-toggle="tab" role="tab" aria-controls="clientaliases" aria-selected="false"><h6 class="my-auto">Client Aliases</h6></a>
</li>
<?php if($mem->reqLevel('view_ip')) : ?>
<li class="nav-item">
<a class="nav-link" id="ipaliases-tab" href="#ipaliases" data-toggle="tab" role="tab" aria-controls="ipaliases" aria-selected="false"><h6 class="my-auto">IP Aliases</h6></a>
</li>
<?php
	endif; // end hide is no records
?>
<li class="nav-item">
<a class="nav-link" id="admin-tab" href="#admin" data-toggle="tab" role="tab" aria-controls="admin" aria-selected="false"><h6 class="my-auto">Admin Actions</h6></a>
</li>
<?php
	## Get Echelon Logs Client Logs (NOTE INFO IN THE ECHELON DB) ##
	$ech_logs = $dbl->getEchLogs($cid, $game);
	
	$count = count((array)$ech_logs);
	if($count > 0) : // if there are records
?>
<li class="nav-item">
<a class="nav-link" id="echelon-tab" href="#echelon" data-toggle="tab" role="tab" aria-controls="echelon" aria-selected="false"><h6 class="my-auto">Echelon Logs</h6></a>
</li>
<?php
	endif; // end hide is no records
	if(!$no_plugins_active)
        $plugins->displayCDFormNavTab($cid);
        $plugins->displayCDFormNavTabLog($cid);
?> 
</ul>
</div>
<div class="card-body">
<div class="tab-content">
<!-- Client Penalties -->
<div id="penalties"  class="tab-pane fade show active table table-hover table-responsive table-sm" role="tabpanel" aria-labelledby="penalties-tab">
	<table id="cd-pen-table" width="100%">
		<thead>
			<tr>
				<th>Ban ID</th>
				<th>Type</th>
				<th>Added</th>
				<th>Duration</th>
				<th>Expires</th>
				<th>Reason</th>
				<th>Admin</th>
			</tr>
		</thead>
		<tfoot>
			<tr><td colspan="7"></td></tr>
		</tfoot>
		<tbody id="contain-pen">
			<?php 
				$type_inc = 'client';
				include 'inc/cd/penalties.php'; 
			?>
		</tbody>
	</table>
</div>

<!-- Start Client Aliases -->
<div id="clientaliases" class="tab-pane fade table table-hover table-responsive table-sm" role="tabpanel" aria-labelledby="clientalises-tab">
<table id="cd-ia-table" width="100%">
	<thead>
		<tr>
			<th>Alias</th>
			<th>Times Used</th>
			<th>First Used</th>
			<th>Last Used</th>
		</tr>
	</thead>
	<tfoot>
		<tr><th colspan="4"></th></tr>
	</tfoot>
	<tbody>
	<?php
		// notice on the query we say that time_add does not equal time_edit, this is because of bug in alias recording in B3 that has now been solved EDIT= it doesnt show all aliases though
        // $query = "SELECT alias, num_used, time_add, time_edit FROM aliases WHERE client_id = 64335 AND time_add != time_edit ORDER BY time_edit DESC";
		$query = "SELECT alias, num_used, time_add, time_edit FROM aliases WHERE client_id = ? ORDER BY time_edit DESC";
		$stmt = $db->mysql->prepare($query) or die('Alias Database Query Error'. $db->mysql->error);
		$stmt->bind_param('i', $cid);
		$stmt->execute();
		$stmt->bind_result($alias, $num_used, $time_add, $time_edit);
		
		$stmt->store_result(); // needed for the $stmt->num_rows call

		if($stmt->num_rows) :
			
			while($stmt->fetch()) :
	
				$time_add = date($tformat, $time_add);
				$time_edit = date($tformat, $time_edit);
				
				$alter = alter();
				
				$token_del = genFormToken('del'.$id);		
				
				// setup heredoc (table data)			
				$data = <<<EOD
				<tr class="$alter">
					<td><strong>$alias</strong></td>
					<td>$num_used</td>
					<td><em>$time_add</em></td>
					<td><em>$time_edit</em></td>
				</tr>
EOD;
				echo $data;
			
			endwhile;
		
		else : // if there are no aliases connected with this user then put out a small and short message
		
			echo '<tr><td colspan="4">'.$name.' has no aliases.</td></tr>';
		
		endif;
	?>
	</tbody>
</table>
</div>

<!-- Start IP-Aliases -->
<?php if($mem->reqLevel('view_ip')) : ?>
<div id="ipaliases" class="tab-pane fade table table-hover table-responsive table-sm" role="tabpanel" aria-labelledby="ipalises-tab">
<table id="cd-ipa-table" width="100%">
	<thead>
		<tr>
			<th>IP-Alias</th>
			<th>Times Used</th>
			<th>First Used</th>
			<th>Last Used</th>
		</tr>
	</thead>
	<tfoot>
		<tr><th colspan="4"></th></tr>
	</tfoot>
	<tbody>
	<?php
		$query = "SELECT ip, num_used, time_add, time_edit FROM ipaliases WHERE client_id = ? ORDER BY time_edit DESC";
		$stmt = $db->mysql->prepare($query) or die('IP-Alias Database Query Error'. $db->mysql->error);
		$stmt->bind_param('i', $cid);
		$stmt->execute();
		$stmt->bind_result($alias, $num_used, $time_add, $time_edit);
		
		$stmt->store_result(); // needed for the $stmt->num_rows call

		if($stmt->num_rows) :
			
			while($stmt->fetch()) :
	
				$time_add = date($tformat, $time_add);
				$time_edit = date($tformat, $time_edit);
				
				$alter = alter();
				
				$token_del = genFormToken('del'.$id);		
				// setup heredoc (table data)			
				$data = <<<EOD
				<tr class="$alter">
					<td><strong><a href="clients.php?s=$alias&amp;t=ip" title="Search for other users with this IP address">$alias</a></strong></td>
					<td>$num_used</td>
					<td><em>$time_add</em></td>
					<td><em>$time_edit</em></td>
				</tr>
EOD;
				echo $data;
			
			endwhile;
		
		else : // if there are no aliases connected with this user then put out a small and short message
		
			echo '<tr><td colspan="4">'.$name.' has no IP-aliases.</td></tr>';
		
		endif;
	?>
	</tbody>
</table>
</div>
<?php
	endif; // end hide is no records
?>

<!-- Admin History -->
<div id="admin" class="tab-pane fade table table-hover table-responsive table-sm" role="tabpanel" aria-labelledby="admin-tab">
	<table id="cd-admin-table" width="100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Type</th>
				<th>Added</th>
				<th>Duration</th>
				<th>Expires</th>
				<th>Reason</th>
				<th>Admin</th>
			</tr>
		</thead>
		<tfoot>
			<tr><td colspan="7"></td></tr>
		</tfoot>
		<tbody id="contain-admin">
			<?php 
				$type_inc = 'admin';
				include 'inc/cd/penalties.php'; 
			?>
		</tbody>
	</table>
</div>

<!-- Start Client Echelon Logs -->

<?php
	## Get Echelon Logs Client Logs (NOTE INFO IN THE ECHELON DB) ##
	$ech_logs = $dbl->getEchLogs($cid, $game);
	
	$count = count((array)$ech_logs);
	if($count > 0) : // if there are records
?>
<div id="echelon" class="tab-pane fade table table-hover table-responsive table-sm" role="tabpanel" aria-labelledby="echelon-tab">
	<table id="cd-ech-table" width="100%">
		<thead>
			<tr>
				<th>ID</th>
				<th>Type</th>
				<th>Message</th>
				<th>Time Added</th>
				<th>Admin</th>
			</tr>
		</thead>
		<tfoot>
			<tr><th colspan="5"></th></tr>
		</tfoot>
		<tbody>
			<?php displayEchLog($ech_logs, 'client'); ?>
		</tbody>
	</table>  
</div>

<?php
	endif; // end hide is no records
?>

<?php 
    ## Plugins Client Bio Area ##
	if(!$no_plugins_active):  
?>
<div id="xlr" class="tab-pane fade" role="tabpanel" aria-labelledby="xlr-tab">
<?php $plugins->displayCDBio();?>
</div>
<?php
	endif; // end hide is no records

    ## Plugins Client Bio Area ##
    if(!$no_plugins_active):  
?>
<div id="chatlog" class="tab-pane fade" role="tabpanel" aria-labelledby="chatlog-tab">
<?php
## Plugins Log Include Area ##
$plugins->displayCDlogs($cid);?>
</div>
<?php
	endif; // end hide is no records
?>
</div>
</div>
</div>
</div>

<?php
// Close page off with the footer
require 'inc/footer.php'; 
?>
