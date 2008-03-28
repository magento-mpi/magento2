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
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */
class Mage_Oscommerce_Model_Mysql4_Oscommerce extends Mage_Core_Model_Mysql4_Abstract
{
    const DEFAULT_WEBSITE_GROUP = 1;
    const DEFAULT_WEBSITE_ID	= 1;
    const DEFAULT_WEBSITE_CODE	= 'base';
    const DFFAULT_PARENT_CATEGORY_ID = 1;
    const DEFAULT_CATALOG_PATH  = '1/2';
    const DEFAULT_DISPLAY_MODE	= 'PRODUCTS';
    const DEFAULT_IS_ANCHOR		= '0';
    const DEFAULT_STORE		= 'default';
    const DEFAULT_PRODUCT_TYPE	= 'Simple Product';
    const DEFAULT_ATTRIBUTE_SET = 'Default';
    const DEFAULT_VISIBILITY	= 'Catalog, Search';
    const DEFAULT_LOCALE        = 'en_US';

    protected $_importType 	            = array();
    protected $_countryIdToCode         = array();
    protected $_countryNameToCode       = array();
    protected $_regionCode              = array();
    protected $_logData                 = array();
    protected $_languagesToStores       = array();
    protected $_prefix                  = '';
    protected $_storeLocales            = array();
    protected $_rootCategory            = '';
    protected $_storeGroup              = '';
    protected $_storeGroupId            = '';
    protected $_website                 = '';
    protected $_tableOrders             = '';
    protected $_storeInformation        = '';
    protected $_websiteCode             = '';
    protected $_isProductWithCategories = false;
    protected $_setupConnection ;
    protected $_resultStatistic         = array();
    protected $_customerIdPair          = array();
    protected $_prefixPath               = '';
    
    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce', 'import_id');
        //= Mage::getModel('catalog/category');
        if (!Mage::registry('Object_Cache_Category')) {
            $this->setCache('Object_Cache_Category', Mage::getModel('catalog/category'));
        }

        if (!Mage::registry('Object_Cache_Product')) {
            $this->setCache('Object_Cache_Product', Mage::getModel('catalog/product'));
        }

//        if (!Mage::registry('Object_Cache_Config')) {
//            $this->setCache('Object_Cache_Config', Mage::getModel('adminhtml/config_data'));
//        }
        
        $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
        $this->_logData['created_at'] = $this->formatDate(time());
        
        $this->_setupConnection = Mage::getSingleton('core/resource')->getConnection('oscommerce_setup');
        $this->_resultStatistic = array(
            'products'=>array('total' => 0, 'imported' =>0),
            'categories' =>array('total' => 0, 'imported' =>0),
            'customers' =>array('total' => 0, 'imported' =>0));
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

    public function setWebsiteCode($code)
    {
        if (isset($code)) $this->_websiteCode = $code;
    }
    
    /**
     * 
     */
    public function createWebsite($obj, $isCreate = true)
    {
        $website  = Mage::getModel('core/website');
        if ($isCreate) {

            $storeInfo = $this->getStoreInformation();
            $website->setName($storeInfo['STORE_NAME']);
            $website->setCode($this->_websiteCode ? $this->_websiteCode : $this->_format($storeInfo['STORE_NAME']));
            $website->save();
    //        $this->setWebsiteId($website->getId());
            $this->_logData['import_id']  = $obj->getId();
            $this->_logData['type_id']      = $this->getImportTypeIdByCode('website'); 
            $this->_logData['ref_id']         = $website->getId();
            $this->_logData['created_at']  = $this->formatDate(time());
            $this->log($this->_logData);
        } else {
            $website->load(self::DEFAULT_WEBSITE_ID);
        }
        $this->setWebsite($website);
        $this->createRootCategory();
        $this->createStoreGroup($obj);
        
    }
    
    public function setWebsite(Mage_Core_Model_Website $website) 
    {
        $this->_website = $website;    
    }
    
    public function getWebsite()
    {
        if (!$this->_website) {
            $this->_website = Mage::getModel('core/website')->load(self::DEFAULT_WEBSITE_ID);
        }
        return $this->_website;
    }
    
    public function createStoreGroup($obj)
    {        
        $storeInfo = $this->getStoreInformation();
        $website = $this->getWebsite();
        $data['website_id'] = $website->getId();
        $data['name'] = ($website->getId()==self::DEFAULT_WEBSITE_ID ? $storeInfo['STORE_NAME']: $website->getName()). ' Store';
        $data['root_category_id'] = $this->getRootCategory()->getId();
        $model = Mage::getModel('core/store_group');
        $model->setData($data);
        $model->save();
        $website->setDefaultGroupId($model->getId());
        $website->save();
        $this->setStoreGroup($model);
        $this->_logData['import_id']    = $obj->getId();
        $this->_logData['type_id']      = $this->getImportTypeIdByCode('group'); 
        $this->_logData['ref_id']       = $model->getId();
        $this->_logData['created_at']   = $this->formatDate(time());
        $this->log($this->_logData);
    }

