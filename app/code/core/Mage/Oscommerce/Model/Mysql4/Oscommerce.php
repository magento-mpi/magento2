<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Oscommerce
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Model_Mysql4_Oscommerce extends Mage_Core_Model_Mysql4_Abstract
{
    const DEFAULT_DISPLAY_MODE     = 'PRODUCTS';
    const DEFAULT_IS_ANCHOR		   = '0';
    const DEFAULT_STORE		       = 'Default';
    const DEFAULT_PRODUCT_TYPE     = 'Simple';
    const DEFAULT_ATTRIBUTE_SET    = 'Default';
    const DEFAULT_VISIBILITY	   = 'Catalog, Search';
    const DEFAULT_LOCALE           = 'en_US';
    const DEFAULT_MAGENTO_CHARSET  = 'UTF-8';
    const DEFAULT_OSC_CHARSET      = 'ISO-8859-1';
    const DEFAULT_FIELD_CHARSET	   = 'utf8';

    protected $_currentWebsiteId;
    protected $_currentWebsite;

    protected $_importType 	            = array();
    protected $_countryIdToCode         = array();
    protected $_countryNameToCode       = array();
    protected $_regionCode              = array();
    protected $_logData                 = array();
    protected $_languagesToStores       = array();
    protected $_prefix                  = '';
    protected $_storeLocales            = array();
    protected $_rootCategory            = '';
    protected $_website                 = '';
    protected $_tableOrders             = '';

    protected $_websiteCode             = '';
    protected $_isProductWithCategories = false;
    protected $_setupConnection ;
    protected $_customerIdPair          = array();
    protected $_categoryIdPair			= array();    
    protected $_prefixPath               = '';
    protected $_stores                  = array();
    protected $_productsToCategories    = array();
    protected $_productsToStores        = array();
    protected $_tableCharset            = array();
    protected $_connectionCharset;
    
    protected $_maxRows;
    protected $_oscStores;
    protected $_oscDefaultLanguage;
    protected $_oscStoreInformation;

    protected $_categoryModel;
    protected $_customerModel;
    protected $_productModel;
    protected $_productAdapterModel;
    protected $_orderModel;
    protected $_addressModel;
    protected $_websiteModel;
    protected $_storeGroupModel;
    protected $_configModel;
    protected $_customerGroupModel;
    protected $_storeModel;
    protected $_importCollection;
    protected $_saveRows                = 0;
    protected $_errors                  = array();
    protected $_importModel;
    protected $_lengthShortDescription;
    protected $_currentUserId;
    
    protected $_oscTables				= array(
    	'products', 'customers', 'categories', 'orders', 
    	'languages','orders_products', 'orders_status_history', 
    	'orders_total', 'products_description', 'address_book', 'categories_description'
    );

	protected $_encodingList = "ISO-8859-1, ISO-8859-2, UTF-8";

    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce', 'import_id');
        $this->_setupConnection = Mage::getSingleton('core/resource')->getConnection('oscommerce_setup');            
        $this->_currentWebsite = Mage::app()->getWebsite();
        $this->_currentWebsiteId = $this->_currentWebsite->getId();
        $this->_maxRows = Mage::getStoreConfig('oscommerce/import/max_rows');
        $this->_lengthShortDescription = Mage::getStoreConfig('oscommerce/import/short_description_length');
        $this->_logData['created_at'] = $this->formatDate(time());
    }

    /**
     * Get paypal session namespace
     *
     * @return Mage_Paypal_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('oscommerce/session');
    }
        
    /**
     * Get characterset of osCommerce table
     *
     * @param string $tableName
     * @return string
     */
    public function getTableCharset($tableName = '')
    {
        $oscTables = array('products', 'customers', 'categories', 'orders', 'languages','orders_products', 'orders_status_history', 'orders_total');
        if (!$this->_tableCharset) {
	       	foreach($oscTables as $oscTable) {
	            $oscTableName = $this->getOscTable($oscTable);
	            if ($results = $this->_getForeignAdapter()->fetchAll("show create table `{$oscTableName}`")) {
	                foreach ($results as $result) {
	                    if($result['Create Table']) {
	                        $lines  = explode("\n",$result['Create Table']);
	                        $i = 0;
	                        foreach ($lines as $line) {
	                            if ($i == sizeof($lines) - 1) {
	                                if (preg_match_all('/CHARSET=(\w+)/', $line, $matches)) {
	                                    if (isset($matches[1][0]))
	                                    {
	                                        $this->_tableCharset[$oscTableName] = $matches[1][0];
	                                    }
	                                }
	                            }
	                            $i++;
	                        }
	                    }
	
	                }
	                if (!isset($this->_tableCharset[$oscTableName])) {
	                    $this->_tableCharset[$oscTableName] = self::DEFAULT_OSC_CHARSET;
	                }
	            }
	        }
        }
        if (isset($tableName) && isset($this->_tableCharset[$tableName])) {
            return $this->_tableCharset[$tableName];
        }
        return false;
    }

    public function getConnectionCharset()
    {
    	 if (!$this->_connectionCharset) {
    	 	$result = $this->_getForeignAdapter()->fetchRow("SHOW VARIABLES LIKE 'character_set_connection'");
    	 	$this->_connectionCharset = $result['Value'];
    	 }
    	 return $this->_connectionCharset;
    }
    
    public function resetConnectionCharset()
    {
    	$charset = $this->getConnectionCharset();
    	$this->_getForeignAdapter()->query("SET NAMES '{$charset}'");
    }
    
    public function getCharsetCollection()
    {
    	$this->getCharset();
    	return $this->_tableCharset ? $this->_tableCharset: false;	
    }
    
    public function setCharset($charset) 
    {
    	if (is_array($charset)) {
    		$this->_tableCharset = $charset;
    	}
    }
    
    public function getCharset($table = '') {
    	if (!$this->_tableCharset) {
	    	foreach($this->_oscTables as $oscTable) {
				$tableName = $this->getOscTable($oscTable);
	    		foreach ($result = $this->_getForeignAdapter()->fetchAll("SHOW FULL COLUMNS FROM {$tableName}") as $field) {
	    			$this->_tableCharset[$tableName][$field['Field']] = $field['Collation'] ? strtolower(preg_replace('/(\_\w+)$/','', $field['Collation'])):'';
	    		}
	    		
	    		if ($oscTable == 'orders') {
	    			$this->_tableCharset[$tableName]['orders_status_name'] = $this->_tableCharset[$tableName]['customers_name'];
	    		}
	    	}
    	}
    	if (!empty($table) && isset($this->_tableCharset[$table])) {
    		return $this->_tableCharset[$table];
    	}
    	return $this->_tableCharset;
    }
    
    /**
     * Get website object
     *
     * @return Mage_Core_Model_Website
     */
    public function getCurrentWebsite()
    {
        return $this->_currentWebsite;    
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->formatDate(time()));
        } 
        $object->setUpdatedAt($this->formatDate(time()));
        parent::_beforeSave($object);
    }

    protected function _getForeignAdapter()
    {
        return $this->_getConnection('foreign');
    }
    
    /**
     * Get store code by id
     *
     * @param integer $id
     * @return string
     */
    public function getStoreCodeById($id)
    {
        if (!$this->_stores)  {
            $stores = Mage::app()->getStores();
            foreach($stores as $store) {
                $this->_stores[$store->getId()] = $store->getCode();
            }
        }
        if (isset($this->_stores[$id])) {
            return $this->_stores[$id];
        }
        return false;
    }

    public function setWebsiteCode($code)
    {
        if (isset($code)) $this->_websiteCode = $code;
    }

    /**
     * Create new website or set current website as default website
     *
     * @param  Mage_Oscommerce_Model_Oscommerce $obj
     * @param integer $websiteId
     */
    public function createWebsite($websiteId = null)
    {
    	$importModel = $this->getImportModel();
    	$this->resetConnectionCharset();
        $websiteModel  = $this->getWebsiteModel();
        if (!is_null($websiteId)) {
            $websiteModel->load($websiteId);
        }

        if (!$websiteModel->getId()) {
            $storeInfo = $this->getOscStoreInformation();
            if ($this->_websiteCode && !($websiteModel->load($this->_websiteCode)->getId())) {
                $websiteModel->setName($storeInfo['STORE_NAME']);
                $websiteModel->setCode($this->_websiteCode ? $this->_websiteCode : $this->_format($storeInfo['STORE_NAME']));
                $websiteModel->save();
            }
        }


        if ($websiteModel->getId()) {
            $this->_logData['user_id'] = $this->_getCurrentUserId();
            $this->_logData['import_id']  = $importModel->getId();
            $this->_logData['type_id']      = $this->getImportTypeIdByCode('website');
            $this->_logData['ref_id']         = $websiteModel->getId();
            $this->_logData['created_at']  = $this->formatDate(time());
            $this->log($this->_logData);
        }

        /**
         * Create Root category
         */
        $this->createRootCategory();

        /**
         * Create default store group
         */
        $this->createStoreGroup();
    }

    protected function _getCollection($code)
    {
        if ($this->_importCollection) {
            if (isset($this->_importCollection[$code])) {
                return $this->_importCollection[$code];
            }
        }
        return;
    }    
    
    public function createStoreGroup()
    {
    	$importModel = $this->getImportModel();
		$this->resetConnectionCharset();
		
        $storeInfo = $this->getOscStoreInformation();

        $websiteModel = $this->getWebsiteModel();

        if (!$websiteModel->getId()) {
            $websiteModel->load($this->_currentWebsiteId); // NEED TO GET DEFAULT WEBSITE ID FROM CONFIG
        }

        $storeGroupModel = $this->getStoreGroupModel();
        $storeGroupModel->unsetData();
        $storeGroupModel->setOrigData();

        $storeGroupName = Mage::helper('oscommerce')->__('%s Store', $websiteModel->getId() == $this->_currentWebsiteId ? $storeInfo['STORE_NAME'] : $websiteModel->getName());
        $storeGroupModel->setWebsiteId($websiteModel->getId());
        $storeGroupModel->setName($storeGroupName);
        $storeGroupModel->setRootCategoryId($this->getRootCategory()->getId());

        try {
            $storeGroupModel->save();

            $websiteModel->setDefaultGroupId($storeGroupModel->getId());
            $websiteModel->save();
        }
        catch (Exception $e) {
            // you catch
        }
        $this->_logData['user_id']      = Mage::getSingleton('admin/session')->getUser()->getId();
        $this->_logData['import_id']    = $importModel->getId();
        $this->_logData['type_id']      = $this->getImportTypeIdByCode('group');
        $this->_logData['ref_id']       = $storeGroupModel->getId();
        $this->_logData['created_at']   = $this->formatDate(time());

        $this->log($this->_logData);

        return $this;
    }

    public function createRootCategory()
    {
    	$importModel = $this->getImportModel();
		$this->resetConnectionCharset();
        $categoryModel = $this->getCategoryModel();
        $categoryModel->unsetData();
        $categoryModel->setOrigData();

        $websiteModel = $this->getWebsiteModel();
        if (!$websiteModel->getId()) {
            $websiteModel->load($this->_currentWebsiteId); // NEED TO GET DEFAULT WEBSITE ID FROM CONFIG
        }

        $storeInfo = $this->getOscStoreInformation();

        $categoryName = Mage::helper('oscommerce')->__('Root category for %s', $websiteModel->getName());

        $categoryModel->setStoreId(0);
        $categoryModel->setIsActive(1);
        $categoryModel->setDisplayMode(self::DEFAULT_DISPLAY_MODE);
        $categoryModel->setName($categoryName);
        //$categoryModel->setDescription($categoryName);
        $categoryModel->setParentId(1);
        $categoryModel->setPath('1');

        try {
            $categoryModel->save();
            $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
            $this->_logData['import_id']  = $importModel->getId();
            $this->_logData['type_id']      = $this->getImportTypeIdByCode('root_category');
            $this->_logData['ref_id']         = $categoryModel->getId();
            $this->_logData['created_at']  = $this->formatDate(time());
            $this->log($this->_logData);            
        }
        catch (Exception $e) {
            // you catch
        }

        $this->setRootCategory(clone $categoryModel);

        return $this;
    }

    /**
     * Importing store data from osCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     */
    public function importStores()
    {
		$this->resetConnectionCharset();    	
    	$importModel = $this->getImportModel();
    	$charsetLang = $this->getCharset('languages');
//    	$charset = $this->getConnectionCharset();
    	
//    	$groupmodel = mage::getmodel('core/store_group')->load(self::DEFAULT_WEBSITE_GROUP);
        $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
        $this->_logData['import_id'] = $importModel->getId();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('store');
        $locales = $this->getStoreLocales();
        $defaultStore = '';
        $storeInformation = $this->getOscStoreInformation();
        $defaultStoreCode = $storeInformation['DEFAULT_LANGUAGE'];
        $configModel = $this->getConfigModel();
        $storeModel = $this->getStoreModel();
        $storeGroupModel = $this->getStoreGroupModel();
        $storeGroupId = $storeGroupModel->getId();
        $websiteModel = $this->getWebsiteModel();
        $websiteId = $websiteModel->getId();
        if ($stores = $this->getOscStores()) foreach($stores as $store) {
            try {
                $this->_logData['value'] = $store['id'];
                unset($store['id']);
                $store['group_id'] = $storeGroupId;
                $store['website_id'] = $websiteId;


                $storeModel->unsetData();
                $storeModel->setOrigData();
                $storeModel->load($store['code']);
                if ($storeModel->getId() && $storeModel->getCode() == $store['code']) {
                    $localeCode = $locales[$store['code']];
                    unset($locales[$store['code']]);
                    $store['code'] = $store['code'].'_'.$websiteId.time(); // for unique store code
                    $locales[$store['code']] = $localeCode;
                }
                
				if (!empty($charsetLang['name'])
					&& $charsetLang['name'] != self::DEFAULT_FIELD_CHARSET) {
					$store['name'] = @iconv($charsetLang['name'], self::DEFAULT_FIELD_CHARSET, $store['name']);		
				}

//				$store['name'] = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $store['name']);
				
                $storeModel->unsetData();
                $storeModel->setOrigData();
                $storeModel->setData($store);
                $storeModel->save();
                $this->_logData['ref_id'] = $storeModel->getId();
                $this->_logData['created_at'] = $this->formatDate(time());
                $this->log($this->_logData);
                $storeLocale = isset($locales[$storeModel->getCode()])?$locales[$storeModel->getCode()]: $locales['default'];

                $configModel->unsetData();
                $configModel->setOrigData();
                $configModel->setScope('stores')
                    ->setScopeId($storeModel->getId())
                    ->setPath('general/locale/code')
                    ->setValue($storeLocale)
                    ->save();
                if ($store['scode'] == $defaultStoreCode) {
                    $defaultStore = $storeModel->getId();
                }
                Mage::dispatchEvent('store_add', array('store'=>$storeModel));
            } catch (Exception $e) {
                //echo $e->getMessage();
            }
        }

        $this->setStoreLocales($locales);

        if ($defaultStore) {
            $storeGroupModel->setDefaultStoreId($defaultStore);
            $storeGroupModel->save();
        }
        Mage::app()->reinitStores();
        unset($stores);
    }

    /**
      * Importing customer/address from osCommerce to Magento
      *
      *  @param Mage_Oscommerce_Model_Oscommerce $obj
      */
    public function importCustomers($startFrom = 0, $useStartFrom = false)
    {
        $this->_resetSaveRows();
        $this->_resetErrors();
        $totalCustomers = $this->getTotalCustomers();
        $maxRows = $this->getMaxRows();
        $pages = floor($totalCustomers / $maxRows) + 1;

        if (!$useStartFrom) {
            for ($i = 0; $i < $pages; $i++) {
                if ($customers = $this->getCustomers(array('from'=>($i * $maxRows),'max'=>$maxRows))) {
                    foreach ($customers as $customer) {
                        $this->_saveCustomer($customer);
                    }
                }
            }            
        } else {
            if ($customers = $this->getCustomers(array('from'=> $startFrom ,'max'=>$maxRows))) {
                foreach ($customers as $customer) {
                    $this->_saveCustomer($customer);
                }
            }            
        }
    }

    /**
     * Save customer data
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     * @param array $data
     */
    protected function _saveCustomer($data = null) {
    	$addressFieldMapping = array(
	    	'street' => 'entry_street_address',
	    	'firstname' => 'entry_firstname',
	    	'lastname'	=> 'entry_lastname',
	    	'city'		=> 'entry_city',
	    	'region'	=> 'entry_state'
	    );
	    $importModel = $this->getImportModel();
		$timezone = $importModel->getTimezone();
//		$charset = $this->getConnectionCharset();
        if (!is_null($data)) {
        	$charsetCustomer = $this->getCharset('customers');
			$charsetAddress  = $this->getCharset('address_book');
            $customerAddresses = array();
            // Getting customer group data
            $customerGroupId = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
            $customerGroupModel = $this->getCustomerGroupModel()->load($customerGroupId);        
            $websiteId = $this->getWebsiteModel()->getId();
            $customerModel = $this->getCustomerModel();
            $addressModel = $this->getAddressModel();
            
            $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
            $this->_logData['type_id'] = $this->getImportTypeIdByCode('customer');
            $this->_logData['import_id'] = $importModel->getId();        
            $this->_logData['value'] = $oscCustomerId = $data['id'];
            
            $data['group_id'] = $customerGroupModel->getName();
            
	        $prepareCreated = explode(' ', $data['created_at']);
	       	$dateFormat = 'YYYY-MM-dd HH:mm:ss';
	        $dateCreated = new Zend_Date();	        
			$dateCreated->setTimezone($timezone);
	        $dateCreated->setDate($prepareCreated[0], 'YYYY-MM-dd');
	        $dateCreated->setTime($prepareCreated[1], 'HH:mm:ss');
	       	$dateCreated->setTimezone('GMT');
	        $data['created_at'] =  $dateCreated->toString($dateFormat);               

            foreach($data as $field => $value) {
            	if (in_array($field, array('firstname', 'lastname')) 
            		&& !empty($charsetCustomer['customers_'.$field])
            		&& $charsetCustomer['customers_'.$field] != self::DEFAULT_FIELD_CHARSET) {
            		$value = @iconv($charsetCustomer['customers_'.$field], self::DEFAULT_FIELD_CHARSET, $value);
            	}
//            	if (in_array($field, array('firstname', 'lastname'))) {
//            		$value = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $value);
//            	}
            	$data[$field] = html_entity_decode($value, ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET);
            	
            }

            
            // Getting addresses
            $addresses = $this->getAddresses($data['id']);        
            if ($addresses) {
                foreach ($addresses as $address) {
                    foreach ($address as $field => $value) {
                    	
                        if ($field == 'street1') {
                            $field = 'street';
                        }
                        if ($field == 'country_id') {
                            $value = $this->getCountryCodeById($value);
                            $field = 'country';
                        }
                        if ($field == 'region_id'
                        && in_array($address['country_id'], array(38, 223))) {
                            $field = 'region';
                        }
                        
                        if (in_array($field, array_keys($addressFieldMapping))
                        	&& !empty($charsetAddress[$addressFieldMapping[$field]])
                        	&& $charsetAddress[$addressFieldMapping[$field]] != self::DEFAULT_FIELD_CHARSET
   							) {
                        	$value = @iconv($charsetAddress[$addressFieldMapping[$field]], self::DEFAULT_FIELD_CHARSET, $value);
                        }
                        
                        if (!in_array($field, array('customers_id'))) {
                            $address[$field] = $value;
                        } else {
                            unset($address[$field]);
                        }
                    }
                    $address['country_id'] = $address['country'];
                    unset($address['country']);
                    $customerAddresses[] = $address;
                }
            }
            $defaultBilling = '';
            $defaultBilling = $data['default_billing'];
            unset($data['default_billing']);
            unset($data['id']);
            
            try {
                $customerModel->setData($data);
                $customerModel->setWebsiteId($websiteId > 0 ? $websiteId: $this->getCurrentWebsite()->getId());
                $customerModel->save();
                $customerId = $customerModel->getId();
    
                if ($customerAddresses) foreach ($customerAddresses as $customerAddress) {
                    $customerAddress['telephone'] = $data['telephone'];
                    $customerAddress['fax'] = $data['fax'];
                    $addressModel->unsetData();
                    $addressModel->setData($customerAddress);
                    $addressModel->setCustomerId($customerId);
                    $addressModel->setId(null);
                    $addressModel->save();
                    if ($defaultBilling == $customerAddress['id']) {
                        $addressId = $addressModel->getId();
                        $customerModel->setDefaultBilling($addressId);
                        $customerModel->setDefaultShipping($addressId);
                    }
                }
                $customerModel->save();
                $this->_customerIdPair[$oscCustomerId] = $customerId;
                $this->_logData['ref_id'] = $customerId;
                $this->_logData['created_at'] = $this->formatDate(time());
                $this->log($this->_logData);
                $this->_saveRows++;	
            } catch (Exception $e) {
                $this->_addErrors(Mage::helper('oscommerce')->__('Email %s cannot be saved because of %s', $data['email'], $e->getMessage()));
            }
        }
    }
    
    public function getCustomerIdPair()
    {
        if (!$this->_customerIdPair) {
    		$select  = "SELECT `value`, `ref_id` FROM `{$this->getTable('oscommerce_ref')}` WHERE `import_id`={$this->getImportModel()->getId()} ";
    		$select .= " AND `type_id`={$this->getImportTypeIdByCode('customer')}";
    		if ($results = $this->_getReadAdapter()->fetchPairs($select)) {
    			$this->_customerIdPair = $results;
    		}
        }
        return $this->_customerIdPair;
    }

    public function setCustomerIdPair($data)
    {
        if (is_array($data)) {
            $this->_customerIdPair = $data;
        }
    }

    public function importCategories($startFrom = 0, $useStartFrom = false)
    {
    	$importModel = $this->getImportModel();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('category');
        $this->_logData['import_id'] = $importModel->getId();
        $categoryModel = $this->getCategoryModel();    		
        
        $this->_resetSaveRows();
        $this->_resetErrors();
        $maxRows = $this->getMaxRows();        
        $totalCategories = $this->getCategoriesCount();

        $pages = floor($totalCategories / $maxRows) + 1;
        if (!$useStartFrom) {
            for ($i = 0; $i < $pages; $i++) {
                if ($categories = $this->getCategories(array('from'=> $i * $maxRows,'max'=>$maxRows))) {
                    foreach ($categories as $category) {
                        $this->_saveCategory($category);
                    }
                }
            }
        } else {
            if ($categories = $this->getCategories(array('from'=> $startFrom ,'max'=>$maxRows))) {
                foreach ($categories as $category) {
                    $this->_saveCategory($category);
                }
            }
        }
    }

    protected function _saveCategory($data) {
    	$importModel = $this->getImportModel();
    	$charsetCategory = $this->getCharset('categories');
    	$charsetDescription = $this->getCharset('categories_description');
//		$charset = $this->getConnectionCharset(); 
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('category');
        $this->_logData['import_id'] = $importModel->getId();
        $categoryModel = $this->getCategoryModel();
        $this->_logData['value'] = $data['id'];
       	unset($data['id']);
        try {
        	$data['store_id'] = 0;
        	$data['is_active'] = 1;
        	$data['display_mode'] = self::DEFAULT_DISPLAY_MODE;
        	$data['is_anchor']	= self::DEFAULT_IS_ANCHOR;
        	$data['attribute_set_id'] = $categoryModel->getDefaultAttributeSetId();

			if (!empty($charsetDescription['categories_name'])
				&& $charsetDescription['categories_name'] != self::DEFAULT_FIELD_CHARSET)
			{
				$data['name'] = @iconv($charsetDescription['categories_name'], self::DEFAULT_FIELD_CHARSET, $data['name']);
			}

//			$data['name'] = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $data['name']);
			
        	$data['name'] = $data['meta_title'] = html_entity_decode($data['name'], ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET);
        	$categoryModel->setData($data);
        	$categoryModel->save();
        	$categoryId = $categoryModel->getId();
        	$this->_logData['ref_id'] = $categoryId;
        	$this->_logData['created_at'] = $this->formatDate(time());
        	$this->log($this->_logData);
        	// saving data for different (encoding has been done in getCategoryToStores method)
        	$storeData = $data['stores'];
        	unset($data['stores']);
        	if (isset($storeData)) {
        		foreach($storeData as $storeId=>$catData) {
        			$catData['name'] = $catData['name'];
        			$categoryModel->setStoreId($storeId)->setName($catData['name'])->setMetaTitle($catData['name'])
        			->save();
        		}
        	}
        	$this->_saveRows++;
        } catch (Exception $e) {
        	$this->_addErrors(Mage::helper('oscommerce')->__('Category %s cannot be saved because of %s', $data['name'], $e->getMessage()));
        }
    }
    

    
    public function buildCategoryPath()
    {
    	$categoryIdPair = $this->getCategoryIdPair();
    	$importModel = $this->getImportModel();
    	if ($categoryIdPair) foreach ($categoryIdPair as $oscommerceId => $magentoId) {
    		$path = $this->getRootCategory()->getPath().'/'.join('/',$this->getCategoryPath($oscommerceId));
			$this->_getWriteAdapter()->raw_query("UPDATE `{$this->getTable('catalog_category')}` SET `path`='{$path}' WHERE `entity_id`={$magentoId}"); 
    	}
    }
    
    public function getCategoryPath($categoryId) 
    {
    	$categoryIdPair = $this->getCategoryIdPair();
        $select = "SELECT `c`.`parent_id` FROM `{$this->getOscTable('categories')}` c ";
        $select .= " WHERE `c`.`categories_id`={$categoryId}";
        if ($parentId = $this->_getForeignAdapter()->fetchOne($select)) {
        	if ($result = $this->getCategoryPath($parentId)) {
        		if (!isset($results)) {
        			$results = $result;
        		} else {
        			array_merge($results, $result);	
        		}
        	} else {
        		$results[] = $categoryIdPair[$parentId];	
        	}
        } 
       	$results[] = $categoryIdPair[$categoryId];
        return $results;
    }
    
    public function getCategoryIdPair()
    {
    	if (!$this->_categoryIdPair) {
    		$select  = "SELECT `value`, `ref_id` FROM `{$this->getTable('oscommerce_ref')}` WHERE `import_id`={$this->getImportModel()->getId()} ";
    		$select .= " AND `type_id`={$this->getImportTypeIdByCode('category')}";
    		if ($results = $this->_getReadAdapter()->fetchPairs($select)) {
    			$this->_categoryIdPair = $results;
    		}
    	}
    	return $this->_categoryIdPair;
    }
        
    public function setCategoryIdPair($data)
    {
    	if (is_array($data)) {
    		$this->_categoryIdPair = $data;
    	}
    }
    
    /**
     * Import products
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     */
    public function importProducts($startFrom = 0, $useStartFrom = false)
    {
    	$importModel = $this->getImportModel();
        $this->_logData['import_id'] = $importModel->getId();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('product');
        $productAdapterModel = Mage::getModel('catalog/convert_adapter_product');
        $productModel = $this->getProductModel();

        $this->_resetSaveRows();
        $this->_resetErrors();
        $maxRows = $this->getMaxRows();        
        $totalProducts = $this->getProductsCount();

        $pages = floor($totalProducts / $maxRows) + 1;
        if (!$useStartFrom) {
            for ($i = 0; $i < $pages; $i++) {
                if ($products = $this->getProducts(array('from'=> $i * $maxRows,'max'=>$maxRows))) {
                    foreach ($products as $product) {
                        $this->_saveProduct($product);
                    }
                }
            }
        } else {
            if ($products = $this->getProducts(array('from'=> $startFrom ,'max'=>$maxRows))) {
                foreach ($products as $product) {
                    $this->_saveProduct($product);
                }
            }
        }
    }
    
    public function getSaveRows()
    {
        return $this->_saveRows;
    }

    protected function _addErrors($error)
    {
        if (isset($error)) $this->_errors[] = $error;
    }
    
    public function getErrors()
    {
        if (sizeof($this->_errors) > 0) {
            return $this->_errors;
        }
    }

    protected function _resetErrors()
    {
        $this->_errors = array();
    }
    
    protected function _resetSaveRows()
    {
        return $this->_saveRows = 0;
    }

    /**
     * Save products data
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     * @param array $data
     */
    protected function _saveProduct($data) {
    	$importModel = $this->getImportModel();
        $charsetProduct = $this->getCharset('products');
        $charsetDescription = $this->getCharset('products_description');
//        $charset = $this->getConnectionCharset();
    	$productAdapterModel = $this->getProductAdapterModel();
        $productModel = $this->getProductModel();
        $mageStores = $this->getLanguagesToStores();
        $this->_logData['value'] = $oscProductId = $data['id'];
        unset($data['id']);
        if ($this->_isProductWithCategories) {
            if ($categories = $this->getProductCategories($oscProductId))
            $data['category_ids'] = $categories;
        }
        

        /**
         * Checking product by using sku and website
         */
        $productModel->unsetData();
        $productId = $productModel->getIdBySku($data['sku']);
        $productModel->load($productId);
        if ($productModel->getId()) {
        	$websiteIds = $productModel->getWebsiteIds();
        	
        	if ($websiteIds) foreach($websiteIds as $websiteId) {
        		if ($websiteId == $this->getWebsiteModel()->getId()) {
	        		$this->_addErrors(Mage::helper('oscommerce')->__('SKU %s was not imported since it already exists in %s', 
	        			$data['sku'], 
	        			$this->getWebsiteModel()->getName()));
	        		return ;
        		}
        	}
        } 
        //$data['short_description'] = $this->_formatStringTruncate($data['description'], $this->_lengthShortDescription) . ' ...';
        try {
            if (isset($data['image'])) {
                if (substr($data['image'], 0,1) != DS) {
                    $data['image'] = DS . $data['image'];
                }

                if (!file_exists(Mage::getBaseDir('media'). DS . 'import' . $data['image'])) {
                    unset($data['image']);
                } else {
                    $data['thumbnail'] = $data['small_image'] = $data['image'];
                }
            }
            if ($stores = $this->getProductStores($oscProductId)) {
            	
                foreach ($stores as $storeId => $store) {
                    if (!$storeCode = $this->getStoreCodeById($mageStores[$storeId])) {
                        $storeCode = $this->getCurrentWebsite()->getDefaultStore()->getCode();
                    }
                    $data['store'] = $storeCode;
                    if (!empty($charsetProduct['products_name']) && $charsetProduct['products_name'] != self::DEFAULT_FIELD_CHARSET) {
                    	$store['name'] = @iconv($charsetProduct['products_name'], self::DEFAULT_FIELD_CHARSET, $store['name']);
                    }
                    if (!empty($charsetDescription['products_description']) && $charsetDescription['products_description'] != self::DEFAULT_FIELD_CHARSET) {
                    	$store['description'] = @iconv($charsetProduct['products_description'], self::DEFAULT_FIELD_CHARSET, $store['description']);
                    }
//					$store['name'] = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $store['name']);
//                  $store['description'] = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $store['description']);
                    
                    $data['name'] = html_entity_decode($store['name'], ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET);
                    $data['description'] = html_entity_decode($store['description'], ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET);
//                    $data['short_description'] = $this->_formatStringTruncate($data['description'], $this->_lengthShortDescription) . ' ...';
                    $data['short_description'] = $data['description'];
                    $productAdapterModel->saveRow($data);
                }
            }

            $productId = $productAdapterModel->getProductModel()->getId();

            $this->_logData['ref_id'] = $productId;
            $this->_logData['created_at'] = $this->formatDate(time());
            $this->log($this->_logData);
            $this->_saveRows++;
        } catch (Exception $e) {
            $this->_addErrors(Mage::helper('oscommerce')->__('SKU %s cannot be saved because of %s', $data['sku'], $e->getMessage()));
        }        
    }
    
    public function importOrders($startFrom = 0, $useStartFrom = false)
    {
    	$importModel = $this->getImportModel();
        $this->_resetSaveRows();
        $this->_resetErrors();        
        // Get orders
        $connCharset = $this->getConnectionCharset();
        if ($connCharset) {
        	$this->_getWriteAdapter()->query("SET NAMES '{$connCharset}'");
        }
        $totalOrders = $this->getOrdersCount();
        $maxRows = $this->getMaxRows();
        $pages = floor($totalOrders / $maxRows) + 1;

        if (!$useStartFrom) {
            for ($i = 0; $i < $pages; $i++) {
                $orders = $this->getOrders(array('from' => $i * $maxRows, 'max' => $maxRows));
                if ($orders) foreach($orders as $order) {
                    $this->_saveOrder($order);
                }
            }
        } else {
            $orders = $this->getOrders(array('from' => $startFrom, 'max' => $maxRows));
            if ($orders) foreach($orders as $order) {
                $this->_saveOrder($order);
            }
        }
    }

    public function createOrderTables()
    {
    	$importModel = $this->getImportModel();
        $importId  = $importModel->getId();
        $websiteId = $this->getWebsiteModel()->getId();

        $tables = array(
            'orders' => "CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_order')}` (
              `osc_magento_id` int(11) NOT NULL auto_increment,
              `orders_id` int(11) NOT NULL,
              `customers_id` int(11) NOT NULL default '0',
              `magento_customers_id` int(11) NOT NULL default '0',
              `import_id` int(11) NOT NULL default '0',
              `website_id` int(11) NOT NULL default '0',
              `customers_name` varchar(64) NOT NULL default '',
              `customers_company` varchar(32) default NULL,
              `customers_street_address` varchar(64) NOT NULL default '',
              `customers_suburb` varchar(32) default NULL,
              `customers_city` varchar(32) NOT NULL default '',
              `customers_postcode` varchar(10) NOT NULL default '',
              `customers_state` varchar(32) default NULL,
              `customers_country` varchar(32) NOT NULL default '',
              `customers_telephone` varchar(32) NOT NULL default '',
              `customers_email_address` varchar(96) NOT NULL default '',
              `customers_address_format_id` int(5) NOT NULL default '0',
              `delivery_name` varchar(64) NOT NULL default '',
              `delivery_company` varchar(32) default NULL,
              `delivery_street_address` varchar(64) NOT NULL default '',
              `delivery_suburb` varchar(32) default NULL,
              `delivery_city` varchar(32) NOT NULL default '',
              `delivery_postcode` varchar(10) NOT NULL default '',
              `delivery_state` varchar(32) default NULL,
              `delivery_country` varchar(32) NOT NULL default '',
              `delivery_address_format_id` int(5) NOT NULL default '0',
              `billing_name` varchar(64) NOT NULL default '',
              `billing_company` varchar(32) default NULL,
              `billing_street_address` varchar(64) NOT NULL default '',
              `billing_suburb` varchar(32) default NULL,
              `billing_city` varchar(32) NOT NULL default '',
              `billing_postcode` varchar(10) NOT NULL default '',
              `billing_state` varchar(32) default NULL,
              `billing_country` varchar(32) NOT NULL default '',
              `billing_address_format_id` int(5) NOT NULL default '0',
              `payment_method` varchar(255) NOT NULL default '',
              `cc_type` varchar(20) default NULL,
              `cc_owner` varchar(64) default NULL,
              `cc_number` varchar(32) default NULL,
              `cc_expires` varchar(4) default NULL,
              `last_modified` datetime default NULL,
              `date_purchased` datetime default NULL,
              `orders_status` varchar(32) default NOT NULL,
              `orders_date_finished` datetime default NULL,
              `currency` char(3) default NULL,
              `currency_value` decimal(14,6) default NULL,
              `currency_symbol` char(3) default NULL,
              `orders_total`  decimal(14,6) default NULL,
              PRIMARY KEY  (`osc_magento_id`),
              KEY `idx_orders_customers_id` (`customers_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        "
            , 'orders_products' => "CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_order_products')}` (
              `orders_products_id` int(11) NOT NULL auto_increment,
              `osc_magento_id` int(11) NOT NULL default '0',
              `products_id` int(11) NOT NULL default '0',
              `products_model` varchar(12) default NULL,
              `products_name` varchar(64) NOT NULL default '',
              `products_price` decimal(15,4) NOT NULL default '0.0000',
              `final_price` decimal(15,4) NOT NULL default '0.0000',
              `products_tax` decimal(7,4) NOT NULL default '0.0000',
              `products_quantity` int(2) NOT NULL default '0',
              PRIMARY KEY  (`orders_products_id`),
              KEY `idx_orders_products_osc_magento_id` (`osc_magento_id`),
              KEY `idx_orders_products_products_id` (`products_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"

            , 'orders_total' => "CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_order_total')}` (
              `orders_total_id` int(10) unsigned NOT NULL auto_increment,
              `osc_magento_id` int(11) NOT NULL default '0',
              `title` varchar(255) NOT NULL default '',
              `text` varchar(255) NOT NULL default '',
              `value` decimal(15,4) NOT NULL default '0.0000',
              `class` varchar(32) NOT NULL default '',
              `sort_order` int(11) NOT NULL default '0',
              PRIMARY KEY  (`orders_total_id`),
              KEY `idx_orders_total_osc_magento_id` (`osc_magento_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"

            , 'orders_status_history'=>"CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce_order_history')}` (
              `orders_status_history_id` int(11) NOT NULL auto_increment,
              `osc_magento_id` int(11) NOT NULL default '0',
              `orders_status_id` int(5) NOT NULL default '0',
              `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
              `customer_notified` int(1) default '0',
              `comments` text,
              `orders_status` varchar(32) default NULL,
              PRIMARY KEY  (`orders_status_history_id`),
              KEY `idx_orders_status_history_osc_magento_id` (`osc_magento_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;"

            );

        $conn = $this->_setupConnection;
        foreach ($tables as $table => $schema) {
            $conn->beginTransaction();
            try {
                $conn->query($schema);
                $conn->commit();
            } catch (Exception $e) {
                //$conn->rollBack();
            }
        }

        $this->checkOrderField();        
    }
    
    public function setTablePrefix($prefix)
    {
        if (isset($prefix)) $this->_prefix = $prefix;
    }

    public function getTablePrefix()
    {
        return $this->_prefix;
    }

    public function setIsProductWithCategories($yn)
    {
        if (is_bool($yn)) {
            $this->_isProductWithCategories = $yn;
        }
    }

    /**
     * Logging imported data to oscommerce_ref table
     *
     * @param array data
     */
    public function log($data = array())
    {
        if (isset($data)) {
            $this->_getWriteAdapter()->beginTransaction();
            try {
                $this->_getWriteAdapter()->insert($this->getTable('oscommerce_ref'), $data);
                $this->_getWriteAdapter()->commit();
            } catch (Exception $e) {
                //$this->_getWriteAdapter()->rollBack();
            }
        }
    }

    public function getOscStoreInformation()
    {
        if (!$this->_oscStoreInformation) {
            $select =  "SELECT `configuration_key` `key`, `configuration_value` `value` FROM `{$this->getOscTable('configuration')}`";
            $select .= " WHERE `configuration_key` IN ('STORE_NAME', 'STORE_OWNER', 'STORE_OWNER_EMAIL', 'STORE_COUNTRY',' STORE_ZONE','DEFAULT_LANGUAGE')";
            if (!($result = $this->_getForeignAdapter()->fetchPairs($select))) {
                $result = array();
            }
            $this->_oscStoreInformation = $result;
        }
        return $this->_oscStoreInformation;
    }

    /**
     * Getting products data from osCommerce
     *
     */
    public function getProducts($limit = array())
    {
        $defaultLanguage = $this->getOscDefaultLanguage();
        $defaultLanguageId = $defaultLanguage['id'];        
        $code = $this->getWebsiteModel()->getCode();
        $website = $code? $code: $this->getCurrentWebsite()->getCode();
        $connection = $this->_getForeignAdapter();
        $select  = " SELECT `p`.`products_id` `id`, `p`.`products_quantity` `qty` ";
        $select .= " , `p`.`products_model` `sku`, `p`.`products_price` `price`";
        $select .= " , `p`.`products_image` `image` ";
        $select .= " , `p`.`products_weight` `weight`, IF(`p`.`products_status`,'Enabled','Disabled') `status` ";
        $select .= " , IF(`p`.`products_status`,'1','0') `is_in_stock`";
        $select .= " , `pd`.`products_name` `name`, `pd`.`products_description` `description` ";
        //$select .= " , `pd`.`products_description` `short_description` ";
        $select .= " , `tc`.`tax_class_title` `tax_class_id`, IF(1,'".self::DEFAULT_VISIBILITY."','') `visibility` ";
        $select .= " , `sp`.`specials_new_products_price` `special_price` ";
        $select .= " , `sp`.`specials_date_added` `special_from_date` ";
        $select .= " , `sp`.`expires_date` `special_to_date` ";
        $select .= " , IF(1,'".self::DEFAULT_ATTRIBUTE_SET."','') `attribute_set` ";
        $select .= " , IF(1,'".self::DEFAULT_PRODUCT_TYPE ."','') `type` ";
        //$select .= ", IF(1,'".self::DEFAULT_STORE."','') `store` ";
        $select .= " , IF(1,'".$website."','') `website` ";
        $select .= " FROM `{$this->getOscTable('products')}` p LEFT JOIN `{$this->getOscTable('products_description')}` pd ";
        $select .= " ON `pd`.`products_id`=`p`.`products_id` AND `pd`.`language_id`={$defaultLanguageId} ";
        $select .= " LEFT JOIN `{$this->getOscTable('tax_class')}` tc ON `tc`.`tax_class_id`=`p`.`products_tax_class_id` ";
        $select .= " LEFT JOIN `{$this->getOscTable('specials')}` sp ON `sp`.`products_id`=`p`.`products_id` ";
        if ($limit && isset($limit['from']) && isset($limit['max'])) {
            $select .= " LIMIT {$limit['from']}, {$limit['max']}";
        }
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }

    public function getProductsCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('products')}`");
    }

    public function getCategoriesCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('categories')}`");
    }
    
    public function getCustomersCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('customers')}`");
    }

    public function getOrdersCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('orders')}`");
    }
    
    public function getOrders($limit = array()) {
        $defaultLanguage = $this->getOscDefaultLanguage();
        $defaultLanguageId = $defaultLanguage['id'];       
        $select  = "SELECT `o`.`orders_id`, `o`.`customers_id`, `o`.`customers_name`";
        $select .= " ,`o`.`customers_company`,`o`.`customers_street_address`";
        $select .= " ,`o`.`customers_suburb`, `o`.`customers_city`";
        $select .= " ,`o`.`customers_postcode`, `o`.`customers_state`";
        $select .= " ,`o`.`customers_country`, `o`.`customers_telephone`";
        $select .= " ,`o`.`customers_email_address`, `o`.`customers_address_format_id`";
        $select .= " ,`o`.`delivery_name`, `o`.`delivery_company`";
        $select .= " ,`o`.`delivery_street_address`, `o`.`delivery_suburb`";
        $select .= " ,`o`.`delivery_city`, `o`.`delivery_postcode`, `o`.`delivery_state`";
        $select .= " ,`o`.`delivery_country`, `o`.`delivery_address_format_id`";
        $select .= " ,`o`.`billing_name`, `o`.`billing_company`";
        $select .= " ,`o`.`billing_street_address`, `o`.`billing_suburb`";
        $select .= " ,`o`.`billing_city`, `o`.`billing_postcode`, `o`.`billing_state`";
        $select .= " ,`o`.`billing_country`, `o`.`billing_address_format_id`";
        $select .= " ,`o`.`payment_method`, `o`.`cc_type`, `o`.`cc_owner`, `o`.`cc_number`";
        $select .= " ,`o`.`cc_expires`, `o`.`last_modified`, `o`.`date_purchased`";
        $select .= " ,`o`.`orders_status`, `o`.`orders_date_finished`, `o`.`currency`, `o`.`currency_value`";        
        $select .= " ,`c`.`symbol_left` `currency_symbol`,`ot`.`value` `orders_total`";
        $select .= " ,`os`.`orders_status_name`  FROM `{$this->getOscTable('orders')}` `o`";
        $select .= " LEFT JOIN `{$this->getOscTable('currencies')}` `c` ON `c`.`code`=`o`.`currency` ";
        $select .= " LEFT JOIN `{$this->getOscTable('orders_total')}` `ot` ON `ot`.`orders_id`=`o`.`orders_id` ";
        $select .= " AND `ot`.`class`='ot_total'";
        $select .= " LEFT JOIN `{$this->getOscTable('orders_status')}` os ON `os`.`orders_status_id`=`o`.`orders_status` ";
        $select .= " AND `os`.`language_id`={$defaultLanguageId} ";
        if (isset($limit) && isset($limit['from']) && isset($limit['max'])) {
            $select .= "  LIMIT {$limit['from']}, {$limit['max']} ";
        }
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }
    
    protected function _saveOrder($data)
    {
         $fieldNoEnc = array(
             'customers_id',
             'orders_id',
             'date_purchased',
             'last_modified',
             'orders_date_finished',
             'orders_products_id',
             'osc_magento_id',
             'products_id'
         );
    	
    	$importModel = $this->getImportModel();
    	$timezone = $importModel->getTimezone();
    	$charsetOrder = $this->getCharset('orders');
    	$charsetHistory = $this->getCharset('orders_status_history');
    	$charsetTotal = $this->getCharset('orders_total');
    	$charsetProduct = $this->getCharset('orders_products');
//    	$charset = $this->getConnectionCharset();
        $customerIdPair = $this->getCustomerIdPair();
        $importId  = $importModel->getId();
        $websiteId = $this->getWebsiteModel()->getId();
//        setlocale(LC_ALL, Mage::app()->getLocale()->getLocaleCode().'.UTF-8');
        if ($data['customers_id'] > 0 && isset($this->_customerIdPair[$data['customers_id']])) {
        	foreach($data as $field => $value) {
        		if (!in_array($field, $fieldNoEnc) && !empty($charsetOrder[$field])
        			&& $charsetOrder[$field] != self::DEFAULT_FIELD_CHARSET) {
        			$data[$field] = @iconv($charsetOrder[$field], self::DEFAULT_FIELD_CHARSET, $value);
        		}
//        		$data[$field] = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $value);
        	}

        	if ($data['date_purchased']) {
		        $preparePurchased = explode(' ', $data['date_purchased']);
		       	$dateFormat = 'YYYY-MM-dd HH:mm:ss';
		        $datePurchased = new Zend_Date();	        
				$datePurchased->setTimezone($timezone);
		        $datePurchased->setDate($preparePurchased[0], 'YYYY-MM-dd');
		        $datePurchased->setTime($preparePurchased[1], 'HH:mm:ss');
		       	$datePurchased->setTimezone('GMT');
		        $data['date_purchased'] =  $datePurchased->toString($dateFormat);        	
        	}
        	
	        if ($data['last_modified']) {
	        	$prepareModified = explode(' ', $data['last_modified']);
		        $dateModified = new Zend_Date();	        
				$dateModified->setTimezone($timezone);
		        $dateModified->setDate($prepareModified[0], 'YYYY-MM-dd');
		        $dateModified->setTime($prepareModified[1], 'HH:mm:ss');
		       	$dateModified->setTimezone('GMT');
		        $data['last_modified'] =  $dateModified->toString($dateFormat);        	        	
	        }
	        
	        if ($data['orders_date_finished']) {
	        	$prepareFinished = explode(' ', $data['orders_date_finished']);
		        $dateFinished = new Zend_Date();	        
				$dateFinished->setTimezone($timezone);
		        $dateFinished->setDate($prepareFinished[0], 'YYYY-MM-dd');
		        $dateFinished->setTime($prepareFinished[1], 'HH:mm:ss');
		       	$dateFinished->setTimezone('GMT');
		        $data['orders_date_finished'] =  $dateFinished->toString($dateFormat);        	        	
	        }	        

            $data['magento_customers_id'] = $this->_customerIdPair[$data['customers_id']]; // get Magento CustomerId
            $data['import_id'] = $importId;
            $data['website_id'] = $websiteId;
            $data['orders_status'] = $data['orders_status_name'];
            unset($data['orders_status_name']);
            $this->_getWriteAdapter()->insert($this->getTable('oscommerce_order'), $data);
            $oscMagentoId = $this->_getWriteAdapter()->lastInsertId();
            $this->_saveRows++;
            
            // Get orders products
            $select  = "SELECT `orders_products_id`, `orders_id`, `products_id` ";
            $select .= ", `products_model`, `products_name`, `products_price`, `final_price` ";
            $select .= ", `products_tax`, `products_quantity` ";
            $select .= " FROM `{$this->getOscTable('orders_products')}` WHERE `orders_id`={$data['orders_id']}";
            if ($orderProducts = $this->_getForeignAdapter()->fetchAll($select)) {
                foreach ($orderProducts as $orderProduct) {
                    unset($orderProduct['orders_id']);
                    unset($orderProduct['orders_products_id']);
                    $orderProduct['osc_magento_id'] = $oscMagentoId;
					foreach ($orderProduct as $field => $value) {		
		        		if (!in_array($field, $fieldNoEnc)) {
//		        			&& !empty($charsetProduct[$field])
//		        			&& $charsetProduct[$field] != self::DEFAULT_FIELD_CHARSET) {
		        			$orderProduct[$field] = @iconv($charsetProduct[$field], self::DEFAULT_FIELD_CHARSET, $value);
		        		}						
//	        			$orderProduct[$field] = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $value);
					}
                    $this->_getWriteAdapter()->insert($this->getTable('oscommerce_order_products'), $orderProduct);
                }
            }

            // Get orders totals
            $select  = "SELECT `orders_total_id`, `orders_id`, `title`, `text`, `value`, `class`, `sort_order` ";
            $select .= " FROM `{$this->getOscTable('orders_total')}` WHERE `orders_id`={$data['orders_id']} ORDER BY `sort_order`";
            
            if ($orderTotals = $this->_getForeignAdapter()->fetchAll($select)) {
                foreach ($orderTotals as $orderTotal) {
                	
                    unset($orderTotal['orders_id']);
                    unset($orderTotal['orders_total_id']);
                    $orderTotal['osc_magento_id'] = $oscMagentoId;
                    
//					foreach ($orderTotal as $field => $value) {
//		        		if (!empty($charsetTotal[$field])
//		        			&& $charsetTotal[$field] != self::DEFAULT_FIELD_CHARSET) {
//		        			$orderTotal[$field] = @iconv($charsetTotal[$field], self::DEFAULT_FIELD_CHARSET, $value);
//		        		}		
//		       			$orderTotal[$field] = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $value);
//					}

                    $orderTotal['title'] = @@iconv($charsetTotal['title'], self::DEFAULT_FIELD_CHARSET, $orderTotal['title']);
                    $orderTotal['text'] = @@iconv($charsetTotal['text'], self::DEFAULT_FIELD_CHARSET, $orderTotal['text']);
                    $this->_getWriteAdapter()->insert($this->getTable('oscommerce_order_total'), $orderTotal);					
                }
            }

	        $defaultLanguage = $this->getOscDefaultLanguage();
    	    $defaultLanguageId = $defaultLanguage['id'];
               
            // Get orders status history
            $select  = "SELECT `osh`.`orders_status_history_id`, `osh`.`orders_id`, `osh`.`orders_status_id` ";
            $select .= ", `os`.`orders_status_name` `orders_status`, `osh`.`date_added`, `osh`.`customer_notified`, `osh`.`comments` ";
            $select .= " FROM `{$this->getOscTable('orders_status_history')}` osh ";
            $select .= " LEFT JOIN `{$this->getOscTable('orders_status')}` os ON `os`.`orders_status_id`=`osh`.`orders_status_id` ";
            $select .= " AND `os`.`language_id`={$defaultLanguageId}";
            $select .= " WHERE `osh`.`orders_id`={$data['orders_id']}";
            if ($orderHistories = $this->_getForeignAdapter()->fetchAll($select)) {
                foreach ($orderHistories as $orderHistory) {
                    unset($orderHistory['orders_id']);
                    unset($orderHistory['orders_status_history_id']);
                    $orderHistory['osc_magento_id'] = $oscMagentoId;
			        $prepareAdded = explode(' ', $orderHistory['date_added']);
			       	$dateFormat = 'YYYY-MM-dd HH:mm:ss';
			        $dateAdded = new Zend_Date();	        
					$dateAdded->setTimezone($timezone);
			        $dateAdded->setDate($prepareAdded[0], 'YYYY-MM-dd');
			        $dateAdded->setTime($prepareAdded[1], 'HH:mm:ss');
			       	$dateAdded->setTimezone('GMT');
			        $orderHistory['date_added'] =  $dateAdded->toString($dateFormat);
//			        foreach($orderHistory as $field => $value) {
//		        		if (!empty($charsetHistory[$field])
//		        			&& $charsetHistory[$field] != self::DEFAULT_FIELD_CHARSET) {
//		        			$orderHistory[$field] = @iconv($charsetHistory[$field], self::DEFAULT_FIELD_CHARSET, $value);
//		        		}
//                    }

                    $orderHistory['orders_status'] = @@iconv($charsetHistory['orders_status'], self::DEFAULT_FIELD_CHARSET, $orderHistory['orders_status']);
                    $orderHistory['comments'] = @@iconv($charsetHistory['comments'], self::DEFAULT_FIELD_CHARSET, $orderHistory['comments']);
                    $orderHistory['customer_notified'] = @@iconv($charsetHistory['customer_notified'], self::DEFAULT_FIELD_CHARSET, $orderHistory['customer_notified']);
                    
                    $this->_getWriteAdapter()->insert($this->getTable('oscommerce_order_history'), $orderHistory);
                }
            }
        } else {
        	$this->_addErrors(Mage::helper('oscommerce')->__('Order #%s failed to import because the customer ID #%s associated with this order could not be found.', $data['orders_id'], $data['customers_id']));
        	//$this->_addErrors(Mage::helper('oscommerce')->__('Order of customer %s [%s] cannot be saved because of email does not have matching in customer entry.', $data['customers_name'], $data['customers_email_address']));
        }
    }
    
    /**
     * Getting product description for different stores
     *
     * @param integer $productId
     * @return mix array/boolean
     */
    public function getProductStores($productId) {
        if (!$this->_productsToStores) {
            $select =  "SELECT `products_id`, `language_id` `store`, `products_name` `name`, `products_description` `description`";
            $select .= " FROM `{$this->getOscTable('products_description')}` ";
            if ($results = $this->_getForeignAdapter()->fetchAll($select)) {
                foreach ($results as $result) {
                    $this->_productsToStores[$result['products_id']][$result['store']] = array('name'=>$result['name'], 'description' => $result['description']);
                }
            }
        }
        if (isset($this->_productsToStores[$productId])) {
            return $this->_productsToStores[$productId];
        }
        return false;
    }

    /**
     * Getting new created categories recursively using products of osCommerce
     *
     * @param integer $productId
     * @return string
     */
    public function getProductCategories($productId)
    {
    	$importModel = $this->getImportModel();
        if (!$this->_productsToCategories) {
            $select = "SELECT `products_id`, `categories_id` FROM `{$this->getOscTable('products_to_categories')}`";

            if ($results = $this->_getForeignAdapter()->fetchAll($select)) {
                $categories = array();
                foreach ($results as $result) {
                    $categories[$result['products_id']] = $result['categories_id'];
                    if (isset($categories[$result['products_id']])) {
                        $categories[$result['products_id']] .= ','.$result['categories_id'];
                    } else {
                        $categories[$result['products_id']] = $result['categories_id'];
                    }
                }
                //$categories = join(',', array_values($results));

                //$this->_getReadAdapter();
                $importId = $importModel->getId();
                $typeId = $this->getImportTypeIdByCode('category');


                if ($categories) foreach ($categories  as  $product => $category) {
                    $select = $this->_getReadAdapter()->select();
                    $select->from(array('osc'=>$this->getTable('oscommerce_ref')), array('id'=>'id','ref_id'=>'ref_id'));
                    $select->where("`osc`.`import_id`='{$importId}' AND `osc`.`type_id`='{$typeId}' AND `osc`.`value` in (".$category.")");
                    $resultCategories = $this->_getReadAdapter()->fetchPairs($select);
                    if ($resultCategories) {
                       $this->_productsToCategories[$product] = join(',',array_values($resultCategories));
                    }
                }
            }
        }
        if (isset($this->_productsToCategories[$productId])) {
            return $this->_productsToCategories[$productId];
        }
        return false;
    }

    public function getCategories($limit = array()) {
    	$importModel = $this->getImportModel();
    	$charsetCategory = $this->getCharset('categories');
    	$charsetDescription = $this->getCharset('categories_description');
//    	$charset = $this->getConnectionCharset();
        $defaultLanguage = $this->getOscDefaultLanguage();
        $defaultLanguageId = $defaultLanguage['id'];
        $select = "SELECT `c`.`categories_id` as `id`, `c`.`parent_id`, `cd`.`categories_name` `name` FROM `{$this->getOscTable('categories')}` c ";// WHERE `c`.`parent_id`={$parentId}";
        $select .= " INNER JOIN `{$this->getOscTable('categories_description')}` cd on `cd`.`categories_id`=`c`.`categories_id`";
        $select .= " AND `cd`.`language_id`={$defaultLanguageId} ";
        if ($limit && isset($limit['from']) && isset($limit['max'])) {
        	$select .= " LIMIT {$limit['from']}, {$limit['max']} ";
        }
        if (!$results = $this->_getForeignAdapter()->fetchAll($select)) {
            $results = array();
        } else {
            $stores = $this->getLanguagesToStores();
            foreach($results as $index => $result) {
                if ($categoriesToStores = $this->getCategoriesToStores($result['id'])) {
                    foreach($categoriesToStores as $store => $categoriesName) {
						if (!empty($charsetDescription['categories_description']) 
							&& $charsetDescription['categories_description'] != self::DEFAULT_FIELD_CHARSET) {
							$categoriesName = @iconv($charsetDescription['categories_description'],
								self::DEFAULT_FIELD_CHARSET, $categoriesName);
						}
//						$categoriesName = @iconv($charset,
//								self::DEFAULT_FIELD_CHARSET, $categoriesName);
						
                        $results[$index]['stores'][$stores[$store]] = array(
                            'name'=>html_entity_decode($categoriesName, ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET)
                        );
                    }
                }
            }
        }
        return $results;
    }
    
    /**
     * Getting language to Magento store data
     *
     * @return array
     */
    public function getLanguagesToStores()
    {
    	$importModel = $this->getImportModel();
        $typeId = $this->getImportTypeIdByCode('store');
        $importId = $importModel->getId();
        if (!$this->_languagesToStores) {
            //$this->_languagesToStores[1] = 1;
            $select = $this->_getReadAdapter()->select();
            $select->from(array('ref'=>$this->getTable('oscommerce_ref')), array('value'=>'value', 'ref_id'=>'ref_id'));
            $select->where("`ref`.`import_id`='{$importId}' AND `ref`.`type_id`='{$typeId}'");
            $this->_languagesToStores = $this->_getReadAdapter()->fetchPairs($select);
        }
        return $this->_languagesToStores;
    }

    /**
     * Getting categry description for different languages
     *
     * @param integer $categoryId
     * @return mix array/boolean
     */
    public function getCategoriesToStores($categoryId)
    {
        $select = "SELECT `language_id`, `categories_name` FROM `{$this->getOscTable('categories_description')}`";
        $select .= "WHERE `categories_id`='{$categoryId}'";
        if ($categoryId && $result = $this->_getForeignAdapter()->fetchPairs($select)) {
            return $result;
        }
        return false;
    }

    /**
     * Getting store data of osCommerce
     *
     * @return array
     */
    public function getOscStores()
    {
        if (!$this->_oscStores) {
            $select = "SELECT `languages_id` `id`, `name`,  `code` `scode`, ";
            $select .= " `directory` `code`, 1 `is_active` FROM `{$this->getOscTable('languages')}`";
            $this->_oscStores =  $this->_getForeignAdapter()->fetchAll($select);
        }
        return $this->_oscStores;
    }


    public function getOscDefaultLanguage()
    {
        if (!$this->_oscDefaultLanguage) {
            $oscStoreInfo = $this->getOscStoreInformation();
            $languageCode = $oscStoreInfo['DEFAULT_LANGUAGE'];
            if ($stores = $this->getOscStores()) foreach($stores as $store) {
                if ($store['scode'] == $languageCode) {
                    $this->_oscDefaultLanguage = $store;
                }
            }
        }
        return $this->_oscDefaultLanguage;
    }

    /**
     * Getting customers from osCommerce
     *
     * @return array
     */
    public function getCustomers($limit = array())
    {
        $select = "SELECT `c`.`customers_id` `id`, `c`.`customers_firstname` `firstname` ";
        $select .= " ,`c`.`customers_lastname` `lastname`, `c`.`customers_email_address` `email` ";
        $select .= " ,`c`.`customers_telephone` `telephone`, `c`.`customers_fax` `fax` ";
        $select .= " ,`c`.`customers_password` `password_hash`, `c`.`customers_newsletter` `is_subscribed` ";
        $select .= " ,`ci`.`customers_info_date_account_created` `created_at` ";
        $select .= " ,`c`.`customers_default_address_id` `default_billing` FROM `{$this->getOscTable('customers')}` c";
        $select .= " LEFT JOIN `customers_info` ci ON `ci`.`customers_info_id`=`c`.`customers_id` ";
        if ($limit && isset($limit['from']) && isset($limit['max'])) {
            $select .= " LIMIT {$limit['from']}, {$limit['max']}";
        }

        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }

        return $result;
    }

    public function getTotalCustomers() {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('customers')}`");
    }
    
    public function getCustomerName($name)
    {
        if (isset($name)) {
            $n = explode(" ", $name);
            if (sizeof($n) > 1) {
                $newName['lastname'] = $n[(sizeof($n) - 1)];
                $newName['fistname']  = substr($name, 0, strlen($name) - (strlen($newName['lastname'] + 1)));
                return $newName;
            }  else {
                return array('firstname' => $n);
            }
        }
        return false;
    }


    /**
     * Getting customer address by CustomerId from osCommerce
     *
     * @param integer $customerId
     * @return array
     */
    public function getAddresses($customerId)
    {

        $select = "SELECT `address_book_id` `id`, `customers_id`, `entry_firstname` `firstname`";
        $select .= ", `entry_lastname` `lastname`, `entry_street_address` `street1`";
        $select .= ", `entry_company` `company` ";
        $select .= ", `entry_postcode` `postcode`, `entry_city` `city`";
        $select .= ", `entry_state` `region`, `entry_country_id` `country_id`";
        $select .= ", `entry_zone_id` `region_id` FROM `{$this->getOscTable('address_book')}` WHERE customers_id={$customerId}";
        if (!isset($customerId) || !($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }

    /**
     * Get address from address book
     *
     * @param integer $address_id
     * @return array
     */
    public function getAddressById($addressId)
    {

        $select = "SELECT `address_book_id` `id`, `customers_id`, `entry_firstname` `firstname`";
        $select .= ", `entry_lastname` `lastname`, `entry_street_address` `street1`";
        $select .= ", `entry_postcode` `postcode`, `entry_city` `city`";
        $select .= ", `entry_state` `region`, `entry_country_id` `country_id`";
        $select .= ", `entry_zone_id` `region_id` FROM `{$this->getOscTable('address_book')}` WHERE address_book_id={$addressId}";
        if (!isset($addressId) || !($result = $this->_getForeignAdapter()->fetchRow($select))) {
            $result = array();
        }
        return $result;
    }

    /**
     * Getting importing types for loging into oscommerce_ref
     *
     * @return array
     */
    public function getImportTypes()
    {
        if (! $this->_importType) {
            $connection = $this->_getReadAdapter();
            $select = $connection->select();
            $select->from($this->getTable('oscommerce_type'), array('*'));
            $this->_importType = $connection->fetchAll($select);
        }
        return $this->_importType;
    }

    /**
     * Getting import_type_id by code
     *
     * @param integer $code
     * @return string/boolean
     */
    public function getImportTypeIdByCode($code = '') {
        $types = $this->getImportTypes();
        if (isset($code) && $types) {
        	foreach ($types as $type) {
	            if ($type['type_code'] == $code) {
	                return $type['type_id'];
	            }        		
        	}
        }
        return false;
    }

    /**
     * Getting countries data (id,code pairs) from osCommerce
     *
     * @return array
     */
//    public function getCountries()
//    {
//        if (!$this->_countryCode) {
//            $select = "SELECT `countries_id`, `countries_iso_code_2` FROM `countries`";
//            $this->_countryCode = $this->_getForeignAdapter()->fetchPairs($select);
//        }
//        return $this->_countryCode;
//    }

    public function getCountryCodeData()
    {
            $select = "SELECT * FROM `{$this->getOscTable('countries')}`";
            $countries = $this->_getForeignAdapter()->fetchAll($select);
            if ($countries) foreach($countries as $country) {
                $this->_countryIdToCode[$country['countries_id']] = $country['countries_iso_code_2'];
                $this->_countryNameToCode[$country['countries_name']] = $country['countries_iso_code_2'];
            }
    }

    /**
     * Getting country code by country id
     *
     * @param integer $id
     * @return string/boolean
     */
    public function getCountryCodeById($id)
    {
        if (!$this->_countryIdToCode) {
            $this->getCountryCodeData();
        }
        $countries = $this->_countryIdToCode;
        if (isset($id) && isset($countries[$id])) {
            return $countries[$id];
        }
        return false;
    }

    public function getCountryCodeByName($name)
    {
        if (!$this->_countryNameToCode) {
            $this->getCountryCodeData();
        }
        $countries = $this->_countryNameToCode;
        if (isset($id) && isset($countries[$name])) {
            return $countries[$name];
        }
        return false;
    }

    public function getCountryIdByCode($countryCode)
    {
        if (!$this->_countryIdToCode) {
            $this->getCountryCodeData();
        }
        if (isset($code)) {
        	foreach($this->_countryToCode as $id => $code) {
	            if ($code == $countryCode) {
	                return $id;
	            }
        	}
        }
        return false;
    }


    /**
     * Getting regions from osCommerce
     *
     * @return array
     */
    public function getRegions()
    {
        if (!$this->_regionCode) {
            $select = "SELECT `zone_id`, `zone_name` FROM `{$this->getOscTable('zones')}`";
            $this->_regionCode = $this->_getForeignAdapter()->fetchPairs($select);
        }
        return $this->_regionCode;
    }

    /**
     * Getting region name by id
     *
     * @param  integer $id
     * @return string/boolean
     */
    public function getRegionCode($id)
    {
        $regions = $this->getRegions();
        if (isset($id) && isset($regions[$id])) {
            return $regions[$id];
        }
        return false;
    }

    public function setStoreLocales($locale)
    {
        if (isset($locale) && is_array($locale))
            $this->_storeLocales = $locale;
    }

    public function getStoreLocales()
    {
        if ($this->_storeLocales) {
            return $this->_storeLocales;
        } else {
            return array('default' => self::DEFAULT_LOCALE );
        }
    }

    public function setRootCategory(Mage_Catalog_Model_Category $category) {
            $this->_rootCategory = $category;
    }

    public function getRootCategory()
    {
        if (!$this->_rootCategory) {
            $this->_rootCategory = $this->getCategoryModel()->load($this->getCurrentWebsite()->getDefaultStoreGroup()->getRootCategoryId());
        }
        return $this->_rootCategory;
    }

    public function setWebsiteId($id)
    {
        if (isset($id) && is_integer($id)) $this->_websiteId = $id;
    }

    public function importTaxClasses()
    {
        $taxModel = Mage::getModel('tax/class');
        if ($classes = $this->getTaxClasses()) {
            foreach ($classes as $id => $name) {
                $taxModel->unsData();
                $taxModel->setId(null);
                try {
                    $taxModel->setClassType('product');
                    $taxModel->setClassName($name);
                    $taxModel->save();
                } catch (Exception $e) {}
            }
        }
    }

    public function getTaxClasses()
    {
        $select = "SELECT  `tax_class_id` `id`, `tax_class_title` `title` FROM `{$this->getOscTable('tax_class')}`";
        if (!($results = $this->_getForeignAdapter()->fetchPairs($select))) {
            $results = array();
        }
        return $results;
    }

    private function _format($str)
    {
    	$str = preg_replace('#[^0-9a-z\/\.]+#i', '', $str);
    	$str = strtolower(str_replace('\\s','',$str));
    	return $str;
    }

    public function setPrefixPath($prefix) {
        if ($prefix) {
            $this->_prefixPath = $prefix;
        }
    }

    /**
     * Load osCommerce orders
     *
     * @param integer $customerId
     * @param integer $websiteId
     * @return array
     */
    public function loadOrders($customerId, $websiteId = '')
    {
        if (!isset($websiteId)) {
            $webisteId = $this->_currentWebsiteId;
        }
        $result = array();
        if (!empty($customerId)) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('order'=>$this->getTable('oscommerce_order')))
                ->join(
                    array('order_total'=>$this->getTable('oscommerce_order_total')),
                    "order_total.osc_magento_id=order.osc_magento_id AND order_total.class='ot_total'",
                    array('value'))
                ->where("order.magento_customers_id={$customerId}")
                ->where("order.website_id={$websiteId}");
                $result = $this->_getReadAdapter()->fetchAll($select);
        }
        return $result;
    }

    /**
     * Load osCommerce order
     *
     * @param integer $id
     * @return array
     */
    public function loadOrderById($id)
    {
        $result = array();
        if (!empty($id)) {
            $select = "SELECT * FROM {$this->getTable('oscommerce_order')} WHERE osc_magento_id={$id}";
            $order = $this->_getReadAdapter()->fetchRow($select);
            if ($order) {
                $result['order'] = $order;
                foreach (array('products','total','history') as $table) {
                    $select = "SELECT * FROM {$this->getTable('oscommerce_order_'.$table)} WHERE osc_magento_id={$id}";
                    $result[$table] = $this->_getReadAdapter()->fetchAll($select);

                }
            }

        }
        return $result;
    }

    // Fix for previous version
    protected function checkOrderField()
    {
        $columnName = 'currency_symbol';
        try {
            if (!($result = $this->_getReadAdapter()->fetchRow("SHOW `columns` FROM `{$this->getTable('oscommerce_order')}` WHERE field='{$columnName}'"))) {
                $this->_setupConnection()->query("ALTER TABLE `{$this->getTable('oscommerce_order')}` ADD {$columnName} char(3) DEFAULT NULL");
                $this->_setupConnection()->commit();
            }
        } catch (Exception $e) {

        }
    }

    public function setMaxRows($rows)
    {
        if (is_integer($rows)) {
            $this->_maxRows = $rows;
        }
    }
    
    public function getMaxRows()
    {
        if ($this->_maxRows <= 0) {
            $this->_maxRows = Mage::getStoreConfig('oscommerce/import/max_rows');
        }
        return $this->_maxRows;
    }
    
    /**
     * Retrieve website model cache
     *
     * @return Mage_Core_Model_Web
     */
    public function getWebsiteModel()
    {
        if (is_null($this->_websiteModel)) {
            $object = Mage::getModel('core/website');
            $this->_websiteModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_websiteModel);
    }

    /**
     * Retrieve store model cache
     *
     * @return Mage_Core_Model_Store
     */
    public function getStoreModel()
    {
        if (is_null($this->_storeModel)) {
            $object = Mage::getModel('core/store');
            $this->_storeModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_storeModel);
    }

    /**
     * Retrieve customer model cache
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomerModel()
    {
        if (is_null($this->_customerModel)) {
            $object = Mage::getModel('customer/customer');
            $this->_customerModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_customerModel);
    }

    /**
     * Retrieve customer model cache
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomerGroupModel()
    {
        if (is_null($this->_customerGroupModel)) {
            $object = Mage::getModel('customer/group');
            $this->_customerGroupModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_customerGroupModel);
    }

    /**
     * Retrieve address model cache
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddressModel()
    {
        if (is_null($this->_addressModel)) {
            $object = Mage::getModel('customer/address');
            $this->_addressModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_addressModel);
    }


    /**
     * Retrieve category model cache
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCategoryModel()
    {
        if (is_null($this->_categoryModel)) {
            $object = Mage::getModel('catalog/category');
            $this->_categoryModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_categoryModel);
    }

    /**
     * Retrieve category model cache
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getProductModel()
    {
        if (is_null($this->_productModel)) {
            $object = Mage::getModel('catalog/product');
            $this->_productModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_productModel);
    }

    public function getProductAdapterModel()
    {
        if (is_null($this->_productAdapterModel)) {
            $object = Mage::getModel('catalog/convert_adapter_product');
            $this->_productAdapterModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_productAdapterModel);
    }
    /**
     * Retrieve store group model cache
     *
     * @return Mage_Core_Model_Store_Group
     */
    public function getStoreGroupModel()
    {
        if (is_null($this->_storeGroupModel)) {
            $object = Mage::getModel('core/store_group');
            $this->_storeGroupModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_storeGroupModel);
    }

    public function getConfigModel()
    {
        if (is_null($this->_configModel)) {
            $object = Mage::getModel('core/config_data');
            $this->_configModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_configModel);

    }
    
    public function importCollection($importId = null) {
        $importTypes = array('website', 'root_category', 'group');        
        $result = array();
        if (!is_null($importId)) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('ref'=>$this->getTable('oscommerce_ref')))
                ->join(
                    array('type'=>$this->getTable('oscommerce_type')),
                    "type.type_id=ref.type_id AND type.type_code in ('".join("','",$importTypes)."')",
                    array('type.type_code'))
                ->where("ref.import_id={$importId}");
            if ($results = $this->_getReadAdapter()->fetchAll($select)) {
                foreach ($results as $result) {
                    $this->_importCollection[$result['type_code']] = $result['ref_id'];    
                }
            }
            
        }
        return $this->_importCollection;
    }
    
    public function setImportModel(Mage_Oscommerce_Model_Oscommerce $model)
    {
    	$this->_importModel = $model;
    }
    
    public function getImportModel()
    {
    	if ($this->_importModel) {
    		return $this->_importModel;
    	}
    }
    
    public function getCollections($code)
    {
        if ($this->_importCollection) {
            return $this->_importCollection;
        }
        return;
    }    

    public function deleteRecords($id = null)
    {
        if (!is_null($id) && $id > 0) {
            if ($result = $this->_getReadAdapter()
            ->fetchRow('SELECT * FROM '.$this->getTable('oscommerce_ref').' WHERE import_id='.$id)) {
                $this->_getWriteAdapter()->raw_query('DELETE FROM '.$this->getTable('oscommerce_ref').' WHERE import_id='.$id);
            }
        }
    }

	protected function _formatStringTruncate($input, $number)
	{
		if(str_word_count($input,0)>$number)
		{
			$wordKey = str_word_count($input,1);
			$posKey = str_word_count($input,2);
			reset($posKey);
			foreach($wordKey as $key => &$value)
			{
				$value=key($posKey);
				next($posKey);
			}
			return substr($input,0,$wordKey[$number]);
		}
		else {return $input;}
	}
	
	protected function _getCurrentUserId()
	{
		if (!$this->_currentUserId) {
			$this->_currentUserId = Mage::getSingleton('admin/session')->getUser()->getId();
			$this->_logData['user_id'] = $this->_currentUserId;
		}
		return $this->_currentUserId;
	}
	
	
	function checkDetectEncoding()
	{
		if (function_exists('mb_detect_encoding')) {
			return true;
		} else {
			return false;
		}
	}
	
	function getOscTable($table)
	{
		return $this->_prefix.$table;
	}
}
