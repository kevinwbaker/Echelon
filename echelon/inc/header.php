<?php
## if the page has the normal query process & there is a connectionn to the B3 DB
if($query_normal && (!$db_error)) :
	$results = $db->query($query_limit);

	$num_rows = $results['num_rows']; // the the num_rows
	$data_set = $results['data']; // seperate out the return data set
endif;

## Pagination for pages with tables ## 
if($pagination == true && (!$db_error)) : // if pagination is needed on the page
	## Find total rows ##
	$total_num_rows = $db->query($query, false); // do not fetch the data
	$total_rows = $total_num_rows['num_rows'];
	
		$query_string_page = queryStringPage();
	
	// create query_string
	if($total_rows > 0) {

		$total_pages = totalPages($total_rows, $limit_rows);
		
		if($page_no > $total_pages) {
			$db->error = true;
			$db->error_msg = 'That page does not exists, please select a real page.';
		}
	} else
		$total_pages = 0;


endif;
date_default_timezone_set($_SESSION['timezone']);
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">
    
	<!-- Load CSS Stylesheet -->
	<link href="<?php echo PATH; ?>css/stylesheet.min.css" rel="stylesheet">
    
    <title><?php echo $site_name; ?> Echelon - <?php echo $page_title; ?></title>
	<!-- favicon -->
	<link rel="shortcut icon" href="favicon.ico" >
	    
	<?php 
		
	// return any plugin CSS files
	if(!$no_plugins_active)
		$plugins->getCSS();

	?>

</head>

<body id="<?php echo $page; ?>">

<div id="page-wrap">

	
<div id="mc">

<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand" href="<?php echo PATH; ?>" title="Home Page"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAEOUlEQVRoQ+2ZWci1UxTHf19RZEjmG4oMGS5EbkxFcoUkhAuzZLwwJlIyZJbxQuZChpDhhszDDRnKnCk3kqHMitBPa3+tbzvPdM53zttT37p63332Wnv99xr3epYxclo2cv1ZBWCpLThPC6wN7BQA3wF+mQfYlQVgNWAv4GBgb2BLYI1K4T+Az4EXgEeBl4G/ZgU1K4BNgQuAI4H1ByrzA3A/cCnwzUDe5dunBbAWcBZwLuDfs5CudRVwHfDrUEHTANgZeBzYrDrsT2D1WPsbuCUUc0mgp8HyrJf3FjFfAQcBbw8BMRTAocA9wJrpkA+Au4Erk4K61AOVIkeEy7j8D3AecAywfdr3O3A08HBfEEMAnJNuVPnfA67dC7wI7BGH3gqc2qCAVjklfnslAv4o4Gpgg8SjxVzrpL4AvPmHkrT3gAOBL4BdgDfjN/15C+C7hpM3BL5McSPvW8HzBLBj4jusjyX6ANDnX01u8wxwCPBzHHYbcGL8fS1wdse1XRMJwG3ynhT71wEeAfaL/3Wn3btioguAGebDFLDe/G5JeWPh23SjFq53OwC4pwSqFtsYUFlJEK8nSxjYxkhjduoCcBFwcQjX53cNtyk67gs8G/98DGwXAdqGwTM/AraJTcp4LjHogm+kmFCHS5oEtgHYBPgs3e5xwF2VoCsim7jcFrz1+e49ORaVcX61wbPuSHG1VVOxawNwU+TuzkywgA3qcsakc5oA2NtY3oe2B/PCovvatvyvd2oCsE/ll/NSbIhcdbIRXIGaANycipEBZmGpyRR3Yyx+Dew/RBvg6bhV2XSP1ybwW8xUXFKn0/sCMF3uEJtPSAGV+a22CpWsqrbTQ8h2es9gUJaBXdPxwO2x+H5V6P5bbrLAj8C6wahiKliTmePyWHwKOGCI9sCTyWrKMhvV5NkvxaI6rdfHAr6kSpV1/9bApxOEq3xJfw8Chw8EII/tgqQs3xU1efYnaVHdVihqkyywbRSawmd1nPQc1P+LT94JaO4hJM+xwdCUJj37pyRU3TKgiS40egCjdyEtNuogFsDo02guZM+3FLIbIsAsZEPTqKnX9kCykNlG1+Rjf6pCNvpWYvTNnKYcdTstgNE/aAQx6ielABb9qLeBNBuVTnjmR70glmqs8lsMy1pHjV1TiZKXV9Zga6OYapSBcBlsOY53sFVu3nM90zlRK/UFoJBJo0Vfao4WfepNM1q03jhatGDNdbSYLVEPdx18OW7pGu468L0vBJXhru20s6RCDrgE1HnzhWGIBQqPMfEYsHll2zwyV0HbEceIkuPGPF53umCxzLSQ8Xo5UB8+M3ok2+9ZyMeS1rt+UR84srIWuwsBZ//Zh/sActbjJ6bLluITU62g7uCEwY98BmbbRz67Wz/yOShY8o98bTetm+XPrIO/f/Ux4zRB3EfuwvasArCwq244aPQW+BeVoBZAUVOunQAAAABJRU5ErkJggg==" id="h-img"></a></li>

        <!-- Mobile UI Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
        </button>