    public function setStoreGroup(Mage_Core_Model_Store_Group $group) 
    {
        $this->_storeGroup = $group;    
    }
    
    public function getStoreGroup()
    {
        return $this->_storeGroup;
    }
    
    public function getStoreGroupId()
    {
        if ($this->_storeGroup) {
            return $this->_storeGroup->getId();
        } else {
            return self::DEFAULT_WEBSITE_GROUP;
        }
    }    
    
    public function createRootCategory()
    {
        $model = $this->getCache('Object_Cache_Category');
        $model->unsData();
        $website = $this->getWebsite();
        $storeInfo = $this->getStoreInformation();
                
         $data = array();
         $data['is_active'] = 1;
         $data['display_mode'] = self::DEFAULT_DISPLAY_MODE;
         $data['is_anchor']	= self::DEFAULT_IS_ANCHOR;
         $data['description'] = $data['meta_title']  = $data['meta_keywords'] = $data['meta_description'] =  $data['name'] = ($website->getId() == self::DEFAULT_WEBSITE_ID ? $storeInfo['STORE_NAME']:$website->getName()). " Category";
         $model->setData($data);
         $model->save();
         $newParentId = $model->getId();
         $model->setPath('1/'.$newParentId);
         $model->save();
         $this->setRootCategory(clone $model);
    }
    
    /**
     * Importing store data from osCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     */
    public function importStores(Mage_Oscommerce_Model_Oscommerce $obj)
    {
        //    	$groupmodel = mage::getmodel('core/store_group')->load(self::DEFAULT_WEBSITE_GROUP);
        $this->_logData['import_id'] = $obj->getId();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('store');
        $locales = $this->getStoreLocales();
        $defaultStore = '';
        $storeInformation = $this->getStoreInformation();
        $defaultStoreCode = $storeInformation['DEFAULT_LANGUAGE'];
        if ($stores = $this->getStores()) foreach($stores as $store) {
            try {
                $this->_logData['value'] = $store['id'];
                unset($store['id']);
                $store['group_id'] = $this->getStoreGroupId();
                $store['website_id'] = $this->getWebsiteId();
                $store['name'] = iconv("ISO-8859-1", "UTF-8", $store['name']);
                $storeModel = Mage::getModel('core/store')->setData($store);
                $storeModel->setId(null);
                $storeModel->save();
                $this->_logData['ref_id'] = $storeModel->getId();
                $this->_logData['created_at'] = $this->formatDate(time());
                $this->log($this->_logData);
                $config = Mage::getModel('core/config_data');
                $config->setScope('stores')
                    ->setScopeId($storeModel->getId())
                    ->setPath('general/locale/code')
                    ->setValue(isset($locales[$storeModel->getCode()])?$locales[$storeModel->getCode()]: $locales['default'])
                    ->save();
                if ($store['scode'] == $defaultStoreCode) {
                    $defaultStore = $storeModel->getId();
                }
            } catch (Exception $e) {

            }
        }
        if ($defaultStore) {
            $storeGroup = $this->getStoreGroup();
            $storeGroup->setDefaultStoreId($defaultStore);
            $storeGroup->save();
        }
        unset($stores);
    }

    /**
      * Importing customer/address from osCommerce to Magento
      * 
      *  @param Mage_Oscommerce_Model_Oscommerce $obj
      */
    public function importCustomers(Mage_Oscommerce_Model_Oscommerce $obj)
    {
        $customers = $this->getCustomers();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('customer');
        $this->_logData['import_id'] = $obj->getId();
        $customerModel = Mage::getModel('customer/customer');
        $websiteCode = $this->getWebsite()->getCode();
        $customerAdapterModel = Mage::getModel('customer/convert_adapter_customer');
        $i = 0;
        $result['total'] = sizeof($customers);
        if ($customers)	foreach ($customers as $customer) {
            $this->_logData['value'] = $customer['id'];
            ++$i;
            
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
            $customer['website_code'] = $websiteCode ? $websiteCode: self::DEFAULT_WEBSITE_CODE;
            unset($customer['id']);

            try {
                $customerAdapterModel->saveRow(array('i'=>$i, 'row'=>$customer));
                $customerId = $customerAdapterModel->getCustomerId();
                $this->_customerIdPair[$this->_logData['value']] = $customerId;
                $this->_logData['ref_id'] = $customerId;
                $this->_logData['created_at'] = $this->formatDate(time());
                $this->log($this->_logData);
                $this->_resultStatistic['customers']['imported'] += 1;
            } catch (Exception $e) {
                
            }
        }
        $this->_resultStatistic['customers']['total'] = $i;
        unset($customerModel);
        unset($customerAdapterModel);
    }

