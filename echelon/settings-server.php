<?php
$page = "settings-server";
$page_title = "Server Settings";
$auth_name = 'manage_settings';
require 'inc.php';

// We are using the game information that was pulled in setup.php
$game_token = genFormToken('serversettings');

$page_type = 'none';
if($_GET['t'])
	$page_type = cleanvar($_GET['t']);

if($page_type == 'add') : ## if add a server page ##

	$token = genFormToken('addserver');

elseif($page_type == 'srv') : ## if edit a server page ##
	
	$server_id = cleanvar($_GET['id']);
	if($server_id == '') {
		set_error('No server id chosen, please choose a server');
		send('game-settings.php');
		exit;
	}
	
	$token = genFormToken('editserversettings');
	
	## get server information
	$server = $dbl->getServer($server_id);
	
else: ## if a normal list page ##

	## Default Vars ##
	$orderby = "id";
	$order = "ASC"; // either ASC or DESC

	## Sorts requests vars ##
	if($_GET['ob'])
		$orderby = addslashes($_GET['ob']);

	if($_GET['o'])
		$order = addslashes($_GET['o']);

	## allowed things to sort by ##
	$allowed_orderby = array('id', 'name', 'ip', 'pb_active');
	if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
		$orderby = 'id'; // if not just set to default id
	
	if($order == 'DESC')
		$order = 'DESC';
	else
		$order = 'ASC';
	
	## Get List ##
	if(!$no_servers) // if there are servers
		$servers = $dbl->getServerList($orderby, $order);
	
	## Find num of servers found ##
	if(!$servers) // if false
		$num_rows = 0;
	else
		$num_rows = count($servers);

endif;

require 'inc/header.php';

if($num_games < 1) : ?>

	<h3>No Games Created</h3>
		<p>Please go to <a href="settings-games.php?t=add">Settings Games</a>, and add a game before you can add/edit any server settings</p>

<?php elseif($page_type == 'add') : ?>

    <div class="container my-2">
    <div class="card my-2">
    <h5 class="card-header">Add Server</h5>
    <div class="card-body">
        <form action="actions/settings-server.php" method="post">
        <h6>Server Details</h6>
        <div class="col justify-center">
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Server Name</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="name" id="name">
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="ip">IP Address</label>
            <div class="col-sm-8">
                <input class="form-control" type="text" name="ip" id="ip">
            </div>
        </div>
        <div class="form-group row">
    			<label class="col-sm-4 col-form-label" for="game-id">Game</label>
                <div class="col-sm-8">
				<select class="form-control" name="game-id" id="game-id">
					<?php
					$i = 0;
					$count = count($games_list);
					$count--; // minus 1
					while($i <= $count) :

						echo '<option value="'.$games_list[$i]['id'].'">'.$games_list[$i]['name'].'</option>';
						
						$i++;
					endwhile;
					?>
				</select>
                </div></div>
        <div class="form-group row">
        <label class="col-sm-4" for="pb">Punkbuster&trade; Active?</label>
        <div class="col">
        <label class="my-1 switch" name="pb" id="pb">
          <input type="checkbox" name="pb" id="pb" <?php if($server['pb_active'] == 1) echo 'checked="checked"'; ?> >
          <span class="slider round"></span>
        </label></div></div></div>
        <hr>
        
        <h6 class="my-4">RCON Details</h6>
        <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="rcon-ip">RCON IP</label>
                <div class="col-sm-8">
                				<input class="form-control" type="text" name="rcon-ip" id="rcon-ip">
                </div>
            </div>

            <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="rcon-port">RCON Port</label>
                <div class="col-sm-8">
                <input class="form-control" type="text" class="int" name="rcon-port" id="rcon-port">
                </div>
            </div>        
            
            <div id="change-pw-box" class="form-group row">
            <label class="col-sm-4 col-form-label" for="rcon-pass">RCON Password</label>
                <div class="col-sm-8">
                <input class="form-control" type="password" name="rcon-pass" id="-rcon-pass"/>
                </div>
            </div>    							
        </div>
	
		<input type="hidden" name="type" value="add" />
		<input type="hidden" name="cng-pw" value="on" />
		<input type="hidden" name="token" value="<?php echo $token; ?>" />
        <button class="btn btn-primary float-right" type="submit" name="server-settings-sub" value="Add Server">Add Server</button>

	</form>

<?php elseif($page_type == 'srv') : /* if edit server page */ ?>
	
	<a href="settings-server.php" title="Go back to the main server listing" class="float-left">&laquo; Server List</a>
	<a href="settings-server.php?t=add" title="Add a server" class="float-right">Add Server &raquo;</a>
	<br />
	