<div class="collapse navbar-collapse" id="navbarNavDropdown">
<!--       
        <ul class="navbar-nav">
            <li class="nav-item  my-auto">
                <a class="nav-link text-uppercase disabled" href="#">Serverstatus</a>
            </li>
        </ul>
-->
		<ul class="navbar-nav">
        <?php if($mem->loggedIn()) { if(!$no_games) : ?>
        
        
          <li class="nav-item dropdown">        
			<a class="nav-link dropdown text-uppercase" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Games</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				<?php
					$this_cur_page = basename($_SERVER['SCRIPT_NAME']);						
					$games_list = $dbl->getGamesList();
					$i = 0;
					$count = count($games_list);
					$count--; // minus 1
					while($i <= $count) :

						#if($game == $games_list[$i]['id'])
							#echo ' class="selected">';
						#else
						#	echo ''; eine zeile drunter fehlt am ende noch ein li tag
						echo '<a class="dropdown-item" href="'.PATH . $this_cur_page .'?game='.$games_list[$i]['id'].'" title="Switch to this game">'.$games_list[$i]['name'].'</a>';
						
						$i++;
					endwhile;
				?>	
            </div>
         </li>
            
        
		<?php if($mem->reqLevel('clients')) : ?>
		<li class="nav-item dropdown">
			<a class="nav-link dropdown text-uppercase" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Clients</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				<a class="dropdown-item" href="<?php echo PATH; ?>clients.php" title="Clients Listing"><i class="fas fa-users mx-2"></i>Clients</a>
				<a class="dropdown-item" href="<?php echo PATH; ?>regular.php" title="Regular non admin visitors to your servers"><i class="fas fa-user-friends mx-2"></i>Regular Visitors</a>
				<a class="dropdown-item" href="<?php echo PATH; ?>admins.php" title="A list of all admins"><i class="fas fa-user-shield mx-2"></i>Admin Listing</a>
                <a class="dropdown-item" href="<?php echo PATH; ?>active.php" title="In-active admins"><i class="fas fa-user-clock mx-2"></i>In-active Admins</a>
				<a class="dropdown-item disabled" href="<?php echo PATH; ?>map.php" title="Player map"><i class="fas fa-globe-africa mx-2"></i>World Player Map</a>
			</div>
		</li>
		<?php
			endif; // reqLevel clients DD
			
			if($mem->reqLevel('penalties')) :
		?>
		<li class="nav-item dropdown">
			<a class="nav-link dropdown text-uppercase" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Penalties</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				<a class="dropdown-item" href="<?php echo PATH; ?>adminkicks.php"><i class="fas fa-user-times mx-2"></i>Admin Kicks</a>
				<a class="dropdown-item" href="<?php echo PATH; ?>bans.php?t=a"><i class="fas fa-user-slash mx-2"></i>Admin Bans</a>
				<a class="dropdown-item" href="<?php echo PATH; ?>bans.php?t=b" title="All Kicks/Bans added automatically by B3"><i class="fas fa-robot mx-2"></i>B3 Bans & Kicks</a>
                <a class="dropdown-item" href="<?php echo PATH; ?>toppen.php"><i class="fas fa-sort-amount-up mx-2"></i>Toplist Penalties</a>
				<a class="dropdown-item" href="<?php echo PATH; ?>pubbans.php" title="A public list of bans in the database"><i class="fas fa-book-open mx-2"></i>Public Ban List</a>
                <a class="dropdown-item" href="<?php echo PATH; ?>notices.php" title="In-game Notices"><i class="fas fa-flag mx-2"></i>Notices</a>
			</div>
		</li>
		<?php
			endif; // end reqLevel penalties DD
            endif; 
        ?>
        
        
		<li class="nav-item dropdown">
			<a class="nav-link dropdown text-uppercase" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Settings</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				<?php if($mem->reqLevel('siteadmin')) : ?>
                <div class="dropdown-header">ECHELON SETTINGS</div>
					<a class="dropdown-item" href="<?php echo PATH; ?>sa.php" title="Site Administration"><i class="fas fa-cogs mx-2"></i>Site Admin</a>
                    <a class="dropdown-item" href="<?php echo PATH; ?>settings.php"><i class="fas fa-cog mx-2"></i>Site Settings</a>
					<a class="dropdown-item" href="<?php echo PATH; ?>sa.php?t=perms" title="User Permissions Management"><i class="fas fa-users-cog mx-2"></i>Permissions</a>
				<?php endif; ?>            
				<?php if($mem->reqLevel('manage_settings')) : ?>
                <div class="dropdown-header">B3 GAME SETTINGS</div>         
                    <a class="dropdown-item" href="<?php echo PATH; ?>settings-games.php" title="Game Settings"><i class="fas fa-gamepad mx-2"></i>Game Settings</a>
                    <a class="dropdown-item" href="<?php echo PATH; ?>settings-server.php" title="Server Settings"><i class="fas fa-server mx-2"></i>Server Settings</a>
				<?php endif; ?>
				<div class="dropdown-header">ACCOUNT SETTINGS</div>         
				<a class="dropdown-item" href="<?php echo PATH; ?>me.php" title="Edit your account"><i class="fas fa-user-circle mx-2"></i>My Account</a>
            </div>
        </li>

        <?php if(!$no_plugins_active AND $plugins->NavExists()): ?>
		
		<li class="nav-item dropdown">
			<a class="nav-link dropdown text-uppercase" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Other</a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                    <?php 
                            $plugins->displayNav(); 
                    ?>
			</div>
		</li>
        <?php endif; // end if no games hide the majority of the navigation ?>


		<?php } else { // if user has no permissions (e.g. visitor or group without any permisions ?> 
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-uppercase" href="<?php echo PATH; ?>pubbans.php" title="Public Ban List">Public Ban List</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link text-uppercase" href="<?php echo PATH; ?>login.php" title="Login to Echelon">Login</a>
            </li>
        </ul>
		<?php } ?>

    </ul>
  
