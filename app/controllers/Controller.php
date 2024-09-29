<?php

class Controller {

	protected $f3;
	protected $db;

    function beforeroute() {
		if($this->f3->get('SESSION.logged_in'))
		{
			$user_id=$this->f3->get('SESSION.user_id');
			$user = new User($this->db);
			$user->getById($user_id);
			$persistent_timeout = 0;
			if($user != NULL){
				$persistent_timeout =  ($user->remember_me)?$this->f3->get('persistent_timeout'):0;
			}
			if(((time() + $persistent_timeout) - $this->f3->get('SESSION.timestamp') ) > $this->f3->get('auto_logout')) 
			{
				$user->rememberme($user->id, 0);
				$this->f3->clear('SESSION');
				$this->f3->reroute('/login');
			} 
			else {
				$this->f3->set('SESSION.timestamp', time());
			}
		}
		$csrf_page = $this->f3->get('PARAMS.0'); //URL route !with preceding slash!

		if( NULL === $this->f3->get('POST.session_csrf') )
		{
			$this->f3->CSRF = $this->f3->session->csrf();
			$this->f3->copy('CSRF','SESSION.'.$csrf_page.'.csrf');
		}
		if ($this->f3->VERB==='POST')
		{
			if(  $this->f3->get('POST.session_csrf') ==  $this->f3->get('SESSION.'.$csrf_page.'.csrf') ) 
			{	// Things check out! No CSRF attack was detected.
				$this->f3->set('CSRF', $this->f3->session->csrf()); // Reset csrf token for next post request
				$this->f3->copy('CSRF','SESSION.'.$csrf_page.'.csrf');  // copy the token to the variable
			}
			else
			{	
				
				// DANGER: CSRF attack!
				$this->f3->error(403); 
			}
		}
    }

	function afterroute() {
		echo Template::instance()->render('layout.htm');
	}

	function __construct() {
		$f3=Base::instance();
		$db=new DB\SQL(
			$f3->get('db_dns') . $f3->get('db_name'),
			$f3->get('db_user'),
			$f3->get('db_pass')
		);
		$this->f3=$f3;
		$this->db=$db;
	}	
}