<div class="container my-2">
<div class="card my-2">
<h5 class="card-header">Manage <?php echo $server['name']; ?></h5>
      <div class="card-body">
            <form action="actions/settings-server.php" method="post">
            <h6>Server Details</h6>
            <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label">Server Name</label>
                <div class="col-sm-8">
                <input class="form-control" type="text" name="name" id="name" value="<?php echo $server['name']; ?>">
                </div>
            </div>

            <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="ip">IP Address</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" name="ip" id="ip" value="<?php echo $server['ip']; ?>">
                </div>
            </div>
            <div class="form-group row">
            <label class="col-sm-4" for="pb">Punkbuster&trade; Active?</label>
                <div class="col">
                <label class="my-1 switch" name="pb" id="pb">
                  <input type="checkbox" name="pb" id="pb" <?php if($server['pb_active'] == 1) echo 'checked="checked"'; ?> >
                  <span class="slider round"></span>
                </label></div></div> 
            </div>		
            <hr>		                	
                    
            <h6 class="my-4">RCON Details</h6>
            <div class="col justify-center">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="rcon-ip">RCON IP</label>
                <div class="col-sm-8">
                				<input class="form-control" type="text" name="rcon-ip" id="rcon-ip" value="<?php echo $server['rcon_ip']; ?>">
                </div>
            </div>

            <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="rcon-port">RCON Port</label>
                <div class="col-sm-8">
                <input class="form-control" type="text" class="int" name="rcon-port" id="rcon-port" value="<?php echo $server['rcon_port']; ?>">
                </div>
            </div>            
            <div class="form-group row">

            <label class="col-sm-4" for="cng-pw">Change RCON Password?</label>
            
            <div class="col">
            <label class="my-1 switch" name="cng-pw" id="cng-pw">
              <input type="checkbox" name="cng-pw" id="cng-pw">
              <span class="slider round"></span>
            </label></div></div>            
            
            
            <div id="change-pw-box" class="form-group row">
            <label class="col-sm-4 col-form-label" for="rcon-pass">RCON Password</label>
                <div class="col-sm-8">
                <input class="form-control" type="password" name="rcon-pass" id="-rcon-pass" />
                </div>
            </div>    							
</div>
		<input type="hidden" name="type" value="edit" />
		<input type="hidden" name="token" value="<?php echo $token; ?>" />
		<input type="hidden" name="server" value="<?php echo $server_id; ?>" />
		<button class="btn btn-primary float-right" type="submit" name="server-settings-sub">Save Settings</button>

	</form>
	
</div></div></div>

	
<?php else : /* if normal list page type */ ?>

	<a href="settings-server.php?t=add" title="Add a server" class="float-right">Add Server &raquo;</a>
    <div class="col-lg-11 mx-auto my-2">
    <div class="card my-2">
    <h5 class="card-header">B3 Servers</h5>
    <div class="card-body table table-hover table-sm table-responsive">

    <table width="100%">
	<thead>
		<tr>
			<th>ID
				<?php linkSort('id', 'id'); ?>
			</th>
			<th>Name
				<?php linkSort('name', 'Name'); ?>
			</th>
			<th>IP
				<?php linkSort('ip', 'Server IP'); ?>
			</th>
			<th>PB Enabled
				<?php linkSort('pb_active', 'Punkbuster Enabled Status'); ?>
			</th>
			<th>Game</th>
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
		if($num_rows > 0) : // query contains stuff
		 
			foreach($servers as $server): // get data from query and spit it out
				$id = $server['id'];
				$name = $server['name'];
				$game_id = $server['game'];
				$pb_active = $server['pb_active'];
				$ip = $server['ip'];
				$game_name = $server['game_name'];
				
				## row color
				$alter = alter();
				
				## Make it human readable
				if($pb_active == 1)
					$pb_active_read = '<span class="on">Yes</span>';
				else
					$pb_active_read = '<span class="off">No</span>';
					
				$ip_read = ipLink($ip);
				
				// set a warning that the active game has changed since the last page?
				if($game != $game_id)
					$warn = 'game';
				else
					$warn = '';
					
				$del_token = genFormToken('del-server'.$id);
			
				$table = <<<EOD
				<tr class="$alter">
					<td>$id</td>
					<td><strong><a href="settings-server.php?t=srv&amp;id=$id">$name</a></strong></td>
					<td>$ip_read</td>
					<td>$pb_active_read</td>
					<td><a href="settings-games.php?game=$game_id&amp;w=$warn" title="Edit the settings for $game_name">$game_name</a></td>
					<td>
						<a href="settings-server.php?t=srv&amp;id=$id"><img src="images/edit.png" alt="[E]" /></a>
						<form style="display: inline;" method="post" action="actions/settings-server.php?t=del&amp;id=$id">
							<input type="hidden" name="token" value="$del_token" />
							<input class="harddel" type="image" title="Delete this Server" src="images/delete.png" alt="[D]" />
						</form>
					</td>
				</tr>
EOD;

				echo $table;
			endforeach;

		else :
			echo '<tr class="odd"><td colspan="6">There are no servers would you like to <a href="settings-server.php?t=add" title="Add a new Server to Echelon DB">add a server</a>.</td></tr>';
		endif; // end if query contains
		?>
	</tbody>
	</table>

</div></div></div>	

<?php endif; // if no an empty id ?>

<?php require 'inc/footer.php'; ?>