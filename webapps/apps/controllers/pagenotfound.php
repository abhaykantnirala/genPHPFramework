<?php
/**
* 
*/
class pagenotfound extends gcontroller
{
	
	function __construct()
	{
		parent::__construct();
	}

	function index(){
		$this->load->view('Error');
	}
}