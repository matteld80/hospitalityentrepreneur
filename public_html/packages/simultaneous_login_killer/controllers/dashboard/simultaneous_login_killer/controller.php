<?php  
defined('C5_EXECUTE') or die(_("Access Denied"));

/**
 * @author 		Nour Akalay (mnakalay)
 * @copyright  	Copyright 2013 Nour Akalay
 * @license     concrete5.org marketplace license
 */

class DashboardSimultaneousLoginKillerController extends Controller {

	public function view(){
		$this->redirect('/dashboard/simultaneous_login_killer/list');
	}

}