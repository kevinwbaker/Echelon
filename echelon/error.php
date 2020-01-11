<?php
$page = 'error';
$page_title = "Error";
$b3_conn = false; // no b3 connection is required
$auth_user_here = false; // allow both logged in and logged out users to see this page
$pagination = false;
require 'inc.php';

require 'inc/header.php';
?>
	
	<div class="col-md-6 error-msg error my-3 mx-auto">
    <div class="card card-signin my-2">
    

	<?php 
		if($_GET['t'] == 'locked') {
			echo '<h5 class="card-header">Locked Out!</h5><div class="card-body">You have been locked out of Echelon for repeated hacking attempts. 
					This ban is permanent. If you feel that you should not be banned please contact the site admin.</div>';
		} elseif($_GET['t'] == 'ie') {
			echo '<h5 class="card-header">Internet Explorer Banned!</h5><div class="card-body">Microsoft Internet Explorer is banned from this site due to secuirty concerns. Please choose a modern browser,
					such as Mozilla Firefox, Google Chrome, Apple Safari, or Opera. Thank-you.</div>';
		} elseif($_GET['t'] == 'plug') {
			echo '<h5 class="card-header">Plugin Page Failure</h5><div class="card-body">The last page you requested requires that a plugin name be sent in the request. You did not sent one.</div>';
		} elseif($_GET['t'] == 'plugpage') {
			echo '<h5 class="card-header">Plugin Page Failure</h5><div class="card-body">That plugin does not have a stand-alone page.</div>';
		} elseif($_GET['t'] == 'ssl') {
			echo '<h5 class="card-header">SSL Connection Required</h5><div class="card-body">An SSL connection is required for this site, and you did not seem to have one.</div>';
		} else {
			echo '<h5 class="card-header">Error! Error!</h5><div class="card-body">Something seems to have gone wrong! A team of highly trained monkeys have been dispatched, in an attempt to fix the problem.</div>';
		}
	?>
	</div>
    </div>
	
<?php require 'inc/footer.php'; ?>