<!-- end #navbar , now user info stuff-->

    
        <?php if($mem->loggedIn()) { ?>	
            <ul class="navbar-nav ml-auto">
            
            <form class="form-inline" action="clients.php" method="get" id="c-search">
<div class="loader" id="c-s-load"></div>
<div class="input-group input-group-sm mx-4">
		<select class="custom-select custom-select-sm col-md-5" name="t">
			<option value="all" <?php if($search_type == "all") echo 'selected="selected"' ?>>All Records</option>
			<option value="alias" <?php if($search_type == "alias") echo 'selected="selected"' ?>>Name</option>
			<option value="pbid" <?php if($search_type == "pbid") echo 'selected="selected"' ?>>PBID</option>
			<option value="ip" <?php if($search_type == "ip") echo 'selected="selected"' ?>>IP Address</option>
			<option value="id" <?php if($search_type == "id") echo 'selected="selected"' ?>>Player ID</option>
            <option>===========</option>
            <option value="ipaliastable" <?php if($search_type == "ipaliastable") echo 'selected="selected"' ?>>IP-Alias</option>
            <option value="aliastable" <?php if($search_type == "aliastable") echo 'selected="selected"' ?>>Alias</option>
		</select>
        
		<div class="form-control suggestionsBox" id="suggestions" style="display: none;">
			<div class="suggestionList" id="suggestionsList">&nbsp;</div>
		</div>        
        
  <input class="form-control" type="text" autocomplete="off" name="s" id="search" onkeyup="suggest(this.value);" onblur="fill();" value="">
  <div class="input-group-append">
                <button class="btn btn-outline-secondary" id="sub-search" value="Search" type="submit">
                    <i class="fa fa-search"></i>
                </button>
  </div>
</div>
</form>
            
           
    <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-uppercase" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php if($mem->loggedIn()){ $mem->displayName();} ?></a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">		
            <a class="dropdown-item" href="<?php echo PATH; ?>actions/logout.php" class="logout" title="Sign out"><i class="fas fa-sign-out-alt mx-2"></i>Sign Out</a>
            </div>
            	</li>
        </ul>

        <?php } ?>        
    
</div>			
</nav>	    
    
    
</div><!-- end #menu -->
<div id="container"> <!-- QUALITY CONTENT -->

	<?php 

    if(!isHome())
        errors(); // echo out all errors/success/warnings

	if($query_normal) : // if this is a normal query page and there is a db error show message
	
		if($db->error)
			dbErrorShow($db->error_msg); // show db error

	endif;