<?php
## NOTE: this page deals with both the request from the change client user level as well the requests to change a user's mask level ##
if($_POST['level-sub']) // what kind of request is it
	$is_mask = false;
else
	$is_mask = true;

if(!$is_mask) // check which auth level is needed
	$auth_name = 'edit_client_level';
else
	$auth_name = 'edit_mask';
	
$b3_conn = true; // this page needs to connect to the B3 database
require '../../inc.php';

## check that the sent form token is corret
if(!$is_mask) {
	if(verifyFormToken('level', $tokens) == false) // verify token
		ifTokenBad('Change client level');
} else {
	if(verifyFormToken('mask', $tokens) == false) // verify token
		ifTokenBad('Change client mask level');
}

## Set and clean vars ##
$level = cleanvar($_POST['level']);
$client_id = cleanvar($_POST['cid']);
$old_level = cleanvar($_POST['old-level']);
$password = cleanvar($_POST['password']);

## Check Empties ##
emptyInput($level, 'data not sent /level');
emptyInput($client_id, 'data not sent /client-id');
if ($old_level == "") # b3 set level names for regular players, will return "empty field" exception otherwise
    $old_level == "Un-Registered";
if(!$is_mask) // only the client level needs a password
	emptyInput($password, 'current password');

## Check if the client_id is numeric ##
if(!isID($client_id))
	sendBack('Invalid data sent, greeting not changed');

## Do some mojo with the B3 group information ##
$b3_groups = $db->getB3Groups();

// change around the recieved data
$b3_groups_id = array();
foreach($b3_groups as $group) :
	array_push($b3_groups_id, $group['id']); // make an array of all the group_bits that exsist
	$b3_groups_name[$group['id']] = $group['name']; // make an array of group_bits to matching names
endforeach;

// Check if the group_bits provided match a known group (Known groups is a list of groups pulled from the DB -- this allow more control for custom groups)
if(!in_array($level, $b3_groups_id))
	sendBack('That group does not exist, please submit a real group');

## Check that authorisation passsword is correct ##
if($config['cosmos']['pw_req_level'] == 1 && !$is_mask) : // if requiring a pw auth for edit-level is on or off
	if($level >= $config['cosmos']['pw_req_level_group']) // site setting to see if only certain levels need a pw check and if the selected level is above the threshold
		$mem->reAuthUser($password, $dbl);
endif;

## Add Echelon Log ##
$level_name = $b3_groups_name[$level];
$old_level_name = $b3_groups_name[$old_level];
if($old_level_name == "")
    $old_level_name == 'Un-Registered';
 
if(!$is_mask)
	$comment = 'User level changed from '. $old_level_name .' to '. $level_name;
else
	$comment = 'Mask level changed from '. $old_level_name .' to '. $level_name;


$dbl->addEchLog('Level Change', $comment, $client_id, $mem->id, $game);

## Query Section ##
if(!$is_mask)
	$query = "UPDATE clients SET group_bits = ? WHERE id = ? LIMIT 1";
else
	$query = "UPDATE clients SET mask_level = ? WHERE id = ? LIMIT 1";
	
$stmt = $db->mysql->prepare($query) or sendBack('Database Error');
$stmt->bind_param('ii', $level, $client_id);
$stmt->execute();

if($is_mask)
	$msg_st = 'Mask';
else
	$msg_st = 'User';

if($stmt->affected_rows)
	sendGood($msg_st.' level has been changed');
else
	sendBack($msg_st.' level was not changed');

$stmt->close(); // close connection