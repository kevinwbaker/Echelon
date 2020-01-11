<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

/**
 * class xlrstats
 * desc: File to deal with Echelon plugin XLRstats
 *
 */ 

class xlrstats extends plugins {

	public static $instance;
	public $name;
	
	function getClass() {
		$name =	get_class($this);
		$this->name = $name;
		return $name;
	}
	
	/**
	 * You may edit below here
	 */
	
	public $xlr_user = false;
	public $xlr_hide = 0;
	public $xlr_fixed_name = NULL;
	
	/**
	 * Gets the current instance of the class, there can only be one instance (this make the class a singleton class)
	 * note: this is needed as a work around for the inc.php file do not change
	 * 
	 * @return object $instance - the current instance of the class
	 */
	public static function getInstance() {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
	
	public function __construct() {
		parent::__construct($this->getClass());
	
		parent::setTitle('XLRstats');
		parent::setVersion(1.0);
	}
	
	public function __destruct() {
		parent::__destruct();
	}
	
	/**
	 * Get the title of the plugin
	 */
	public function getTitle() {
		return parent::getTitle();
	}
	
	public function returnClientFormTab() {
		
		global $mem; // use the member class instance from outside this class
	
		if($mem->reqLevel('edit_xlrstats'))
			return '<a class="nav-link" id="setxlr-tab" rel="setxlr" data-toggle="pill" href="#setxlr" role="tab" aria-controls="setxlr" aria-selected="false"><h6 class="my-auto">XLR Options</h6></a>';

	}// end returnClientFormTab
	
	public function returnClientNavTab() {
		
		global $mem; // use the member class instance from outside this class
	
		if($this->xlr_user)
			return '<a class="nav-link" id="xlr-tab" href="#xlr" data-toggle="tab" role="tab" aria-controls="xlr" aria-selected="false"><h6 class="my-auto">XLRStats</h6></a>';

	}// end returnClientFormTab    
    
	public function returnClientForm($cid) {
	
		if(empty($cid))
			return NULL;
	
		global $mem; // use the member class instance from outside this class
	
		if($mem->reqLevel('edit_xlrstats')) :
	
			$xlr_token = genFormToken('xlrstats');
	
			if($this->xlr_hide == 1) 
				$hide = 'checked="checked"';
	
			$data = '<div class="tab-pane fade" id="setxlr" role="tabpanel" aria-labelledby="setxlr-tab">
                <div class="col justify-center">
				<form action="lib/plugins/'.__CLASS__.'/actions.php" method="post">
				<div class="form-row">    
					<label class="col-form-label col-sm-4" for="xlr-name">Fixed Name</label>
                        <div class="col-md-4">    
						<input class="form-control" type="text" name="fixed-name" value="'. $this->xlr_fixed_name .'" id="xlr-name" />
					</div></div>
                    <div class="form-row">
					<label class="col-sm-4 my-2" for="xlr-hide">Hide Stats?</label>
                    
                        <div class="col-md-24 my-2">
                        <label class="switch" name="pb" id="pb">
                          <input type="checkbox" name="hidden" id="xlr-hid" '.$hide.'>
                          <span class="slider round"></span>
                        </label></div></div>                    
						
					
					<input type="hidden" name="cid" value="'.$cid.'" />
					<input type="hidden" name="token" value="'. $xlr_token .'" />
					<button class="btn btn-primary float-right my-auto" type="submit" name="xlrstats-sub">Save Changes</button>
				</form>
			</div>
            </div>';
		
			return $data;
			
		else:
			return NULL;
		
		endif;
	
	} // end returnClientForm
	
	/**
	 * Internal function to connect to the DB and retrieve clients XLR bio Infomation
	 */
	private function getClientBio() {
	
		$db = DB_B3::getPointer(); // get the pointer to the current B3 connection
		global $cid;
	
		## Get information for xlrstats ##
		$query_xlr = "SELECT id, kills, deaths, ratio, skill, rounds, hide, fixed_name FROM xlr_playerstats WHERE client_id = ? LIMIT 1";
		$stmt = $db->mysql->prepare($query_xlr) or die('Database Error');
		$stmt->bind_param('i', $cid);
		$stmt->execute();
		$stmt->store_result();

		if($stmt->num_rows > 0) {
			
			$this->xlr_user = true;
			$stmt->bind_result($id, $kills, $deaths, $ratio, $skill, $rounds, $hide, $fixed_name);
			$stmt->fetch();
			
			$results = array(
				'id' => $id,
				'kills' => $kills,
				'deaths' => $deaths,
				'ratio' => $ratio,
				'skill' => $skill,
				'rounds' => $rounds,
				'hide' => $hide,
				'fixed_name' => $fixed_name
			);
			
			$this->xlr_fixed_name = $fixed_name;
			$this->xlr_hide = $hide;
			
		} else
			$this->xlr_user = false;

		$stmt->free_result();
		$stmt->close();
		
		return $results;
			
	}
	
	## Main Function ##
	public function returnClientBio() {
	
		$result = $this->getClientBio();
	
		if($this->xlr_user) :
		
			$ratio = number_format($result['ratio'], 2, '.', '');
			$skill = number_format($result['skill'], 2, '.', '');
			
			if(empty($result['fixed_name'])) 
				$name =  "Non Set";
			else 
				$name = $result['fixed_name'];
			
			if($this->xlr_hide == 1) 
				$hide = "Yes";
			else
				$hide = "No";

			$data = '
            <div class="justify-content-center table table-hover table-responsive table-sm">
            <table width="100%" id="xlrstats-table">
				<tbody>
				<tr>
					<th>Kills</th>
						<td>'.$result['kills'].'</td>
					<th>Deaths</th>
						<td>'.$result['deaths'].'</td>
				</tr>
				<tr>
					<th>Ratio</th>
						<td>'.$ratio.'</td>
					<th>Skill</th>
						<td>'.$skill.'</td>
				</tr>
				<tr>
					<th>Rank</th>
						<td>(Not Working)</td>
					<th>XLRStats id</th>
						<td>'.$result['id'].'</td>
				</tr>
				<tr>
					<th>Fixed Name</th>
						<td>'.$name.'</td>
					<th>Hidden</th>
						<td>'.$hide.'</td>
				</tr>
				</tbody>
			</table>
            </div>';
			
			return $data;
		
		endif;
		
	} // end displayXLRclient

} // end class