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
 * @package    Mage_OsCommerce
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OsCommerce resource model
 * 
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */
class Mage_Oscommerce_Model_Mysql4_Oscommerce extends Mage_Core_Model_Mysql4_Abstract
{
	const DEFAULT_WEBSITE_STORE = '1';
	const DEFAULT_WEBSITE_CODE	= 'base';
	const DEFAULT_CATALOG_PATH  = '1/2';
	const DEFAULT_DISPLAY_MODE	= 'PRODUCTS';
	const DEFAULT_IS_ANCHOR		= '0';
	const DEFAULT_STORE			= 'default';
	const DEFAULT_PRODUCT_TYPE	= 'Simple Product';
	const DEFAULT_ATTRIBUTE_SET = 'Default';
	const DEFAULT_VISIBILITY	= 'Catalog, Search';
	
	protected $_importType 	= array();
	protected $_countryCode = array();
	protected $_regionCode  = array();
	protected $_logData		= array();
	
    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce', 'import_id');
        //= Mage::getModel('catalog/category');
        if (!Mage::registry('Object_Cache_Category')) {
            $this->setCache('Object_Cache_Category', Mage::getModel('catalog/category'));
        } 
        $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
        $this->_logData['created_at'] = $this->formatDate(time());
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
    
    public function debug()
    {
        $res = $this->_getForeignAdapter();
        var_dump($res);
    }
    
    /**
     * Importing store data from OsCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     */
    public function importStores(Mage_Oscommerce_Model_Oscommerce $obj)
    {
    	$groupmodel = mage::getmodel('core/store_group')->load(self::DEFAULT_WEBSITE_STORE);
    	$this->_logData['import_id'] = $obj->getId();
    	$this->_logData['type_id'] = $this->getImportTypeIdByCode('store');
    	if ($stores = $this->getStores()) foreach($stores as $store) {
			try {
		    	$this->_logData['value'] = $store['id'];
		    	unset($store['id']);
		    	$store['group_id'] = self::DEFAULT_WEBSITE_STORE;
		    	$storemodel = mage::getmodel('core/store')->setdata($store);
		    	$storemodel->setId(null);
		    	$storemodel->setwebsiteid($groupmodel->getwebsiteid());
		    	$storemodel->save();
		    	$this->_logData['ref_id'] = $storemodel->getId();
    			$this->_logData['created_at'] = $this->formatDate(time());		    	
		    	$this->log($this->_logData);
			} catch (Exception $e) {
			}
    	}
    	unset($stores);
    }
    
	/**
	 * Importing customer/address from OsCommerce to Magento
	 *
	 * @param Mage_Oscommerce_Model_Oscommerce $obj
	 */
    public function importCustomers(Mage_Oscommerce_Model_Oscommerce $obj)
    {
    	$customers = $this->getCustomers();
    	$this->_logData['type_id'] = $this->getImportTypeIdByCode('customer');
    	$this->_logData['import_id'] = $obj->getId();
    	$customerModel = Mage::getModel('customer/customer');
    	$customerAdapterModel = Mage::getModel('customer/convert_adapter_customer');
    	$i = 0;
    	if ($customers)	foreach ($customers as $customer) {
    		$this->_logData['value'] = $customer['id'];

    		if ($customer['default_billing']
    			&& $address = $this->getAddressById($customer['default_billing'])) {
    			unset($address['id']);
    			unset($address['customer_id']);
    			foreach ($address as $field => $value) {
    				if ($field == 'country_id') {
    					$value = $this->getCountryCodeById($value);
    					$field = 'country';
    				}
    				if ($field == 'region_id' 
    					&& in_array($address['country_id'], array(38, 223))) {
    					$field = 'region';
    					$value = $this->getRegionCode($value);	
    				}
    				$customer['shipping_'.$field] = $value;
    				$customer['billing_'.$field] = $value;
    			}
    			unset($customer['default_billing']);
    			$customer['shipping_telephone'] = $customer['telephone'];
    			$customer['billing_telephone'] = $customer['telephone'];
    		} else {
    			$address = $this->getAddresses($$customer['id']);
    			foreach ($address as $field => $value) {
    				if ($field == 'country_id') {
    					$value = $this->getCountryCodeById($value);
    					$field = 'country';
    				}
    				if ($field == 'region_id' 
    					&& in_array($address['country_id'], array(38, 223))) {
    					$field = 'region';
    					$value = $this->getRegionCode($value);	
    				}
    				$customer['shipping_'.$field] = $value;
    				$customer['billing_'.$field] = $value;
    			}
    			unset($customer['default_billing']);
    			$customer['shipping_telephone'] = $customer['telephone'];
    			$customer['billing_telephone'] = $customer['telephone'];
    		}
    		$customer['website_code'] = self::DEFAULT_WEBSITE_CODE;
    		unset($customer['id']); 
    		++$i;
    		$customerAdapterModel->saveRow(array('i'=>$i, 'row'=>$customer));
    		$this->_logData['ref_id'] = $customerAdapterModel->getCustomerId();
    		$this->_logData['created_at'] = $this->formatDate(time());
		    $this->log($this->_logData);
    	}
 		unset($customerModel);
 		unset($customerAdapterModel);
    }
    
