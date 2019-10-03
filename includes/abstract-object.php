<?php




if ( ! defined( 'ABSPATH' ) ) {	exit; };




abstract class pstuFAQAbstractObject {



	protected $domain;



	protected $labels;



	protected $args;



	protected $name;



	function __construct( $name, $domain ) {
		$this->name = $name;
		$this->domain = $domain;
		$this->set_fields();
	}


	protected function set_fields() {}



	public function register() {}



	public function get( $name ) {
		return $this->$name;
	}



}