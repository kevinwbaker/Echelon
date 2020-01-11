<?php
$page = "adminkicks";
$page_title = "Admin Kicks";
$auth_name = 'penalties';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true; // this is a normal query page, so evoke query function in header.php
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "time_add";
$order = "DESC";

//$limit_rows = 75; // limit_rows can be set by the DB settings // uncomment this line to manually overide the number of table rows per page

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o'])
	$order = addslashes($_GET['o']);

// allowed things to sort by
$allowed_orderby = array('target_name', 'time_add', 'admins_name');
if(!in_array($orderby, $allowed_orderby)) { // Check if the sent varible is in the allowed array 
	$orderby = 'time_add'; // if not just set to default id
}

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;

###########################
######### QUERIES #########

$query = "SELECT p.time_add, p.reason, target.id as target_id, target.name as target_name, c.id as admin_id, c.name as admins_name FROM penalties p, clients c, clients as target WHERE admin_id != '0' AND p.type = 'Kick' AND inactive = 0 AND p.client_id = target.id AND p.admin_id = c.id";

$query .= sprintf(" ORDER BY %s ", $orderby);

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
<div class="card-header">
    <h5 class="my-auto">Admin Kicks</h5></div>
    <div class="card-body table table-hover table-sm table-responsive">
    <table width="100%">
	<thead>
		<tr>
			<th>Client
				<?php linkSort('target_name', 'client name'); ?>
			</th>
			<th>Kicked At
				<?php linkSort('time_add', 'time the penalty was added'); ?>
			</th>
			<th>Reason</th>
			<th>
				Admin
				<?php linkSort('admins_name', 'admin name'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="4"></th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	if($num_rows > 0) : // query contains stuff

		foreach($data_set as $data): // get data from query and loop
			$time_add = $data['time_add'];
			$reason = tableClean($data['reason']);
			$client_id = $data['target_id'];
			$client_name = tableClean($data['target_name']);
			$admin_id = $data['admin_id'];
			$admin_name = tableClean($data['admins_name']);

			## Tidt data to make more human friendly
			if($time_expire != '-1')
				$duration_read = time_duration($duration*60); // all penalty durations are stored in minutes, so multiple by 60 in order to get seconds
			else
				$duration_read = '';

			$time_add_read = date($tformat, $time_add);
			$reason_read = removeColorCode($reason);
			$client_link = clientLink($client_name, $client_id);
			$admin_link = clientLink($admin_name, $admin_id);
			
			## Row color
			$alter = alter();

			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client_link</strong></td>
				<td>$time_add_read</td>
				<td>$reason_read</td>
				<td><strong>$admin_link</strong></td>
			</tr>
EOD;

		echo $data;
		endforeach;
	else:
		$no_data = true;
		echo '<tr class="odd"><td colspan="4">There are no kicks in the database</td></tr>';
	endif; // no records
	?>
	</tbody>
</table>
</div></div></div>
<?php 
endif;

require 'inc/footer.php'; 
?>
