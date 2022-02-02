<?php


/**
 * 
 */
class Product extends ProductCore{

	public $extra_details;
	
	public function __construct($id_product = null, $full = false, $id_lang = null, $id_shop = null, Context $context = null){
		
		self::$definition['fields']['extra_details'] = array(
			'type' => self::TYPE_STRING,
			'required' => false,
			'size' => 255
		);

		parent::__construct($id_product, $full, $id_lang, $id_shop,$context);


	}
}