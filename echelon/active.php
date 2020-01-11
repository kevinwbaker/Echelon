<?php
$page = "active";
$page_title = "Inactive Admins";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_edit";
$order = "asc";

$time = time();
$length = 7; // default length in days that the admin must be in active to show on this list

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o']) 
	$order = addslashes($_GET['o']);
	
if($_GET['d'])
	$length = addslashes($_GET['d']);

// allowed things to sort by
$allowed_orderby = array('id', 'name', 'connections', 'group_bits', 'time_edit');
if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
	$orderby = 'time_edit'; // if not just set to default id

// allowed times to limit by
$allowed_length = array(1, 3, 7, 14, 21, 28, 35, 182, 365);
if(!in_array($length, $allowed_length)) // Check if the sent varible is in the allowed array 
	$length = 7; // reset to default of 7 days
	
## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;


###########################
####### Query Setup #######

$query = sprintf("SELECT c.id, c.name, c.connections, c.time_edit, g.name as level
	FROM clients c LEFT JOIN groups g ON c.group_bits = g.id
	WHERE c.group_bits >= 8
	AND(%d - c.time_edit > %d*60*60*24 )", $time, $length);

$query .= sprintf("ORDER BY %s ", $orderby);

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

<div class="col-lg-11 mx-auto my-2">
<div class="card my-2">
<h5 class="card-header">Inactive Admins</h5>
<div class="card-body table table-hover table-sm table-responsive">
<table width="100%">
		<form action="active.php" method="get" class="sm-f-select">
        <div class="row my-1">
        <small class="my-auto mx-3">There are <strong><?php echo $total_rows; ?></strong> admins who have not been seen by B3 for</small>
			<select class="form-control col-md-2" name="d" onchange="this.form.submit()">
				<option value="1"<?php if($length == '1') echo ' selected="selected"'; ?>>1 Day</option>
				<option value="3"<?php if($length == '3') echo ' selected="selected"'; ?>>3 Days</option>
				<option value="7"<?php if($length == '7') echo ' selected="selected"'; ?>>1 Week</option>
				<option value="14"<?php if($length == '14') echo ' selected="selected"'; ?>>2 Weeks</option>
				<option value="21"<?php if($length == '21') echo ' selected="selected"'; ?>>3 Weeks</option>
				<option value="28"<?php if($length == '28') echo ' selected="selected"'; ?>>4 Weeks</option>
				<option value="35"<?php if($length == '35') echo ' selected="selected"'; ?>>5 Weeks</option>
				<option value="182"<?php if($length == '182') echo ' selected="selected"'; ?>>6 Months</option>
				<option value="365"<?php if($length == '365') echo ' selected="selected"'; ?>>1 Year</option>
			</select>
            </div>
		</form>
	</caption>
	<thead>
		<tr>
			<th>Name
				<?php linkSort('name', 'Name'); ?>
			</th>
			<th>Client-ID
				<?php linkSort('id', 'Client-id'); ?>
			</th>
			<th>Level
				<?php linkSort('group_bits', 'Level'); ?>
			</th>
			<th>Connections
				<?php linkSort('connections', 'Connections'); ?>
			</th>
			<th>Last Seen
				<?php linkSort('time_edit', 'Last Seen'); ?>
			</th>
			<th>
				Duration
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
	if($num_rows > 0) { // query contains stuff
	 
		foreach($data_set as $info): // get data from query and loop
			$cid = $info['id'];
			$name = $info['name'];
			$level = $info['level'];
			$connections = $info['connections'];
			$time_edit = $info['time_edit'];
			
			## Change to human readable		
			$time_diff = time_duration($time - $time_edit, 'yMwd');		
			$time_edit = date($tformat, $time_edit); // this must be after the time_diff
			$client_link = clientLink($name, $cid);

			$alter = alter();
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client_link</strong></td>
				<td>@$cid</td>
				<td>$level</td>
				<td>$connections</td>
				<td><em>$time_edit</em></td>
				<td><em>$time_diff</em></td>
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	} else {
		$no_data = true;
		echo '<tr class="odd"><td colspan="6">There are no admins that have been in active for more than '. $length . ' days.</td></tr>';
	} // end if query contains information
	?>
	</tbody>
</table>
</div></div></div>
<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>