    /**
     * Importing categories recursively from osCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     * @param array $children
     * @param string $parentId
     */
    public function importCategories(Mage_Oscommerce_Model_Oscommerce $obj, $children = array(), $parentId = '')
    {
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('category');
        $this->_logData['import_id'] = $obj->getId();
        $rootPath = $this->getRootCategory()->getPath();
        $parentId = $parentId?$parentId:$rootPath;
        if ($children) {
            $categories = $children;
        } else {
            $categories = $this->getCategories();
        }

        // Getting cagetory object from cache
        $model = $this->getCache('Object_Cache_Category');
        $model->unsData();
        
        if ($categories) foreach($categories as $category) {
            $data = array();
            $data = $category;
            if (isset($data['children'])) {
                unset($data['children']);
            }
            
            $this->_resultStatistic['categories']['total'] += 1;
            // Setting and saving category
            $this->_logData['value'] = $data['id'];

            $data['is_active'] = 1;
            $data['display_mode'] = self::DEFAULT_DISPLAY_MODE;
            $data['is_anchor']	= self::DEFAULT_IS_ANCHOR;
            unset($data['id']);
            unset($data['parent_id']);
            $data['description'] = $data['meta_title']  = $data['meta_keywords'] = $data['meta_description'] =  iconv("ISO-8859-1", "UTF-8", $data['name']);
            try {
                $model->setData($data);
                $model->save();
                $newParentId = $model->getId();
                $model->setPath($parentId.'/'.$newParentId);
                $model->save();
                $this->_logData['ref_id'] = $newParentId;
                $this->_logData['created_at'] = $this->formatDate(time());
                $this->log($this->_logData);
                $data['path'] = $model->getPath();
                if (isset($category['stores'])) foreach($category['stores'] as $store=>$name) {
    
                    $data['description'] = $data['meta_title']  = $data['meta_keywords'] = $data['meta_description'] =  $data['name'] = iconv("ISO-8859-1", "UTF-8", $name);
                    $model->addData($data);
                    $model->setStoreId($store);
                    $model->save();
                }
                $this->_resultStatistic['categories']['imported'] += 1;
            } catch (Exception $e) {
                
            }
            // End setting and saving category

            // Checking child category recursively
            if (isset($category['children'])) {
                $this->importCategories($obj, $category['children'],
                $data['path']);
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
        $count = 0;
        $tmpPath = Mage::getSingleton('catalog/product_media_config')->getBaseTmpMediaPath();
        $imageTmpPath = $tmpPath.($this->_prefixPath?$this->_prefixPath:DS);
       
        if ($products) foreach ($products as $product) {

            $data = array();
            $data = $product;
            $data['name'] = iconv("ISO-8859-1", "UTF-8", $data['name']);
            $data['description'] = iconv("ISO-8859-1", "UTF-8", $data['description']);
            $this->_logData['value'] = $data['id'];
            unset($data['id']);
            //$model = $this->getCache('Object_Cache_Product');
            if ($this->_isProductWithCategories &&  $categories = $this->getProductCategories($product['id'])) {
                $data['category_ids'] = $categories;
            }
            
            ++$i;

            try {
                $row = $data;
                $productAdapterModel->saveRowSilently(compact('i','row'));
                $productId = $productAdapterModel->getProductId();
                $productModel = $this->getCache('Object_Cache_Product')->load($productId);
                $this->saveProductToWebsite($productId);
                $this->_logData['ref_id'] = $productId;
                $this->_logData['created_at'] = $this->formatDate(time());
                $this->log($this->_logData);           
               
                if ($data['image'] && file_exists($imageTmpPath.$data['image'])) {
                    try {
                        $filename = ($this->_prefixPath?$this->_prefixPath:DS).$data['image'];
                        $mediaGalleryData = $productModel->getMediaGallery();
                        if (!is_array($mediaGalleryData)) {
                            $mediaGalleryData = array('images' => array());
                        }
                        $mediaGalleryData['images'][] =  array(
                            'file'=>$filename,
                            'label' => $data['name'],
                            'position' => 1);
                        $productModel->setMediaGallery($mediaGalleryData);
                        $productModel->setImage($filename);
                        $productModel->save();                                                              
                    } catch (Exception $e) {
                        echo "Image gallery ".$e->getMessage()."<br />";        
                    }
                }                
                
                $mageStores = $this->getLanguagesToStores();
                if ($stores = $this->getProductStores($productId)) foreach ($stores as $store) {
                    $productModel->setStoreId($mageStores[$store['store']]);
                    $productModel->setName(iconv("ISO-8859-1", "UTF-8", $store['name']));
                    $productModel->setDescription(iconv("ISO-8859-1", "UTF-8", $store['description']));
                    $productModel->save();
                }
                ++$count;
            } catch (Exception $e) {
                echo "Saving product ".$e->getMessage()."<br />";
            }
        }
        $this->_resultStatistic['products'] = array('total' => sizeof($products), 'imported' => $count);
    }

    public function importOrders($obj)
    {
        $customerIdPair = $this->_customerIdPair;
        $importId  = $obj->getId();
        $websiteId = $this->getWebsite()->getId();
        $tablePrefix = (string)Mage::getConfig()->getNode('global/resources/db/table_prefix');
        $tables = array(
            'orders' => "CREATE TABLE IF NOT EXISTS `{$tablePrefix}oscommerce_orders` (
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
              `orders_status` int(5) NOT NULL default '0',
              `orders_date_finished` datetime default NULL,
              `currency` char(3) default NULL,
              `currency_value` decimal(14,6) default NULL,
              PRIMARY KEY  (`osc_magento_id`),
              KEY `idx_orders_customers_id` (`customers_id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
        "
            , 'orders_products' => "CREATE TABLE IF NOT EXISTS `{$tablePrefix}oscommerce_orders_products` (
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
            ) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;"
            
            , 'orders_total' => "CREATE TABLE IF NOT EXISTS `{$tablePrefix}oscommerce_orders_total` (
              `orders_total_id` int(10) unsigned NOT NULL auto_increment,
              `osc_magento_id` int(11) NOT NULL default '0',
              `title` varchar(255) NOT NULL default '',
              `text` varchar(255) NOT NULL default '',
              `value` decimal(15,4) NOT NULL default '0.0000',
              `class` varchar(32) NOT NULL default '',
              `sort_order` int(11) NOT NULL default '0',
              PRIMARY KEY  (`orders_total_id`),
              KEY `idx_orders_total_osc_magento_id` (`osc_magento_id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;"
            
            , 'orders_status_history'=>"CREATE TABLE IF NOT EXISTS `{$tablePrefix}oscommerce_orders_status_history` (
              `orders_status_history_id` int(11) NOT NULL auto_increment,
              `osc_magento_id` int(11) NOT NULL default '0',
              `orders_status_id` int(5) NOT NULL default '0',
              `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
              `customer_notified` int(1) default '0',
              `comments` text,
              PRIMARY KEY  (`orders_status_history_id`),
              KEY `idx_orders_status_history_osc_magento_id` (`osc_magento_id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;");
            
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
        //$this->_tableOrders = $tables;
        //foreach ($tables as $table => $schema) {

        
        
        // Get orders
        $total =  $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->_prefix}orders`");
        $max = 100;
        $page = (int) $total/$max;
        $orderCount = 0;
        if ($total <= 100)  {
            $results = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders`");

            if ($results) foreach ($results as $result) {
                $result['magento_customers_id'] = $customerIdPair[$result['customers_id']]; // get Magento CustomerId
                $result['import_id'] = $importId;
                $result['website_id'] = $websiteId;
                $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders", $result);
                $oscMagentoId = $this->_getWriteAdapter()->lastInsertId();
                ++$orderCount;
                // Get orders products
                if ($orderProducts = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_products` WHERE `orders_id`={$result['orders_id']}")) {
                    foreach ($orderProducts as $orderProduct) {
                        unset($orderProduct['orders_id']);
                        unset($orderProduct['orders_products_id']);
                        $orderProduct['osc_magento_id'] = $oscMagentoId;

                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_products", $orderProduct);


                    }
                }

                // Get orders totals
                if ($orderTotals = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_total` WHERE `orders_id`={$result['orders_id']}")) {
                    foreach ($orderTotals as $orderTotal) {
                        unset($orderTotal['orders_id']);
                        unset($orderTotal['orders_total_id']);
                        $orderTotal['osc_magento_id'] = $oscMagentoId;

                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_total", $orderTotal);


                    }
                }

                // Get orders totals
                if ($orderHistories = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_status_history` WHERE `orders_id`={$result['orders_id']}")) {
                    foreach ($orderHistories as $orderHistory) {
                        unset($orderHistory['orders_id']);
                        unset($orderHistory['orders_status_history_id']);
                        $orderHistory['osc_magento_id'] = $oscMagentoId;

                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_status_history", $orderHistory);


                    }
                }

            }
        } else {
            for ($i = 1; $i <= $page; $i++) {
                $results = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders` LIMIT ".($i>1?($i*$max+1):$i).", $max");
                $this->_getWriteAdapter()->beginTransaction();
                if ($results) foreach ($results as $result) {

                    $result['magento_customers_id'] = $customerIdPair[$result['customers_id']]; // get Magento CustomerId
                    $result['import_id'] = $importId;
                    $result['website_id'] = $websiteId;
                    $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders", $result);
                    $this->_getWriteAdapter()->commit();
                    $oscMagentoId = $this->_getWriteAdapter()->lastInsertId();
                    ++$orderCount;
                    // Get orders products
                    if ($orderProducts = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_products` WHERE `orders_id`={$result['orders_id']}")) {
                        foreach ($orderProducts as $orderProduct) {
                            unset($orderProduct['orders_id']);
                            unset($orderProduct['orders_products_id']);
                            $orderProduct['unique_id'] = $oscMagentoId;
                            $this->_getWriteAdapter()->beginTransaction();

                            $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_products", $orderProduct);


                        }
                    }

                    // Get orders totals
                    if ($orderTotals = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_total` WHERE `orders_id`={$result['orders_id']}")) {
                        foreach ($orderTotals as $orderTotal) {
                            unset($orderTotal['orders_id']);
                            unset($orderTotal['orders_total_id']);
                            $orderTotal['unique_id'] = $oscMagentoId;
                            $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_total", $orderTotal);

                        }
                    }

                    // Get orders totals
                    if ($orderHistories = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_status_history` WHERE `orders_id`={$result['orders_id']}")) {
                        foreach ($orderHistories as $orderHistory) {
                            unset($orderHistory['orders_id']);
                            unset($orderHistory['orders_status_history_id']);
                            $orderHistory['unique_id'] = $oscMagentoId;

                            $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_status_history", $orderHistory);
                        }

                    }
                }

            }
        }
    
                    
        
        
        
        
        
        
        /*
            // Get orders
            $total =  $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->_prefix}orders`");
            $max = 100;
            $page = (int) $total/$max;
            $orderCount = 0;
            if ($total <= 100)  {
                $results = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders`");
                $this->_getWriteAdapter()->beginTransaction();
                if ($results) foreach ($results as $result) {
                       try {
                            $result['magento_customers_id'] = $customerIdPair[$result['customers_id']]; // get Magento CustomerId                           
                            $result['import_id'] = $importId;
                            $result['website_id'] = $websiteId;
                            $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders", $result);
                            $this->_getWriteAdapter()->commit();
                            $oscMagentoId = $this->_getWriteAdapter()->lastInsertId("{$tablePrefix}oscommerce_orders", 'unique_id');
                            ++$orderCount;
                            // Get orders products
                            if ($orderProducts = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_products` WHERE `orders_id`={$result['orders_id']}")) {
                                foreach ($orderProducts as $orderProduct) {
                                    unset($orderProduct['orders_id']);
                                    unset($orderProduct['orders_products_id']);
                                    $orderProduct['osc_magento_id'] = $oscMagentoId;
                                    $this->_getWriteAdapter()->beginTransaction();
                                    try {
                                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_products", $orderProduct); 
                                        $this->_getWriteAdapter()->commit();
                                    } catch (Exception $e) {
                                        // Rollback
                                    }
                                    
                                }
                            }
                            
                            // Get orders totals
                            if ($orderTotals = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_total` WHERE `orders_id`={$result['orders_id']}")) {
                                foreach ($orderTotals as $orderTotal) {
                                    unset($orderTotal['orders_id']);
                                    unset($orderTotal['orders_total_id']);
                                    $orderTotal['osc_magento_id'] = $oscMagentoId;
                                    $this->_getWriteAdapter()->beginTransaction();
                                    try {
                                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_total", $orderTotal); 
                                        $this->_getWriteAdapter()->commit();
                                    } catch (Exception $e) {
                                        // Rollback
                                    }
                                    
                                }
                            }                            
                            
                            // Get orders totals
                            if ($orderHistories = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_status_history` WHERE `orders_id`={$result['orders_id']}")) {
                                foreach ($orderHistories as $orderHistory) {
                                    unset($orderHistory['orders_id']);
                                    unset($orderHistory['orders_status_history_id']);
                                    $orderHistory['osc_magento_id'] = $oscMagentoId;
                                    $this->_getWriteAdapter()->beginTransaction();
                                    try {
                                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_status_history", $orderHistory); 
                                        $this->_getWriteAdapter()->commit();
                                    } catch (Exception $e) {
                                        // Rollback
                                    }
                                    
                                }
                            }                                               
                            
                            
                        } catch (Exception $e) {
                            //$this->_getWriteAdapter()->rollBack();
                        }  
                }
            } else {
                for ($i = 1; $i <= $page; $i++) {
                    $results = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders` LIMIT ".($i>1?($i*$max+1):$i).", $max");
                    $this->_getWriteAdapter()->beginTransaction();  
                    if ($results) foreach ($results as $result) { 
                        try {
                            $result['magento_customers_id'] = $customerIdPair[$result['customers_id']]; // get Magento CustomerId                           
                            $result['import_id'] = $importId;
                            $result['website_id'] = $websiteId;
                            $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders", $result);
                            $this->_getWriteAdapter()->commit();
                            $oscMagentoId = $this->_getWriteAdapter()->lastInsertId();
                            ++$orderCount;
                            // Get orders products
                            if ($orderProducts = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_products` WHERE `orders_id`={$result['orders_id']}")) {
                                foreach ($orderProducts as $orderProduct) {
                                    unset($orderProduct['orders_id']);
                                    unset($orderProduct['orders_products_id']);
                                    $orderProduct['osc_magento_id'] = $oscMagentoId;
                                    $this->_getWriteAdapter()->beginTransaction();
                                    try {
                                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_products", $orderProduct); 
                                        $this->_getWriteAdapter()->commit();
                                    } catch (Exception $e) {
                                        // Rollback
                                    }
                                    
                                }
                            }
                            
                            // Get orders totals
                            if ($orderTotals = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_total` WHERE `orders_id`={$result['orders_id']}")) {
                                foreach ($orderTotals as $orderTotal) {
                                    unset($orderTotal['orders_id']);
                                    unset($orderTotal['orders_total_id']);
                                    $orderTotal['osc_magento_id'] = $oscMagentoId;
                                    $this->_getWriteAdapter()->beginTransaction();
                                    try {
                                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_total", $orderTotal); 
                                        $this->_getWriteAdapter()->commit();
                                    } catch (Exception $e) {
                                        // Rollback
                                    }
                                    
                                }
                            }                            
                            
                            // Get orders totals
                            if ($orderHistories = $this->_getForeignAdapter()->fetchAll("SELECT * FROM `{$this->_prefix}orders_status_history` WHERE `orders_id`={$result['orders_id']}")) {
                                foreach ($orderHistories as $orderHistory) {
                                    unset($orderHistory['orders_id']);
                                    unset($orderHistory['orders_status_history_id']);
                                    $orderHistory['osc_magento_id'] = $oscMagentoId;
                                    $this->_getWriteAdapter()->beginTransaction();
                                    try {
                                        $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_status_history", $orderHistory); 
                                        $this->_getWriteAdapter()->commit();
                                    } catch (Exception $e) {
                                        // Rollback
                                    }
                                    
                                }
                            }                                               
                            
                            
                        } catch (Exception $e) {
                            //$this->_getWriteAdapter()->rollBack();
                        }            
                    }                    
                }
            }
            */
        //}  
            $this->_resultStatistic['orders']['total'] = $total;      
            $this->_resultStatistic['orders']['imported'] = $orderCount;
    }
    
    /**
     * Importing orders from osCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     */
//    public function importOrders(Mage_Oscommerce_Model_Oscommerce $obj)
//    {
//        $this->_getForeignAdapter()->query("")
//        $orders = $this->getOrders();   
//        $billing = Mage::getModel('sales/order_address');
//        $orderModel = Mage::getModel('sales/order');
//        $orderItem = Mage::getModel('sales/order_item');
//        $productModel = Mage::getModel('catalog/product');
//      
//        if ($orders) foreach ($orders as $order) {
//            $billing->setStreet1($order['billing_street_address']);
//            $billing->setCity($order['billing_city']);
//            $billing->setPostCode($order['billing_postcode']);
//            $billing->setCountry($this->getCountryCodeByName($order['billing_country']));
//            $billing->setRegion($order['billing_state']);
//            $billingName = $this->getCustomerName($order['billing_name']);
//            $billing->setFirstname($billingName['firstname']);
//            $billing->setLastname($billingName['lastname']);
//            $billing->setType('billing');
//            $orderModel->addAddress($billing);
//            
//            $shipping = clone $billing;
//            $shipping->unsData();
//            $shipping->setStreet1($order['delivery_street_address']);
//            $shipping->setCity($order['delivery_city']);
//            $shipping->setPostCode($order['delivery_postcode']);
//            $shipping->setCountry($this->getCountryCodeByName($order['delivery_country']));
//            $shipping->setRegion($order['delivery_state']);
//            $shippingName = $this->getCustomerName($order['deliver_name']);
//            $shipping->setFirstname($billingName['firstname']);
//            $shipping->setLastname($billingName['lastname']);
//            $shipping->setType('shipping');
//            $orderModel->addAddress($shipping);
//            
//            // getting orders_products
//            $orderProducts = $this->getOrdersProducts($order['orders_id']);
//            if ($orderProducts) foreach($orderProducts as $product) {
//                $productId = $productModel->getIdBySku($product['products_model']);
//                $productModel->load($productId);
//                $orderItem->unsData();
//                if ($productModel->getId()) {
//                    $orderItem->setStoreId($productModel->getStoreId())
//                    ->setQuoteItemId($productModel->getId())
//                    ->setProductId($productModel->getProductId())
//                    ->setSuperProductId($productModel->getSuperProductId())
//                    ->setParentProductId($productModel->getParentProductId())
//                    ->setSku($productModel->getSku())
//                    ->setName($productModel->getName())
//                    ->setDescription($productModel->getDescription())
//                    ->setWeight($productModel->getWeight())
//                    ->setIsQtyDecimal($productModel->getIsQtyDecimal())
//                    ->setQtyOrdered($product['products_quantity'])
//                    ->setOriginalPrice($product['products_price'])
////                    ->setAppliedRuleIds($item->getAppliedRuleIds())
////                    ->setAdditionalData($item->getAdditionalData())
//
//                    ->setPrice($product['final_price'])
////                    ->setTaxPercent($item->getTaxPercent())
//                    ->setTaxAmount($product['products_tax'])
//                    ->setRowWeight($product->getRowWeight());
//
//                    //        Mage::dispatchEvent('sales_convert_quote_item_to_order_item',
//                    //            array('order_item'=>$orderItem, 'item'=>$item)
//                    //        );
//                    //        return $orderItem;
//                    $orderModel->addItem($orderItem);
//                }
//            }
//
//            // getting orders_total
//            $orderTotals = $this->getOrdersTotal($order['orders_id']);
//            if ($orderTotals) {
//                print_r($orderTotals);
//            }
//            
//        }
//    }
    
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

    public function getStoreInformation()
    {
        if (!$this->_storeInformation) {
            $select =  "SELECT `configuration_key` `key`, `configuration_value` `value` FROM `{$this->_prefix}configuration`";
            $select .= " WHERE `configuration_key` IN ('STORE_NAME', 'STORE_OWNER', 'STORE_OWNER_EMAIL', 'STORE_COUNTRY',' STORE_ZONE','DEFAULT_LANGUAGE')";
            if (!($result = $this->_getForeignAdapter()->fetchPairs($select))) {
                $result = array();
            } 
            $this->_storeInformation = $result;
        }
        return $this->_storeInformation;
    }
    
    /**
     * Getting products data from osCommerce
     *
     */
    public function getProducts()
    {
        $code = $this->getWebsite()->getCode();
        $website = $code? $code: self::DEFAULT_WEBSITE_CODE;
        $connection = $this->_getForeignAdapter();
        $select =  "SELECT `p`.`products_id` `id`, `p`.`products_quantity` `qty` ";
        $select .= ", `p`.`products_model` `sku`, `p`.`products_price` `price`, `p`.`products_image` `image` ";
        $select .= ", `p`.`products_weight` `weight`, IF(`p`.`products_status`,'Enabled','Disabled') `status` ";
        $select .= ", IF(`p`.`products_status`,'1','0') `is_in_stock`";
        $select .= ", `pd`.`products_name` `name`, `pd`.`products_description` `description` ";
        $select .= ", `tc`.`tax_class_title` `tax_class_id`, IF(1,'".self::DEFAULT_VISIBILITY."','') `visibility` ";
        $select .= ", IF(1,'".self::DEFAULT_ATTRIBUTE_SET."','') `attribute_set` ";
        $select .= ", IF(1,'".self::DEFAULT_PRODUCT_TYPE ."','') `type` ";
        $select .= ", IF(1,'".self::DEFAULT_STORE."','') `store` ";
        $select .= ", IF(1,'".$website."','') `website` ";
        $select .= "FROM `{$this->_prefix}products` p LEFT JOIN `{$this->_prefix}products_description` pd ";
        $select .= "ON `pd`.`products_id`=`p`.`products_id` AND `pd`.`language_id`=1 ";
        $select .= "LEFT JOIN `{$this->_prefix}tax_class` tc ON `tc`.`tax_class_id`=`p`.`products_tax_class_id` ";
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }

    /**
     * Getting product description for different stores
     *
     * @param integer $productId
     * @return mix array/boolean
     */
    public function getProductStores($productId) {
        $select =  "SELECT `language_id` `store`, `products_name` `name`, `products_description` `description`";
        $select .= " FROM `{$this->_prefix}products_description` ";
        $select .= " WHERE `products_id`={$productId}";
        if (!$results = $this->_getForeignAdapter()->fetchAll($select)) {
            $results = array();
        }
        return $results;
    }

    /**
     * Getting new created categories recursively using products of osCommerce
     *
     * @param integer $productId
     * @return string
     */
    public function getProductCategories($productId)
    {
        $select = "SELECT `categories_id` `id`, `categories_id` `value` FROM `{$this->_prefix}products_to_categories` WHERE products_id={$productId}";
        if (isset($productId) && $results = $this->_getForeignAdapter()->fetchPairs($select)) {
            $categories = join(',', array_values($results));

            //$this->_getReadAdapter();
            $importId = Mage::registry('current_convert_osc')->getId();
            $typeId = $this->getImportTypeIdByCode('category');
            $select = $this->_getReadAdapter()->select();
            $select->from(array('osc'=>$this->getTable('oscommerce_ref')), array('id'=>'id','ref_id'=>'ref_id'));
            $select->where("`osc`.`import_id`='{$importId}' AND `osc`.`type_id`='{$typeId}' AND `osc`.`value` in (".$categories.")");
            $result = $this->_getReadAdapter()->fetchPairs($select);
            if ($result) {
                return join(',',array_values($result));
            }
        } 
        return false;
    }

    /**
     * Getting categories recursively of osCommerce
     *
     * @param integer $parentId
     * @return array
     */
    public function getCategories($parentId = '0') {
        $select = "SELECT `c`.`categories_id` as `id`, `c`.`parent_id`, `cd`.`categories_name` `name` FROM `{$this->_prefix}categories` c";
        $select .= " INNER JOIN `{$this->_prefix}categories_description` cd on `cd`.`categories_id`=`c`.`categories_id`";
        $select .= " AND `cd`.`language_id`=1 WHERE `c`.`parent_id`={$parentId}";
        if (!$results = $this->_getForeignAdapter()->fetchAll($select)) {
            $results = array();
        } else {
            $stores = $this->getLanguagesToStores();
            foreach($results as $index => $result) {
                if ($categoriesToStores = $this->getCagetoriesToStores($result['id'])) {
                    foreach($categoriesToStores as $store => $categoriesName) {
                        $results[$index]['stores'][$stores[$store]] = $categoriesName;
                    }
                }

                $sub = $this->getCategories($result['id']);
                if ($sub) {
                    $results[$index]['children'] = $sub;
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
        $typeId = $this->getImportTypeIdByCode('store');
        $importId = Mage::registry('current_convert_osc')->getId();
        if (!$this->_languagesToStores) {
            $this->_languagesToStores[1] = 1;
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
    public function getCagetoriesToStores($categoryId)
    {
        $select = "SELECT `language_id`, `categories_name` FROM `{$this->_prefix}categories_description`";
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
    public function getStores()
    {
        $select = "SELECT `languages_id` `id`, `name`,  `code` `scode`, ";
        $select .= " `directory` `code`, 1 `is_active` FROM `{$this->_prefix}languages`";
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }


    /**
     * Getting customers from osCommerce
     *
     * @return array
     */
    public function getCustomers()
    {
        $select = "SELECT `customers_id` `id`, `customers_firstname` `firstname`";
        $select .= ", `customers_lastname` `lastname`, `customers_email_address` `email`";
        $select .= ", `customers_telephone` `telephone`";
        $select .= ", `customers_password` `password_hash`, `customers_newsletter` `is_subscribed`";
        $select .= ", `customers_default_address_id` `default_billing` FROM `{$this->_prefix}customers`";
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
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
     * Enter description here...
     *
     * @return uarray
     */
    public function getOrders()
    {
        $select = "SELECT * FROM `{$this->_prefix}orders` ORDER BY `orders_id`";
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        } 
        return $result;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $orderId
     * @return unknown
     */
    public function getOrdersProducts($orderId) {
        $select = "SELECT * FROM `{$this->_prefix}orders_products` WHERE `orders_id`='{$orderId}'";
        if (!isset($orderId) || !($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $orderId
     */
    public function getOrdersTotal($orderId)
    {
        $select = "SELECT * FROM `{$this->_prefix}orders_total` WHERE `orders_id`='{$orderId}'";
        if (!isset($orderId) || !($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
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
        $select .= ", `entry_postcode` `postcode`, `entry_city` `city`";
        $select .= ", `entry_state` `region`, `entry_country_id` `country_id`";
        $select .= ", `entry_zone_id` `region_id` FROM `{$this->_prefix}address_book` WHERE customers_id={$customerId}";
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
        $select .= ", `entry_zone_id` `region_id` FROM `{$this->_prefix}address_book` WHERE address_book_id={$address_id}";
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
            $select = "SELECT * FROM `{$this->_prefix}countries`";
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
        if (isset($code)) foreach($this->_countryToCode as $id => $code) {
            if ($code == $countryCode) {
                return $id;
            }
        }
        return false;
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
            $websiteId = strlen($websiteId) == 0 ? $this->getWebsite()->getId(): $websiteId;
            $this->_getWriteAdapter()->beginTransaction();
            $data = array('product_id'=>$productId, 'website_id' => $websiteId);
            try {
                $this->_getWriteAdapter()->insert($this->getTable('catalog_product_website'), $data);
                $this->_getWriteAdapter()->commit();
            } catch (Exception $e) {
                //$this->_getWriteAdapter()->rollBack();
            }
        }
    }

    /**
     * Getting regions from osCommerce
     *
     * @return array
     */
    public function getRegions()
    {
        if (!$this->_regionCode) {
            $select = "SELECT `zone_id`, `zone_name` FROM `{$this->_prefix}zones`";
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
            $this->_rootCategory = $this->getCache('Object_Cache_Category')->load(self::DFFAULT_PARENT_CATEGORY_ID);
        }
        return $this->_rootCategory;    
    }
    
    public function setWebsiteId($id)
    {
        if (isset($id) && is_integer($id)) $this->_websiteId = $id;
    }
    
    public function getWebsiteId()
    {
        if ($this->_website) {
            return $this->_website->getId();
        } else {
            return self::DEFAULT_WEBSITE_ID;
        }
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
                } catch (Exception $e) {
                    //echo $e->getMessage();
                }
            }
        }
    }
    
    public function getTaxClasses()
    {
        $select = "SELECT  `tax_class_id` `id`, `tax_class_title` `title` FROM `{$this->_prefix}tax_class`";
        if (!($results = $this->_getForeignAdapter()->fetchPairs($select))) {
            $results = array();
        }
        return $results;
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
    
    private function _format($str)
    {
    	$str = preg_replace('#[^0-9a-z\/\.]+#i', '', $str);
    	$str = strtolower(str_replace('\\s','',$str));
    	return $str;
    }    
    
    public function getResultStatistic() 
    {
        return $this->_resultStatistic;
    }
    public function setResultStatistic(array $statistic) 
    {
        if (is_array($statistic)) $this->_resultStatistic = $statistic;
    }
    
    public function setPrefixPath($prefix) {
        if ($prefix) {
            $this->_prefixPath = $prefix;
        }
    }
}
