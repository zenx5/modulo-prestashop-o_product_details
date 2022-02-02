<?php

	if(!defined('_PS_VERSION_')) {
		exit;
	}	

	
	if( ! defined('ACTIVE_MODULE_FD') ){
		include_once 'classModuleFD.php';
	}
	
	
	class o_product_details extends ModuleFD{

		public function __construct(){
			$this->name = 'o_product_details';
			$this->tab = 'front_office_features';
			$this->version = '1.0.0';
			$this->author = 'Octavio Martinez';
			$this->need_instance = 0;
			$this->ps_versions_compliancy = [
				'min' => '1.6',
				'max' => _PS_VERSION_
			];
			$this->bootstrap = true;
			parent::__construct();
			
			$this->displayName = 'Product Extra Details';
			$this->description = 'Algunos Detalles extras para los productos';
			$this->confirmUninstall = 'Are you sure you want to Uninstall?';

			if(!Configuration::get('MYMODULE_NAME')) {
				$this->warning = 'No name provided';
			}

			$this->hooks[] = array('displayAdminProductsMainStepRightColumnBottom','1');
			$this->hooks[] = array('displayFooterProduct','1');

		}
		

		/******************
		**** INSTALL ******
		*******************/

		public function install(){
			//$this->installTab();
			
			if(Shop::isFeatureActive()) {
				Shop::setContext(Shop::CONTEXT_ALL);
			}
			$this->changeTables();
			return parent::install() && $this->addHooks($this->hooks);
		}

		public function changeTables(){
			return true;
			$sql = "ALTER TABLE "._DB_PREFIX_."product ADD extra_details VARCHAR(255) NULL";
			return DB::getInstance()->execute($sql);
		}

		public function uninstall(){
			//$this->uninstallTab();
			return parent::uninstall() && $this->addHooks($this->hooks, false);
		}

		/****************
		**** HOOKS ******
		*****************/
		
		public function hookDisplayAdminProductsMainStepRightColumnBottom($params){
			$product = new Product($params['id_product']);

			$this->context->smarty->assign(array(
				'extra_details' => $product->extra_details
			));
			
			return $this->display(__FILE__,'views/templates/hook/back.tpl');
		}
		public function hookDisplayFooterProduct($params){
			$product = new Product($params['product']['id_product']);

			$this->context->smarty->assign(array(
				'extra_details' => $product->extra_details
			));
			
			return $this->display(__FILE__,'views/templates/hook/front.tpl');
		}

		/************************
		**** CONFIGURATION ******
		************************/

		public function getContent(){
			return;
			$output = null;
			if(Tools::isSubmit('submit')) {
				$output .= $this->displayConfirmation('Se actualizo la Configuracion del Modulo');
			}
			return $output.$this->displayForm();
		}

		public function displayForm(){
			return;
			$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
			
			$inputs = array(
				array(
					'type' => 'text',
					'label' => $this->l('APP ID'),
					'name' => 'appid',
					'desc' => $this->l('API ID for openweathermap')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Units'),
					'name' => 'units',
					'options' => array(
						'query' => array(
							array( 'id' => 'standard', 'name' => 'Standard'),
							array( 'id' => 'metric', 'name' => 'Metric'),
							array( 'id' => 'imperial', 'name' => 'Imperial'),
						),
						'id' => 'id',
						'name' => 'name'

					),
					'desc' => $this->l('Metric system')
				),

			);

			$fields_form = array(
				'form' => array(
		            'legend' => array(
						'title' => 'Titulo',
						'icon' => 'icon-cogs'
		            ),
		            'input' => $inputs, 
		            'submit' => array(
		                'name' => 'submit',
		                'title' => $this->trans('Save', array(), 'Admin.Actions')
		            ),
		        ),
        	);

        	$helper = new HelperForm();
	        $helper->module = $this;
	        $helper->table = $this->name;
	        $helper->token = Tools::getAdminTokenLite('AdminModules');
	        $helper->currentIndex = $this->getModuleConfigurationPageLink();
	        
	        $helper->default_form_language = $lang->id;
	        
	        $helper->title = $this->displayName;
	        $helper->show_toolbar = false;
	        $helper->toolbar_scroll = false;
	        
	        $helper->submit_action = 'submit';
	        

			$helper->identifier = $this->identifier;


	        $helper->tpl_vars = array(
	            'languages' => $this->context->controller->getLanguages(),
	            'id_language' => $this->context->language->id,    
	            'fields_value' => array( 
	            	'appid' => Configuration::get('appid',''),
	            	'units' => Configuration::get('units','standard'),
	            )
	        );

	        return $helper->generateForm(array($fields_form));
		}


		/**************
		**** TABS ******
		***************/
		private function installTab(){
			return true;
			/*
			$response = true;

			$subTab = new Tab();
			$subTab->active = 1;
			$subTab->name = array();
			$subTab->class_name = 'OscLinkTab';
			$subTab->icon = 'menu';
			foreach (Language::getLanguages() as $lang) {
				$subTab->name[$lang['id_lang']] = 'Subcategories Cards';
			}

			$subTab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog');
			$subTab->module = $this->name;
			$response &= $subTab->add();

			return $response;*/
		}

		private function uninstallTab(){
			return true;
			/*$response = true;
			$tab_id = (int)Tab::getIdFromClassName('OscLinkTab');
			if(!$tab_id){
				return true;
			}

			$tab = new Tab($tab_id);
			$response &= $tab->delete();
			return $response;*/
		}
	}
		