    /**
     * Importing categories recursively from OsCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     * @param array $children
     * @param string $parentId
     */
    public function importCategories(Mage_Oscommerce_Model_Oscommerce $obj, $children = array(), $parentId = '1/2')
    {
		$this->_logData['type_id'] = $this->getImportTypeIdByCode('category');
    	$this->_logData['import_id'] = $obj->getId();
    	if ($children) {
    		$categories = $children;
    	} else {
    		$categories = $this->getCategories();
    	}

    	if ($categories) foreach($categories as $category) {
    		// Getting cagetory object from cache
    		$model = $this->getCache('Object_Cache_Category');
    		$model->unsData();
    		$data = array();
    		$data = $category;
    		if (isset($data['children'])) {
    			unset($data['children']);
    		}
    		
    		// Setting and saving category
    		$this->_logData['value'] = $data['id'];
    		$data['path'] = $parentId;
    		$data['is_active'] = 1;
    		$data['description'] = $data['meta_title']  = $data['meta_keywords'] = $data['meta_description'] =  $data['name'];
			$data['display_mode'] = self::DEFAULT_DISPLAY_MODE; 
			$data['is_anchor']	= self::DEFAULT_IS_ANCHOR;
    		unset($data['id']);
    		unset($data['parent_id']);
    		$model->setData($data);
    		$model->save();
    		// End setting and saving category
    		
    		$categoryId = $model->getId();
    		$this->_logData['ref_id'] = $categoryId;
    		$this->_logData['created_at'] = $this->formatDate(time());
    		$this->log($this->_logData);
    		
    		// Checking child category recursively
    		if (isset($category['children'])) {
    			$this->importCategories($obj, $category['children'], 
    				$parentId?$parentId.'/'.$categoryId:$categoryId);
    		}

    	}
    }
       
