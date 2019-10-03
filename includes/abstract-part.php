<?php






if ( ! defined( 'ABSPATH' ) ) {	exit; };





abstract class pstuFAQAbstractPart {



	use pstuFAQFunctions;



	protected $slug;



 	protected $version;



 	protected $domain;



 	protected $object;



 	function __construct( $slug, $version, $domain, $object ) {
 		$this->slug = $slug;
 		$this->version = $version;
 		$this->domain = $domain;
 		$this->object = $object;
 	}


 	public function run() {}


}