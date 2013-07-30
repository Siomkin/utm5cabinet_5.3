<?php
class DRG_Mail extends Zend_Mail {
	
	public function __construct($charset = 'utf-8') {
		parent::__construct ( $charset );
		$this->setFrom ( 'darang@tut.by', 'MG7' );
	
	}
	
	public function setbodyView($script, $params = array()) {
		$layout = new Zend_Layout ( array ('layoutPath' => APPLICATION_PATH . '/views/layouts' ) );
		$layout->setLayout ( 'activationEmail' );
		$view = new Zend_View ();
		$view->setScriptPath ( APPLICATION_PATH . '/views/email' );
		foreach ( $params as $key => $value ) {
			$view->assign ( $key, $value );
		}
		$layout->content = $view->render ( $script . '.phtml' );
		$html = $layout->render ();
		$this->setBodyHtml ( $html );
		return $this;
	}
	
	public function adminEmail(){
		$this->addTo('darang@tut.by', 'DRG' );
	}

}