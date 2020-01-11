<?php
$page = "home";
$page_title = "Home";
$auth_name = 'login';
$auth_user_here = true;
$b3_conn = false;
$pagination = false;
require 'inc.php';


require 'inc/header.php';
?>
    <main role="main">

      <div class="jumbotron">
        <div class="container">
    
	<?php if($_SESSION['last_seen'] == 0 && $_SESSION['username'] == 'admin') : /* Show this message to the admin user (the first user create) only on their first visit */ ?> 
	
		<div class="msg success">
			<p>Welcome to Echelon for the first time, now all you need to do is good to the 'Echelon' tab in the navigation up above. It is suggested that you change the settings, and setup game and server information for Echelon.</p>
		</div>
		
	<?php endif; 
    
	## if Site Admin check for current Echelon Version and if not equal add warning
	if($mem->reqLevel('see_update_msg')) :
		if(isHome()) {
			$latest = getEchVer();
			if(ECH_VER !== $latest && $latest != false) // if current version does not equal latest version show warning message
				set_warning('You are not using the lastest version of Echelon. Please check the <a href="https://github.com/miltann/Echelon-2">Echelon Github repository</a> for the latest version.');
		}
	endif;
    	errors(); // echo out all errors/success/warnings
    
    ?>
	     
    <h1 class="display-3">Welcome, <?php echo $mem->displayName();?>!</h1>
    <?php if(!$no_games) : ?>
    <p>You are logged into the &ldquo;<?php echo $game_name; ?>&rdquo; database. You can change what game information you would like to see under the 'game' dropdown above.<?php endif; ?>
    <?php if($_SESSION['last_seen'] != 0) : ?> 
    <br>Your last visit was on <?php $mem->lastSeen('l, jS F Y (H:i)'); ?>.
    <?php endif; ?>
	</p>
	<p><?php if(!$no_games) : ?><a class="btn btn-primary btn-lg" href="clients.php?ob=time_edit&o=DESC" title="Enter the repository and start exploring Echelon">Enter the Repository</a><?php endif; ?>
    </p>	
        </div>
    </div>
<div id="index">
    <div class="container">
        <h3 class="my-2">Latest Announcements</h3>
        <div class="row mt-4 my-4">
          <div class="col-md-4">
            <h5>New design for Echelon!</h5>
            <p> <?php echo $config['cosmos']['newsfeed']; /* sets news, which can be set in the echelon settings page */ ?></p>
            <!--You shall now be able to use Echelon . For techminded people: I've used Bootstrap 4 to completely redesign Echelon.-->
          </div>
          <div class="col-md-4">
            <h5>Added Features</h5>
            <p><ul><li>You can see your XLRStats here</li><li>Echelon Logs FIXED!</li><li>issues with setting levels</li><li>add security (SSL)</li><li>added IP-Alias feature</li><li>some design improvements (alignment &amp; font)</li><li>fixed alias and ip alias feature (now shows all aliases logged)</li><li>some php errors</li><li>fixed registration keys</li></ul></p>
          </div>
          <div class="col-md-4">
            <h5>Coming soon...</h5>
            <p><ul><li>Live permban, meaning player will be banned instantly if he is online on our servers </li><li>Chatlogger &amp; chatting with clients online and checking chatlogs</li></ul></p>
          </div>
        </div>
</div>
</div>
</main>    
	
	<?php /*
		## External Links Section ##
		$links = $dbl->getLinks();
		
		$num_links = $links['num_rows'];
		
		if($num_links > 0) :
			
			echo '<div id="links-table" class="index-block">
					<h3>External Links</h3>
					<ul class="links-list">';
		
				foreach($links['data'] as $link) :
				
					echo '<li><a href="'. $link['url'] .'" class="external" title="'. $link['title'] .'">' . $link['name'] . '</a></li>';
				
				endforeach;
			
			echo '</ul></div>';
			
		else:
			echo 'no results';
			
		endif;
		## End External Links Section ## */
	?>

	
<?php require 'inc/footer.php'; ?>