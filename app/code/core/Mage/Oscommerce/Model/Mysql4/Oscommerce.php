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
    const DEFAULT_DISPLAY_MODE	= 'PRODUCTS';
    const DEFAULT_IS_ANCHOR		= '0';
    const DEFAULT_STORE		    = 'Default';
    const DEFAULT_PRODUCT_TYPE	= 'Simple';
    const DEFAULT_ATTRIBUTE_SET = 'Default';
    const DEFAULT_VISIBILITY	= 'Catalog, Search';
    const DEFAULT_LOCALE        = 'en_US';
    const DEFAULT_MAGENTO_CHARSET = 'UTF-8';
    const DEFAULT_OSC_CHARSET   = 'ISO-8859-1';

    protected $_currentWebsiteId;

    protected $_importType 	            = array();
    protected $_countryIdToCode         = array();
    protected $_countryNameToCode       = array();
    protected $_regionCode              = array();
    protected $_logData                 = array();
    protected $_languagesToStores       = array();
    protected $_prefix                  = '';
    protected $_storeLocales            = array();
    protected $_rootCategory            = '';
    protected $_website              = '';
    protected $_tableOrders             = '';

    protected $_websiteCode             = '';
    protected $_isProductWithCategories = false;
    protected $_setupConnection ;
    protected $_resultStatistic         = array();
    protected $_customerIdPair          = array();
    protected $_prefixPath               = '';
    protected $_stores                  = array();
    protected $_productsToCategories    = array();
    protected $_productsToStores        = array();
    protected $_tableCharset            = array();

    protected $_oscDefaultLanguage;
    protected $_oscStoreInformation;

    protected $_categoryModel;
    protected $_customerModel;
    protected $_productModel;
    protected $_orderModel;
    protected $_addressModel;
    protected $_websiteModel;
    protected $_storeGroupModel;
    protected $_configModel;
    protected $_customerGroupModel;
    protected $_storeModel;

    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce', 'import_id');

        $this->_logData['created_at'] = $this->formatDate(time());

        $this->_setupConnection = Mage::getSingleton('core/resource')->getConnection('oscommerce_setup');
        $this->_resultStatistic = array(
            'products'=>array('total' => 0, 'imported' =>0),
            'categories' =>array('total' => 0, 'imported' =>0),
            'customers' =>array('total' => 0, 'imported' =>0));
        $this->_currentWebsiteId = Mage::app()->getWebsite()->getId();
    }

    public function getCharset($type)
    {
        $tables = array('products', 'customers', 'categories', 'orders', 'languages');
        if (!$this->_tableCharset) foreach($tables as $table) {
            $tableName = "{$this->_prefix}$table";
            if ($results = $this->_getForeignAdapter()->fetchAll("show create table `{$tableName}`")) {
                foreach ($results as $result) {
                    if($result['Create Table']) {
                        $lines  = explode("\n",$result['Create Table']);
                        $i = 0;
                        foreach ($lines as $line) {
                            if ($i == sizeof($lines) - 1) {
                                if (preg_match_all('/CHARSET=(\w+)/', $line, $matches)) {
                                    if (isset($matches[1][0]))
                                    {
                                        $this->_tableCharset[$tableName] = $matches[1][0];
                                    }
                                }
                            }
                            $i++;
                        }
                    }

                }
                if (!isset($this->_tableCharset[$tableName])) {
                    $this->_tableCharset[$tableName] = self::DEFAULT_OSC_CHARSET;
                }
            }
        }
        if (isset($type) && isset($this->_tableCharset[$type])) {
            return $this->_tableCharset[$type];
        }
        return false;
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
     *
     */
    //public function createWebsite($obj, $isCreate = true)
    public function createWebsite($obj, $id = '')
    {
        $websiteModel  = $this->getWebsiteModel();
        if (isset($id)) {
            $websiteModel->load($id);
        }
        if (!$websiteModel->getId()) {
        //if ($isCreate) {
            $storeInfo = $this->getOscStoreInformation();
            if ($this->_websiteCode && !($websiteModel->load($this->_websiteCode)->getId())) {
                $websiteModel->setName($storeInfo['STORE_NAME']);
                $websiteModel->setCode($this->_websiteCode ? $this->_websiteCode : $this->_format($storeInfo['STORE_NAME']));
                $websiteModel->save();
            }
            $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
            $this->_logData['import_id']  = $obj->getId();
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
        $this->createStoreGroup($obj);
    }

    public function createStoreGroup($obj)
    {
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

//        $storeGroupModel->setData($data);
//
//
//        $data['website_id'] = $websiteModel->getId();
//        $data['name'] = ($websiteModel->getId()==$this->_currentWebsiteId ? $storeInfo['STORE_NAME']: $websiteModel->getName()). ' Store';
//        $data['default_category_id'] = 0;
//        //$data['root_category_id'] = $this->getRootCategory()->getId();
//        $storeGroupModel = $this->getStoreGroupModel();
//        $storeGroupModel->unsetData();
//        $storeGroupModel->setOrigData();
//        $storeGroupModel->setData($data);
//        try {
//        $storeGroupModel->save();
//
//        $websiteModel->setDefaultGroupId($storeGroupModel->getId());
//        $websiteModel->save();
//        } catch (Exception $e) {
//            echo $e;
//        }

        $this->_logData['user_id']      = Mage::getSingleton('admin/session')->getUser()->getId();
        $this->_logData['import_id']    = $obj->getId();
        $this->_logData['type_id']      = $this->getImportTypeIdByCode('group');
        $this->_logData['ref_id']       = $storeGroupModel->getId();
        $this->_logData['created_at']   = $this->formatDate(time());

        $this->log($this->_logData);

        return $this;
    }

    public function createRootCategory()
    {
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
        }
        catch (Exception $e) {
            // you catch
        }

        $this->setRootCategory(clone $categoryModel);

        return $this;

        /* ############################# COMMENT ############################ */
        // Where log ? :)
        /* ################## DONT USE THIS CODE ############################ */

        $data = array();
        $data['store_id'] = 1; // default view;
        $data['is_active'] = 1;
        $data['display_mode'] = self::DEFAULT_DISPLAY_MODE;
        $data['is_anchor']	= self::DEFAULT_IS_ANCHOR;
        $data['description'] = $data['name'] = ($websiteModel->getId() == $this->_currentWebsiteId ? $storeInfo['STORE_NAME']:$websiteModel->getName()). " Category";
        $categoryModel->setParentId(1);
        //$categoryModel->setStoreId(1); //
        $categoryModel->setData($data);
        $categoryModel->save();
        $newParentId = $categoryModel->getId();
        $categoryModel->setPath('1/'.$newParentId);
        $categoryModel->save();
        $this->getStoreGroupModel()->setRootCategoryId($categoryModel->getId())->save();
        $this->setRootCategory(clone $categoryModel);
    }

    /**
     * Importing store data from osCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     */
    public function importStores(Mage_Oscommerce_Model_Oscommerce $obj)
    {
        //    	$groupmodel = mage::getmodel('core/store_group')->load(self::DEFAULT_WEBSITE_GROUP);
        $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
        $this->_logData['import_id'] = $obj->getId();
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
        if ($stores = $this->getStores()) foreach($stores as $store) {
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
                    $store['code'] = $store['code'].'_'.$websiteId;
                    $locales[$store['code']] = $localeCode;
                }

                //$store['code'] = $this->getWebsite()->getCode().$store['code'];
                $store['name'] = iconv($this->getCharset('languages'), self::DEFAULT_MAGENTO_CHARSET, $store['name']);
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
    public function importCustomers(Mage_Oscommerce_Model_Oscommerce $obj)
    {
        $totalCustomers = $this->getTotalCustomers();
        $this->_resultStatistic['customers']['total'] = $totalCustomers;

        // Getting customer group data
        $customerGroupId = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
        $customerGroupModel = $this->getCustomerGroupModel()->load($customerGroupId);
        // End getting customer group data


        $this->_logData['user_id'] = Mage::getSingleton('admin/session')->getUser()->getId();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('customer');
        $this->_logData['import_id'] = $obj->getId();
        $websiteCode = $this->getWebsiteModel()->getCode();
        $customerModel = $this->getCustomerModel();
        $addressModel = $this->getAddressModel();

        $max = 100;
        $page = (int) $totalCustomers/$max;
        if ($totalCustomers <= $max)  {
            if ($customers = $this->getCustomers())	{
                foreach ($customers as $customer) {
                    $customerAddresses = array();
                    $this->_logData['value'] = $oscCustomerId = $customer['id'];

                    $customer['group_id'] = $customerGroupModel->getName();
                    $addresses = $this->getAddresses($customer['id']);
                    if ($addresses) {
                        foreach ($addresses as $address) {
                            //if ($address['id'] != $customer['default_billing']) {
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
                                        //$value = $this->getRegionCode($value);
                                        //unset($address['country_id']);
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
                            //}
                        }
                    }
                    $defaultBilling = '';
                    $defaultBilling = $customer['default_billing'];
                    unset($customer['default_billing']);

                    $customer['website'] = $websiteCode ? $websiteCode: self::DEFAULT_WEBSITE_CODE;
                    unset($customer['id']);

                    try {
                        $customerModel->unsetData();
                        $customerModel->setOrigData();
                        $customerModel->setData($customer);
                        $customerModel->save();
                        $customerId = $customerModel->getId();

                        if ($customerAddresses) foreach ($customerAddresses as $customerAddress) {
                            $customerAddress['telephone'] = $customer['telephone'];
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
                        $this->_resultStatistic['customers']['imported']++;

                    } catch (Exception $e) {
                        echo $e->getMessage()."<br/>";
                    }
                }
            }
        } else {
            for ($i = 1; $i <= $page; $i++) {
                if ($customers = $this->getCustomers(array('from'=>($i >1?$i*$max:$i),'max'=>$max))) {
                    foreach ($customers as $customer) {
                        $customerAddresses = array();
                        $this->_logData['value'] = $oscCustomerId = $customer['id'];

                        $customer['group_id'] = $customerGroupModel->getName();
                        $addresses = $this->getAddresses($customer['id']);
                        if ($addresses) {
                            foreach ($addresses as $address) {
                                //if ($address['id'] != $customer['default_billing']) {
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
                                            //$value = $this->getRegionCode($value);
                                            //unset($address['country_id']);
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
                                //}
                            }
                        }
                        $defaultBilling = '';
                        $defaultBilling = $customer['default_billing'];
                        unset($customer['default_billing']);

                        $customer['website'] = $websiteCode ? $websiteCode: self::DEFAULT_WEBSITE_CODE;
                        unset($customer['id']);

                        try {
                            $customerModel->unsetData();
                            $customerModel->setOrigData();
                            $customerModel->setData($customer);
                            $customerModel->save();
                            $customerId = $customerModel->getId();

                            if ($customerAddresses) foreach ($customerAddresses as $customerAddress) {
                                $customerAddress['telephone'] = $customer['telephone'];
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
                            $this->_resultStatistic['customers']['imported']++;

                        } catch (Exception $e) {
                            echo $e->getMessage()."<br/>";
                        }
                    }
                }
            }
        }
   }

    /**
     * Importing categories recursively from osCommerce to Magento
     *
     * @param Mage_Oscommerce_Model_Oscommerce $obj
     * @param array $children
     * @param string $parentId
     */
    public function importCategories(Mage_Oscommerce_Model_Oscommerce $obj, $parentId = null, $parentPath = null, &$children = null)
    {
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('category');
        $this->_logData['import_id'] = $obj->getId();
        $rootCategory = $this->getRootCategory();
        $parentPath = !is_null($parentPath) ? $parentPath: $rootCategory->getPath();
        $parentId = !is_null($parentId) ? $parentId: $rootCategory->getId();
        if (!is_null($children)) {
            $categories = $children;
        } else {
            $categories = $this->getCategories();
        }

        // Getting cagetory object from cache
        $categoryModel = $this->getCategoryModel();

        $categoryModel->unsetData();
        $categoryModel->setOrigData();

        if ($categories) foreach($categories as $category) {
            $data = $category;
            if (isset($data['children'])) {
                unset($data['children']);
            }

            $this->_resultStatistic['categories']['total']++;
            // Setting and saving category
            $this->_logData['value'] = $data['id'];

            try {
                unset($data['stores']);
                unset($data['id']);
                $data['store_id'] = 0;
                $data['is_active'] = 1;
                $data['display_mode'] = self::DEFAULT_DISPLAY_MODE;
                $data['is_anchor']	= self::DEFAULT_IS_ANCHOR;
                $data['parent_id'] = $parentId;
                $data['attribute_set_id'] = $categoryModel->getDefaultAttributeSetId();
                $data['path'] = $parentPath;
                $data['name'] = iconv($this->getCharset('categories'), self::DEFAULT_MAGENTO_CHARSET, $data['name']);
                $data['description'] = $data['meta_title'] = $data['name'];
                
                $categoryModel->setData($data);
                $categoryModel->save();
                $newParentId = $categoryModel->getId();
                $newParentPath = $categoryModel->getPath();
                $this->_logData['ref_id'] = $newParentId;
                $this->_logData['created_at'] = $this->formatDate(time());
                $this->log($this->_logData);

                //$category['stores'][1] = array('name' => $data['description']);

                // saving data for different
                if (isset($category['stores'])) foreach($category['stores'] as $storeId=>$catData) {
                    if ($categoryModel->getStoreId() != $storeId) {
                        $categoryModel->setStoreId($storeId)->setName($catData['name'])->setDescription($catData['name'])
                            ->setMultistoreSaveFlag(true)
                            ->save();
                    }
                }

                $this->_resultStatistic['categories']['imported'] += 1;
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            // End setting and saving category

            // Checking child category recursively
            if (isset($category['children'])) {
                $this->importCategories($obj, $newParentId, $newParentPath, $category['children']);
            }
        }
    }

    public function importProducts(Mage_Oscommerce_Model_Oscommerce $obj)
    {
        $this->_logData['import_id'] = Mage::registry('current_convert_osc')->getId();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('product');
        $productAdapterModel = Mage::getModel('catalog/convert_adapter_product');
        $productModel = $this->getProductModel();

        $mageStores = $this->getLanguagesToStores();
        $count = 0;
        $max = 100;
        $totalProducts = $this->getTotalProducts();

        $page = (int) $totalProducts/$max;
        if ($totalProducts <= 100)  {
            if ($products = $this->getProducts()) foreach ($products as $data) {
                $data['name'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $data['name']);
                $data['description'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $data['description']);
                $this->_logData['value'] = $oscProductId = $data['id'];
                $this->_resultStatistic['products']['total']++;
                unset($data['id']);
                if ($this->_isProductWithCategories) {
                    if ($categories = $this->getProductCategories($oscProductId))
                        $data['category_ids'] = $categories;
                }
                try {

                    // Check existing file or not on the filesystem
                    // MAGENTO_INSTALLATION_ROOT/media/import
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


                    if ($stores = $this->getProductStores($oscProductId)) foreach ($stores as $storeId => $store)
                    {
                        //$productAdapterModel->getProductModel()->unsetData();
                        //$productAdapterModel->getProductModel()->setOrigData();
                        if (!$storeCode = $this->getStoreCodeById($mageStores[$storeId])) {
                            $storeCode = self::DEFAULT_STORE ;
                        }
                        $data['store'] = $storeCode;
                        $data['name'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $store['name']);
                        $data['description'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $store['description']);
                        $productAdapterModel->saveRow($data);
                    }
                    $productId = $productAdapterModel->getProductModel()->getId();
                    $this->_logData['ref_id'] = $productId;
                    $this->_logData['created_at'] = $this->formatDate(time());
                    $this->log($this->_logData);
                    $this->_resultStatistic['products'] ['imported']++;
                } catch (Exception $e) {
                    //echo $e->getMessage()."<br/>";
                }
            }
        } else {
            for ($i = 1; $i <= $page; $i++) {
                if ($products = $this->getProducts(array('from'=>($i >1?($i-1)*$max:$i),'max'=>$max))) {
                    foreach ($products as $data) {
                        $data['name'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $data['name']);
                        $data['description'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $data['description']);
                        $this->_logData['value'] = $oscProductId = $data['id'];
                        $this->_resultStatistic['products']['total']++;
                        unset($data['id']);
                        if ($this->_isProductWithCategories) {
                            if ($categories = $this->getProductCategories($oscProductId))
                                $data['category_ids'] = $categories;
                        }
                        try {

                            // Check existing file or not on the filesystem
                            // MAGENTO_INSTALLATION_ROOT/media/import
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

                            $storeCount = 0;
                            //$productModel->unsetData();
                            //$productModel->setOrigData();
                            if ($stores = $this->getProductStores($oscProductId)) foreach ($stores as $storeId => $store)
                            {
                                //$productAdapterModel->getProductModel()->unsetData();
                                //$productAdapterModel->getProductModel()->setOrigData();
                                if (!$storeCode = $this->getStoreCodeById($mageStores[$storeId])) {
                                    $storeCode = self::DEFAULT_STORE ;
                                }

                                $data['store'] = $storeCode;
                                $data['name'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $store['name']);
                                $data['description'] = iconv($this->getCharset('products'), self::DEFAULT_MAGENTO_CHARSET, $store['description']);
                                $productAdapterModel->saveRow($data);
                            }
                            $productId = $productAdapterModel->getProductModel()->getId();

                            $this->_logData['ref_id'] = $productId;
                            $this->_logData['created_at'] = $this->formatDate(time());
                            $this->log($this->_logData);
                            $this->_resultStatistic['products'] ['imported']++;
                        } catch (Exception $e) {
                            echo $e->getMessage()."<br/>";
                        }

                    }
                }
           }
        }
    }

    public function importOrders($obj)
    {
        $customerIdPair = $this->_customerIdPair;
        $importId  = $obj->getId();
        $websiteId = $this->getWebsiteModel()->getId();
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
              `orders_status` varchar(32) default NOT NULL,
              `orders_date_finished` datetime default NULL,
              `currency` char(3) default NULL,
              `currency_value` decimal(14,6) default NULL,
              `currency_symbol` char(3) default NULL,
              `orders_total`  decimal(14,6) default NULL,
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
              `orders_status` varchar(32) default NULL,
              PRIMARY KEY  (`orders_status_history_id`),
              KEY `idx_orders_status_history_osc_magento_id` (`osc_magento_id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;"

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

        // Get orders
        $total =  $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->_prefix}orders`");
        $max = 100;
        $page = (int) $total/$max;
        $orderCount = 0;
        if ($total <= 100)  {
            //$results = $this->_getForeignAdapter()->fetchAll("SELECT `o`.*, `c`.`symbol_left` `currency_symbol` FROM `{$this->_prefix}orders` `o` LEFT JOIN `{$this->_prefix}currencies` `c` ON `c`.`code`=`o`.`currency`");
            $select  = "SELECT `o`.*, `c`.`symbol_left` `currency_symbol`,`ot`.`value` `orders_total`,";
            $select .= " `os`.`orders_status_name`  FROM `{$this->_prefix}orders` `o`";
            $select .= " LEFT JOIN `{$this->_prefix}currencies` `c` ON `c`.`code`=`o`.`currency` ";
            $select .= " LEFT JOIN `{$this->_prefix}orders_total` `ot` ON `ot`.`orders_id`=`o`.`orders_id` ";
            $select .= " AND `ot`.`class`='ot_total' ";
            $select .= " LEFT JOIN `{$this->_prefix}orders_status` os ON `os`.`orders_status_id`=`o`.`orders_status`";
            $select .= "AND `os`.`language_id`=1";

            $results = $this->_getForeignAdapter()->fetchAll($select);

            if ($results) foreach ($results as $result) {
                if (isset($this->_customerIdPair[$result['customers_id']])) {
                    $result['magento_customers_id'] = $this->_customerIdPair[$result['customers_id']]; // get Magento CustomerId
                    $result['import_id'] = $importId;
                    $result['website_id'] = $websiteId;
                    $result['orders_status'] = $result['orders_status_name'];
                    unset($result['orders_status_name']);
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
                            $orderHistory['orders_status']  = $result['orders_status'];
                            $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_status_history", $orderHistory);
                        }
                    }
                }
            }
        } else {
            for ($i = 1; $i <= $page; $i++) {

                $select  = "SELECT `o`.*, `c`.`symbol_left` `currency_symbol`,`ot`.`value` `orders_total`,";
                $select .= " `os`.`orders_status_name`  FROM `{$this->_prefix}orders` `o`";
                $select .= " LEFT JOIN `{$this->_prefix}currencies` `c` ON `c`.`code`=`o`.`currency` ";
                $select .= " LEFT JOIN `{$this->_prefix}orders_total` `ot` ON `ot`.`orders_id`=`o`.`orders_id` ";
                $select .= " AND `ot`.`class`='ot_total'";
                $select .= " LEFT JOIN `{$this->_prefix}orders_status` os ON `os`.`orders_status_id`=`o`.`orders_status` ";
                $select .= " AND `os`.`language_id`=1 ";
                $select .= "  LIMIT ".($i>1?($i*$max+1):$i).", $max";

                $results = $this->_getForeignAdapter()->fetchAll($select);
                $this->_getWriteAdapter()->beginTransaction();
                if ($results) foreach ($results as $result) {

                    $result['magento_customers_id'] = $customerIdPair[$result['customers_id']]; // get Magento CustomerId
                    $result['import_id'] = $importId;
                    $result['website_id'] = $websiteId;
                    $result['orders_status'] = $result['orders_status_name'];
                    unset($result['orders_status_name']);
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
                            $orderHistory['orders_status'] = $result['orders_status'];
                            $this->_getWriteAdapter()->insert("{$tablePrefix}oscommerce_orders_status_history", $orderHistory);
                        }

                    }
                }
            }
        }
        $this->_resultStatistic['orders']['total'] = $total;
        $this->_resultStatistic['orders']['imported'] = $orderCount;
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
            $select =  "SELECT `configuration_key` `key`, `configuration_value` `value` FROM `{$this->_prefix}configuration`";
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
        $code = $this->getWebsiteModel()->getCode();
        $website = $code? $code: self::DEFAULT_WEBSITE_CODE;
        $connection = $this->_getForeignAdapter();
        $select =  "SELECT `p`.`products_id` `id`, `p`.`products_quantity` `qty` ";
        $select .= ", `p`.`products_model` `sku`, `p`.`products_price` `price`";
        $select .= ", `p`.`products_image` `image` ";
        $select .= ", `p`.`products_weight` `weight`, IF(`p`.`products_status`,'Enabled','Disabled') `status` ";
        $select .= ", IF(`p`.`products_status`,'1','0') `is_in_stock`";
        $select .= ", `pd`.`products_name` `name`, `pd`.`products_description` `description` ";
        $select .= ", `pd`.`products_description` `short_description` ";
        $select .= ", `tc`.`tax_class_title` `tax_class_id`, IF(1,'".self::DEFAULT_VISIBILITY."','') `visibility` ";
        $select .= ", IF(1,'".self::DEFAULT_ATTRIBUTE_SET."','') `attribute_set` ";
        $select .= ", IF(1,'".self::DEFAULT_PRODUCT_TYPE ."','') `type` ";
        //$select .= ", IF(1,'".self::DEFAULT_STORE."','') `store` ";
        $select .= ", IF(1,'".$website."','') `website` ";
        $select .= "FROM `{$this->_prefix}products` p LEFT JOIN `{$this->_prefix}products_description` pd ";
        $select .= "ON `pd`.`products_id`=`p`.`products_id` AND `pd`.`language_id`=1 ";
        $select .= "LEFT JOIN `{$this->_prefix}tax_class` tc ON `tc`.`tax_class_id`=`p`.`products_tax_class_id` ";
        if ($limit && isset($limit['from']) && isset($limit['max'])) {
            $select .= " LIMIT {$limit['from']}, {$limit['max']}";
        }
        //echo $select."<Br/>";
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }

    public function getTotalProducts()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->_prefix}products`");
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
            $select .= " FROM `{$this->_prefix}products_description` ";
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
//        $select =  "SELECT `language_id` `store`, `products_name` `name`, `products_description` `description`";
//        $select .= " FROM `{$this->_prefix}products_description` ";
//        $select .= " WHERE `products_id`={$productId}";
//        if (!$results = $this->_getForeignAdapter()->fetchAll($select)) {
//            $results = array();
//        }
//        return $results;

//        $select =  "SELECT `language_id` `store`, `products_name` `name`, `products_description` `description`";
//        $select .= " FROM `{$this->_prefix}products_description` ";
//        $select .= " WHERE `products_id`={$productId}";
//        if (!$results = $this->_getForeignAdapter()->fetchAll($select)) {
//            $results = array();
//        }
//        return $results;
    }

    /**
     * Getting new created categories recursively using products of osCommerce
     *
     * @param integer $productId
     * @return string
     */
    public function getProductCategories($productId)
    {
        if (!$this->_productsToCategories) {
            $select = "SELECT `products_id`, `categories_id` FROM `{$this->_prefix}products_to_categories`";

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
                $importId = Mage::registry('current_convert_osc')->getId();
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
//        $select = "SELECT `categories_id` `id`, `categories_id` `value` FROM `{$this->_prefix}products_to_categories` WHERE products_id={$productId}";
//
//        if (isset($productId) && $results = $this->_getForeignAdapter()->fetchPairs($select)) {
//
//            $categories = join(',', array_values($results));
//
//            //$this->_getReadAdapter();
//            $importId = Mage::registry('current_convert_osc')->getId();
//            $typeId = $this->getImportTypeIdByCode('category');
//            $select = $this->_getReadAdapter()->select();
//            $select->from(array('osc'=>$this->getTable('oscommerce_ref')), array('id'=>'id','ref_id'=>'ref_id'));
//            $select->where("`osc`.`import_id`='{$importId}' AND `osc`.`type_id`='{$typeId}' AND `osc`.`value` in (".$categories.")");
//            $result = $this->_getReadAdapter()->fetchPairs($select);
//            if ($result) {
//                return join(',',array_values($result));
//            }
//        }
//        return false;
    }

    /**
     * Getting categories recursively of osCommerce
     *
     * @param integer $parentId
     * @return array
     */
    public function getCategories($parentId = '0') {
        $defaultLanguage = $this->getOscDefaultLanguage();
        $defaultLanguageId = $defaultLanguage['id'];
        $select = "SELECT `c`.`categories_id` as `id`, `c`.`parent_id`, `cd`.`categories_name` `name` FROM `{$this->_prefix}categories` c ";// WHERE `c`.`parent_id`={$parentId}";
        $select .= " INNER JOIN `{$this->_prefix}categories_description` cd on `cd`.`categories_id`=`c`.`categories_id`";
        $select .= " AND `cd`.`language_id`={$defaultLanguageId} WHERE `c`.`parent_id`={$parentId}";
        if (!$results = $this->_getForeignAdapter()->fetchAll($select)) {
            $results = array();
        } else {
            $stores = $this->getLanguagesToStores();
            foreach($results as $index => $result) {
                if ($categoriesToStores = $this->getCategoriesToStores($result['id'])) {
                    foreach($categoriesToStores as $store => $categoriesName) {
                        $results[$index]['stores'][$stores[$store]] = array(
                            'name'=>iconv($this->getCharset('categories'), self::DEFAULT_MAGENTO_CHARSET, $categoriesName)
                        );
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
        if (!$this->_stores) {
            $select = "SELECT `languages_id` `id`, `name`,  `code` `scode`, ";
            $select .= " `directory` `code`, 1 `is_active` FROM `{$this->_prefix}languages`";
            $this->_stores =  $this->_getForeignAdapter()->fetchAll($select);
        }
        return $this->_stores;
    }


    public function getOscDefaultLanguage()
    {
        if (!$this->_oscDefaultLanguage) {
            $oscStoreInfo = $this->getOscStoreInformation();
            $languageCode = $oscStoreInfo['DEFAULT_LANGUAGE'];
            if ($stores = $this->getStores()) foreach($stores as $store) {
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
        $select = "SELECT `customers_id` `id`, `customers_firstname` `firstname` ";
        $select .= ", `customers_lastname` `lastname`, `customers_email_address` `email` ";
        $select .= ", `customers_telephone` `telephone` ";
        $select .= ", `customers_password` `password_hash`, `customers_newsletter` `is_subscribed` ";
        $select .= ", `customers_default_address_id` `default_billing` FROM `{$this->_prefix}customers` ";
        if ($limit && isset($limit['from']) && isset($limit['max'])) {
            $select .= " LIMIT {$limit['from']}, {$limit['max']}";
        }

        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }

        return $result;
    }

    public function getTotalCustomers() {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->_prefix}customers`");
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
        $select = "SELECT `o`.*, `ot`.`value` `orders_total` FROM `{$this->_prefix}orders` `o` ORDER BY `o`.orders_id` ";
        $select .= " LEFT JOIN `{$this->_prefix}orders_total` `ot` ON `ot`.`orders_id`=`o`.`orders_id` ";
        $select .= " AND `ot`.`class`='ot_total'";
        $select .= "LEFT JOIN `{$this->_prefix}orders_status` os ON `os`.`orders_status_id`=`o`.`orders_status`";

        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }

    /**
     * Get products from order
     *
     * @param integer $orderId
     * @return array
     */
    public function getOrdersProducts($orderId) {
        $select = "SELECT * FROM `{$this->_prefix}orders_products` WHERE `orders_id`='{$orderId}'";
        if (!isset($orderId) || !($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }

    /**
     * Get total from order total
     *
     * @param integer $orderId
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
            $websiteId = strlen($websiteId) == 0 ? $this->getWebsiteModel()->getId(): $websiteId;
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
            $this->_rootCategory = $this->getCategoryModel()->load(self::DFFAULT_PARENT_CATEGORY_ID);
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
        $select = "SELECT  `tax_class_id` `id`, `tax_class_title` `title` FROM `{$this->_prefix}tax_class`";
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
        $tablePrefix = (string)Mage::getConfig()->getNode('global/resources/db/table_prefix');
        $columnName = 'currency_symbol';
        try {
            if (!($result = $this->_getReadAdapter()->fetchRow("SHOW `columns` FROM {$tablePrefix}oscommerce_orders WHERE field='{$columnName}'"))) {

                    $this->_setupConnection()->query("ALTER TABLE {$tablePrefix}oscommerce_orders ADD {$columnName} char(3) DEFAULT NULL");
                    $this->_setupConnection()->commit();
            }
        } catch (Exception $e) {

        }
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
}