    public function importProducts(Mage_Oscommerce_Model_Oscommerce $obj)
    {
    	$products = $this->getProducts();
    	$this->_logData['import_id'] = Mage::registry('current_convert_osc')->getId();
    	$this->_logData['type_id'] = $this->getImportTypeIdByCode('product');
    	$productAdapterModel = Mage::getModel('catalog/convert_adapter_product');
    	$i = 0;
    	if ($products) foreach ($products as $product) {

    		$data = array();
    		$data = $product;
    		$this->_logData['value'] = $data['id'];
    		unset($data['id']);
    		//$model = $this->getCache('Object_Cache_Product');
    		if ($categories = $this->getProductCategories($product['id'])) {
    			$data['category_ids'] = $categories;
    		}
    		++$i;

    		try {
	    		$productAdapterModel->saveRow(array('i'=>$i, 'row'=>$data));
	    		$productId = $productAdapterModel->getProductId();
	    		$this->saveProductToWebsite($productId);
	    		$this->_logData['ref_id'] = $productId;
	    		$this->_logData['created_at'] = $this->formatDate(time());
	    		$this->log($this->_logData);
    			
    		} catch (Exception $e) {
    			
    		}
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
    			$this->_getWriteAdapter()->rollBack();
    		}
    	}
    }

    /**
     * Getting products data from OsCommerce
     *
     */
    public function getProducts()
    {
    	$connection = $this->_getForeignAdapter();
    	$select =  "SELECT `p`.`products_id` `id`, `p`.`products_quantity` `qty` ";
    	$select .= ", `p`.`products_model` `sku`, `p`.`products_price` `price` ";
    	$select .= ", `p`.`products_weight` `weight`, IF(`p`.`products_status`,'Enabled','Disabled') `status` ";
    	$select .= ", IF(`p`.`products_status`,'1','0') `is_in_stock`";
    	$select .= ", `pd`.`products_name` `name`, `pd`.`products_description` `description` ";
    	$select .= ", `tc`.`tax_class_title` `tax_class_id`, IF(1,'".self::DEFAULT_VISIBILITY."','') `visibility` ";
    	$select .= ", IF(1,'".self::DEFAULT_ATTRIBUTE_SET."','') `attribute_set` ";
    	$select .= ", IF(1,'".self::DEFAULT_PRODUCT_TYPE ."','') `type` ";
    	$select .= ", IF(1,'".self::DEFAULT_STORE."','') `store` ";
    	$select .= ", IF(1,'".self::DEFAULT_WEBSITE_CODE."','') `website` ";
    	$select .= "FROM `products` p INNER JOIN `products_description` pd ";
    	$select .= "ON `pd`.`products_id`=`p`.`products_id` AND `pd`.`language_id`=1 ";
    	$select .= "LEFT JOIN `tax_class` tc ON `tc`.`tax_class_id`=`p`.`products_tax_class_id` ";
    	if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
    		$result = array();
    	}
    	return $result;
    }    

    /**
     * Getting new created categories recursively using products of OsCommerce
     *
     * @param integer $productId
     * @return string
     */
    public function getProductCategories($productId)
    {
    	$select = "SELECT `categories_id` `id`, `categories_id` `value` FROM `products_to_categories` WHERE products_id={$productId}";
    	if (isset($productId) && $results = $this->_getForeignAdapter()->fetchPairs($select)) {
			$categories = join(',', array_values($results));

    		//$this->_getReadAdapter();
    		$importId = Mage::registry('current_convert_osc')->getId();
    		$typeId = $this->getImportTypeIdByCode('category');
    		$select = $this->_getReadAdapter()->select();
    		$select->from($this->getTable('oscommerce_ref'), array('id'=>'id','ref_id'=>'ref_id'));
    		$select->where("import_id={$importId} AND type_id={$typeId} AND value in (".$categories.")");
    		$result = $this->_getReadAdapter()->fetchPairs($select);
    		if ($result) {
    			return join(',',array_values($result));
    		} 
    		return null;
    	} else {
    		return null;
    	}
    }
    
    /**
     * Getting categories recursively of OsCommerce
     *
     * @param integer $parentId
     * @return array
     */
    public function getCategories($parentId = '0') {
    	$select = "SELECT `c`.`categories_id` as `id`, `c`.`parent_id`, `cd`.`categories_name` `name` FROM `categories` c";
    	$select .= " INNER JOIN `categories_description` cd on `cd`.`categories_id`=`c`.`categories_id`";
    	$select .= " AND `cd`.`language_id`=1 WHERE `c`.`parent_id`={$parentId}";
    	if (!$results = $this->_getForeignAdapter()->fetchAll($select)) {
    		$results = array();
    	} else {
    		foreach($results as $index => $result) {
    			$sub = $this->getCategories($result['id']);
    			if ($sub) {
    				$results[$index]['children'] = $sub;
    			}
    		}
    	}
    	return $results;    	
    }
    /**
     * Getting store data of OsCommerce
     *
     * @return array
     */
    public function getStores()
    {
    	$select = "SELECT `languages_id` `id`, `name`,";
    	$select .= " `directory` `code`, 1 `is_active` FROM `languages`";
    	if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
    		$result = array();
    	}
    	return $result;
    }
        
    
    /**
     * Getting customers from OsCommerce
     *
     * @return array
     */
    public function getCustomers()
    {
    	$select = "SELECT `customers_id` `id`, `customers_firstname` `firstname`";
    	$select .= ", `customers_lastname` `lastname`, `customers_email_address` `email`";
    	$select .= ", `customers_telephone` `telephone`";
    	$select .= ", `customers_password` `password_hash`, `customers_newsletter` `is_subscribed`";
    	$select .= ", `customers_default_address_id` `default_billing` FROM `customers`";
    	if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
    		$result = array();
    	}
    	return $result;
    }
    
    /**
     * Getting customer address by CustomerId from OsCommerce
     *
     * @param integer $customerId
     * @return array
     */
    public function getAddresses($customerId)
    {
    	
    	$select = "SELECT `address_book_id` `id`, `customers_id`, `entry_firstname` `firstname`";
    	$select .= ", `entry_lastname` `lastname`, `entry_street_address` `street1`";
    	$select .= ", `entry_postcode` `postcode`, `entry_city` `city`";
    	$select .= ", `entry_state` `region`, `entry_country_id` `country_id`";
    	$select .= ", `entry_zone_id` `region_id` FROM `address_book` WHERE customers_id={$customerId}";
    	if (!isset($customerId) || !($result = $this->_getForeignAdapter()->fetchRow($select))) {
    		$result = array();
    	}
    	return $result;    	
    }

    /**
     * Enter description here...
     *
     * @param integer $address_id
     * @return array
     */
    public function getAddressById($address_id)
    {
    	
    	$select = "SELECT `address_book_id` `id`, `customers_id`, `entry_firstname` `firstname`";
    	$select .= ", `entry_lastname` `lastname`, `entry_street_address` `street1`";
    	$select .= ", `entry_postcode` `postcode`, `entry_city` `city`";
    	$select .= ", `entry_state` `region`, `entry_country_id` `country_id`";
    	$select .= ", `entry_zone_id` `region_id` FROM `address_book` WHERE address_book_id={$address_id}";
    	if (!isset($address_id) || !($result = $this->_getForeignAdapter()->fetchRow($select))) {
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
    	if (isset($code) && $types) foreach ($types as $type) {
    		if ($type['type_code'] == $code) {
    			return $type['type_id'];
    		}
    	}
    	return false;
    }
    
    /**
     * Getting countries data (id,code pairs) from OsCommerce
     *
     * @return array
     */
    public function getCountries() 
    {
    	if (!$this->_countryCode) {
	    	$select = "SELECT `countries_id`, `countries_iso_code_2` FROM `countries`";
	    	$this->_countryCode = $this->_getForeignAdapter()->fetchPairs($select);
    	} 
    	return $this->_countryCode;	
    }
    
    /**
     * Saving product to given website
     *
     * @param integer $productId
     * @param integer $websiteId
     */
    public function saveProductToWebsite($productId, $websiteId = '')
    {
    	if (isset($productId)) {
    		$websiteId = strlen($websiteId) == 0 ? Mage::app()->getWebsite(self::DEFAULT_WEBSITE_CODE)->getWebsiteId(): $websiteId;
    		$this->_getWriteAdapter()->beginTransaction();
    		$data = array('product_id'=>$productId, 'website_id' => $websiteId);
    		try {
    			$this->_getWriteAdapter()->insert($this->getTable('catalog_product_website'), $data);
    			$this->_getWriteAdapter()->commit();
    		} catch (Exception $e) {
    			$this->_getWriteAdapter()->rollBack();
    		}    		
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
    	$countries = $this->getCountries();
    	if (isset($id) && isset($countries[$id])) {
    		return $countries[$id];
    	}
    	return false;
    }
    
    /**
     * Getting regions from OsCommerce
     *
     * @return array
     */
    public function getRegions()
    {
    	if (!$this->_regionCode) {
	    	$select = "SELECT `zone_id`, `zone_name` FROM `zones`";
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
    
    /**
     * Getting Mage_Catalog_Model_Category from cache
     *
     * @param Mage_Catalog_Model_Category $category
     */
    protected function setCacheCategory(Mage_Catalog_Model_Category $category)
    {
        $id = Varien_Object_Cache::singleton()->save($category);
        Mage::register('Object_Cache_Category', $id);    	
    }

    protected function setCache($name, $obj)
    {
        $id = Varien_Object_Cache::singleton()->save($obj);
        Mage::register($name, $id);    	
    }
       
    protected function getCacheCategory()
    {
    	return Varien_Object_Cache::singleton()->load(Mage::registry('Object_Cache_Category'));
    }
    
    protected function getCache($name)
    {
    	return Varien_Object_Cache::singleton()->load(Mage::registry($name));
    }    
}
