<?php

class view {
	
	public $name;
	
	public function __construct($view) {
		$this->name = $view;
		$this->template_path = "tmpl/$view.html.php";
		$this->script_path = "views/$view.html.js";
	}
	
	public function _html() {
		return $this->template_path;
	}
	
	public function _script() {
		return $this->script_path;	
	}
	
}

?>