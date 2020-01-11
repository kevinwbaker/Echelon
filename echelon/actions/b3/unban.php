<?php
$auth_name = 'unban';
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';

## Check the form was submitted ##
if(!$_POST['unban-sub']) { // if the form not is submitted
    send('../../index.php');
	set_error('Please do not call that unban page directly, thank you.');
}

## get vars ##
$ban_id = $_POST['banid'];
$type = cleanvar($_POST['type']);
$cid = cleanvar($_POST['cid']);

## check that the sent form token is correct ## #wrong token error

#if(verifyFormToken('unban'.$ban_id, $tokens) == false) // verify token
#	ifTokenBad('Unban');

## Check for empties ##
emptyInput($type, 'data not sent');
emptyInput($ban_id, 'data not sent');

## Check ban_id is a number ##
if(!isID($ban_id))
	sendBack('Invalid data sent, ban not added');

## Send query ##
$results = $db->makePenInactive($ban_id);

if(!$results) // if bad send back warning
	sendBack('Penalty has not been removed');
	
$guid = $db->getGUIDfromPID($ban_id);
$i = 1; 
if($i <= $game_num_srvs) : // only needs to be sent once, if a shared db is used
    
    $rcon_pass = $config['game']['servers'][$i]['rcon_pass'];
    $rcon_ip = $config['game']['servers'][$i]['rcon_ip'];
    $rcon_port = $config['game']['servers'][$i]['rcon_port'];
    
    $command = "unban " .$guid;
    rcon($rcon_ip, $rcon_port, $rcon_pass, $command); // send the ban command
endif;
 
    
## If a permban send unban rcon command ##
if($type == 'Ban' AND $config['game']['servers'][$i]['pb_active'] == '1') :

	## Get the PBID of the client ##
	$pbid = $db->getPBIDfromPID($ban_id);
	
	## Loop thro server for this game and send unban command and update ban file
	$i = 1;
	while($i <= $game_num_srvs) :

		if($config['game']['servers'][$i]['pb_active'] == '1') {
			// get the rcon information from the massive config array
			$rcon_pass = $config['game']['servers'][$i]['rcon_pass'];
			$rcon_ip = $config['game']['servers'][$i]['rcon_ip'];
			$rcon_port = $config['game']['servers'][$i]['rcon_port'];
		
			// PB_SV_BanGuid [guid] [player_name] [IP_Address] [reason]
			$command = "pb_sv_unbanguid " . $pbid;
			rcon($rcon_ip, $rcon_port, $rcon_pass, $command); // send the ban command
			sleep(1); // sleep for 1 sec in ordere to the give server some time
			$command_upd = "pb_sv_updbanfile"; // we need to update the ban files
			rcon($rcon_ip, $rcon_port, $rcon_pass, $command_upd); // send the ban file update command
		}

		$i++;
	endwhile;

endif;

$comment = 'Unbanned Ban-ID '.$ban_id;
$dbl->addEchLog('Unban', $comment, $cid, $mem->id, $game);

if($results) // if good results send back good message
	sendGood('Penalty has been deactivated.');
	
exit;
