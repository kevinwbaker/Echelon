<?php
$page = "client";
$page_title = "Clients Listing";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_edit";
$order = "DESC";

$is_search = false;

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o'])
	$order = addslashes($_GET['o']);

// allowed things to sort by
$allowed_orderby = array('id', 'name', 'connections', 'group_bits', 'time_add', 'time_edit');
// Check if the sent varible is in the allowed array 
if(!in_array($orderby, $allowed_orderby))
	$orderby = 'id'; // if not just set to default id

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;

## Search Request handling ##
if($_GET['s']) {
	$search_string = addslashes($_GET['s']);
	$is_search = true; // this is then a search page
}

if($_GET['t']) {
	$search_type = $_GET['t']; //  no need to escape it will be checked off whitelist
	$allowed_search_type = array('all', 'alias', 'guid', 'ip', 'id', 'aliastable', 'ipaliastable');
	if(!in_array($search_type, $allowed_search_type))
		$search_type = 'all'; // if not just set to default all
}


###########################
######### QUERIES #########

$query = "SELECT c.id, c.name, c.connections, c.time_edit, c.time_add, c.group_bits, g.name as level
			FROM clients c LEFT JOIN groups g
			ON c.group_bits = g.id WHERE c.id != 1 ";
            

if($is_search == true) : // IF SEARCH
	if($search_type == 'alias') { // NAME
		$query .= "AND c.name LIKE '%$search_string%' ORDER BY $orderby";
		
	} elseif($search_type == 'id') { // ID
		$query .= "AND c.id LIKE '%$search_string%' ORDER BY $orderby";
		
	} elseif($search_type == 'guid') { // GUID
		$query .= "AND c.guid LIKE '%$search_string%' ORDER BY $orderby";
		
	} elseif($search_type == 'ip') { // IP
		$query .= "AND c.ip LIKE '%$search_string%' ORDER BY $orderby";
		
	} elseif($search_type == 'aliastable') { // ALIAS
		$query = "SELECT client_id AS id, alias AS name, time_edit, time_add FROM aliases WHERE alias LIKE '%$search_string%' ORDER BY $orderby";

	} elseif($search_type == 'ipaliastable') { // IP-ALIAS
		$query = "SELECT client_id AS id, ip AS name, time_edit, time_add FROM ipaliases WHERE ip LIKE '%$search_string%' ORDER BY $orderby";

	}else { // ALL
		$query .= "AND c.name LIKE '%$search_string%' OR c.guid LIKE '%$search_string%' OR c.ip LIKE '%$search_string%' OR c.id LIKE '%$search_string%'
			ORDER BY $orderby";
	}
else : // IF NOT SEARCH
	$query .= sprintf("ORDER BY %s ", $orderby);

endif; // end if search request

## Append this section to all queries since it is the same for all ##
if($order == "DESC")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Require Header ##	
require 'inc/header.php';

if(!$db->error) :
?>

<div class="container my-2">
<div class="card">
<div class="card-header">
    <h5 class="my-auto">Client Listings
    <small class="my-1 float-sm-right"><?php echo $game_name; ?></small>
    </h5>
    			<?php
			if($search_type == "all")
				echo 'You are searching all clients that match <strong>'.$search_string.'</strong>. There are <strong>'. $total_rows .'</strong> entries matching your request.';
			elseif($search_type == 'alias')
				echo 'You are searching all clients names for <strong>'.$search_string.'</strong>. There are <strong>'. $total_rows .'</strong> entries matching your request.';
            elseif($search_type == 'aliastable')
				echo 'You are searching all alias names for <strong>'.$search_string.'</strong>. There are <strong>'. $total_rows .'</strong> entries matching your request.';
            elseif($search_type == 'ipaliastable')
				echo 'You are searching all client IP-alias names for <strong>'.$search_string.'</strong>. There are <strong>'. $total_rows .'</strong> entries matching your request.';
			elseif($search_type == 'guid')
				echo 'You are searching all clients GUIDs for <strong>'.$search_string.'</strong>. There are <strong>'. $total_rows .'</strong> entries matching your request.';
			elseif($search_type == 'id')
				echo 'You are searching all clients B3 IDs for <strong>'.$search_string.'</strong>. There are <strong>'. $total_rows .'</strong> entries matching your request.';
			elseif($search_type == 'ip')
				echo 'You are searching all clients IP addresses for <strong>'.$search_string.'</strong>. There are <strong>'. $total_rows .'</strong> entries matching your request.';
			?>
</div>
    <div class="card-body table table-hover table-sm table-responsive">
    <table width="100%">
	<thead>
		<tr>
			<th>Name
				<?php linkSortClients('name', 'Name', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Client-ID
				<?php linkSortClients('id', 'Client-ID', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Level
				<?php linkSortClients('group_bits', 'Level', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Connections
				<?php linkSortClients('connections', 'Connections', $is_search, $search_type, $search_string); ?>
			</th>
			<th>First Seen
				<?php linkSortClients('time_add', 'First Seen', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Last Seen
				<?php linkSortClients('time_edit', 'Last Seen', $is_search, $search_type, $search_string); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6">Click client name to see details.</th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	if($num_rows > 0) : // query contains stuff
	 
		foreach($data_set as $client): // get data from query and loop
			$cid = $client['id'];
			$name = $client['name'];
			$level = $client['level'];
			$connections = $client['connections'];
			$time_edit = $client['time_edit'];
			$time_add = $client['time_add'];
			
			$time_add = date($tformat, $time_add);
			$time_edit = date($tformat, $time_edit);
			
			$alter = alter();
				
			$client = clientLink($name, $cid);
			
			
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client</strong></td>
				<td>@$cid</td>
				<td>$level</td>
				<td>$connections</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
			</tr>
EOD;

		echo $data;
		endforeach;
	else :
		$no_data = true;
	
		echo '<tr class="odd"><td colspan="6">';
		if($is_search == false)
			echo 'There are no clients in the database.';
		else
			echo 'Your search for <strong>'.$search_string.'</strong> has returned no results. Maybe try an Alias/IP-Alias search?';
		echo '</td></tr>';
	endif; // no records
	?>
	</tbody>
</table>
</div></div></div>

<?php
	else:
		
	endif; // db error

	require 'inc/footer.php'; 
?>