<?php
$page = "top_pen";
$page_title = "Top Penalties";
$auth_name = 'penalties';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'inc.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "duration";
$order = "DESC"; // pick either asc or desc


## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o']) 
	$order = addslashes($_GET['o']);

#### lastseen> order by duration, rn works without ordering bz duraation
// allowed things to sort by
$allowed_orderby = array('duration', 'avg_conn');
if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
	$orderby = 'duration'; // if not just set to default id
	
## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;


###########################
######### QUERIES #########
$query = "SELECT count(T1.id) as Penalty, count(T1.id)/T2.connections as avg_conn, sum(T1.duration) as duration, T1.client_id, T2.name FROM `penalties` T1 INNER JOIN clients T2 on T1.client_id = T2.id AND T1.inactive = 0 GROUP BY T1.client_id, T2.name";

$query .= " ORDER BY $orderby";

# Append this section to all queries since it is the same for all ##
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
<h5 class="card-header">Toplist Penalties</h5>
<div class="card-body table table-hover table-sm table-responsive">
<table width="100%">
	<thead>
		<tr>
			<th>Name</th>
			<th>Client-ID</th>
			<th>Total duration
            <?php linkSort('duration', 'duration'); ?>
            </th>
			<th>Warns per connection
            <?php linkSort('avg_conn', 'average connection'); ?>
            </th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="5">Click client name to see details.</th>
		</tr>
	</tfoot>
	<tbody>
	<?php
	if($num_rows > 0) :
	 
		foreach($data_set as $top_pen): // get data from query and loop
			$cname = tableClean($top_pen['name']);
			$cid = $top_pen['client_id'];
			$duration = tableClean($top_pen['duration']);
			
			## Change to human readable	time
			$duration = time_duration($duration*60, 'yMwdhm');
            
			## Row color
			$alter = alter();
				
			$client = clientLink($cname, $cid);
			$avgcon = $top_pen['avg_conn'];
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client</strong></td>
				<td>@$cid</td>
				<td>$duration</td>
				<td>$avgcon</td>
			</tr>
EOD;

			echo $data;
		endforeach;
		
		$no_data = false;
	else:
		$no_data = true;
		echo '<tr class="odd"><td colspan="5">There are no penalties in the database.</td></tr>';
	endif; // no records
	?>
	</tbody>
</table>
</div></div></div>
<?php 
	endif; // db error

	require 'inc/footer.php'; 
?>