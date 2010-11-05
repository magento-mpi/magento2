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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * osCommerce resource model
 *
 * @category    Mage
 * @package     Mage_Oscommerce
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Model_Resource_Oscommerce extends Mage_Core_Model_Resource_Db_Abstract
{
    const DEFAULT_DISPLAY_MODE    = 'PRODUCTS';
    const DEFAULT_IS_ANCHOR       = 0;
    const DEFAULT_STORE           = 'Default';
    const DEFAULT_PRODUCT_TYPE    = 'Simple';
    const DEFAULT_ATTRIBUTE_SET   = 'Default';
    const DEFAULT_VISIBILITY      = 'Catalog, Search';
    const DEFAULT_LOCALE          = 'en_US';
    const DEFAULT_MAGENTO_CHARSET = 'UTF-8';
    const DEFAULT_OSC_CHARSET     = 'ISO-8859-1';
    const DEFAULT_FIELD_CHARSET   = 'utf8';

    /**
     * Website
     *
     * @var Mage_Core_Model_Website
     */
    protected $_currentWebsite;

    /**
     * Website ID
     *
     * @var string
     */
    protected $_currentWebsiteId           = '';

    /**
     * Website code
     *
     * @var unknown
     */
    protected $_websiteCode                = '';

    /**
     * Import types
     *
     * @var array
     */
    protected $_importType                 = array();

    /**
     * List of country codes
     *
     * @var array
     */
    protected $_countryIdToCode            = array();

    /**
     * List of country names
     *
     * @var array
     */
    protected $_countryNameToCode          = array();

    /**
     * List of region codes
     *
     * @var array
     */
    protected $_regionCode                 = array();

    /**
     * Lo data
     *
     * @var array
     */
    protected $_logData                    = array();

    /**
     * Languages
     *
     * @var array
     */
    protected $_languagesToStores          = array();

    /**
     * Prefix for osCommerce table
     *
     * @var string
     */
    protected $_prefix                     = '';

    /**
     * Locales
     *
     * @var array
     */
    protected $_storeLocales               = array();

    /**
     * Root category
     *
     * @var string
     */
    protected $_rootCategory               = '';

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_isProductWithCategories    = false;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_setupConnection;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_customerIdPair             = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_categoryIdPair             = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_prefixPath                 = '';

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_stores                     = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_productsToCategories       = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_productsToStores           = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_connectionCharset;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_dataCharset;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_maxRows;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_oscStores;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_oscDefaultLanguage;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_oscStoreInformation;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_categoryModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_customerModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_productModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_productAdapterModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_orderModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_addressModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_websiteModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_storeGroupModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_configModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_customerGroupModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_storeModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_importCollection;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_saveRows                   = 0;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_errors                     = array();

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_importModel;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_lengthShortDescription;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_currentUserId;

    /**
     * Enter description here ...
     *
     * @var unknown
     */
    protected $_oscTables                  = array(
        'products', 'customers', 'categories', 'orders', 'languages',
        'orders_products', 'orders_status_history', 'orders_total',
        'products_description', 'address_book', 'categories_description'
    );

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce', 'import_id');
        $this->_setupConnection = Mage::getSingleton('core/resource')->getConnection('oscommerce_setup');
        $this->_currentWebsite = Mage::app()->getWebsite();
        $this->_currentWebsiteId = $this->_currentWebsite->getId();
        $this->_maxRows = Mage::getStoreConfig('oscommerce/import/max_rows');
        $this->_lengthShortDescription = Mage::getStoreConfig('oscommerce/import/short_description_length');
    }

    /**
     * Get oscommerce session namespace
     *
     * DON'T USE?
     *
     * @return Mage_Oscommerce_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('oscommerce/session');
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

    /**
     * Enter description here ...
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getCreatedAt()) {
            $object->setCreatedAt($this->formatDate(time()));
        }
        $object->setUpdatedAt($this->formatDate(time()));
        parent::_beforeSave($object);
    }

    /**
     * Getting external connection adapter
     *
     * @return object
     */
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
        if (!$this->_stores) {
            $stores = Mage::app()->getStores();
            foreach ($stores as $store) {
                $this->_stores[$store->getId()] = $store->getCode();
            }
        }
        if (isset($this->_stores[$id])) {
            return $this->_stores[$id];
        }
        return false;
    }

    /**
     * Set website code
     *
     * @param unknown_type $code
     */
    public function setWebsiteCode($code)
    {
        if (isset($code)) $this->_websiteCode = $code;
    }

    /**
     * Create new website or set current website as default website
     *
     * @param integer $websiteId
     */
    public function createWebsite($websiteId = null)
    {
        $importModel = $this->getImportModel();
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
              $this->saveLogs(array( 0 => $websiteModel->getId()), 'website');
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

    /**
     * Create store group
     *
     * @return Mage_Oscommerce_Model_Resource_Oscommerce
     */
    public function createStoreGroup()
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
            throw $e;
        }

        $this->saveLogs(array(0 => $storeGroupModel->getId(), 'group'));
        return $this;
    }

    /**
     * Create Root Category
     *
     * @return Mage_Oscommerce_Model_Resource_Oscommerce
     */
    public function createRootCategory()
    {
        $categoryModel = $this->getCategoryModel();
        $categoryModel->unsetData();
        $categoryModel->setOrigData();

        $websiteModel = $this->getWebsiteModel();
        if (!$websiteModel->getId()) {
            $websiteModel->load($this->_currentWebsiteId); // NEED TO GET DEFAULT WEBSITE ID FROM CONFIG
        }

        $categoryName = Mage::helper('oscommerce')->__('Root Category for %s', $websiteModel->getName());

        $categoryModel->setStoreId(0);
        $categoryModel->setIsActive(1);
        $categoryModel->setDisplayMode(self::DEFAULT_DISPLAY_MODE);
        $categoryModel->setName($categoryName);
        $categoryModel->setParentId(1);
        $categoryModel->setPath('1');

        try {
            $categoryModel->save();
            $this->saveLogs(array(0 => $categoryModel->getId()), 'root_category');
        }
        catch (Exception $e) {
            throw $e;
        }

        $this->setRootCategory($categoryModel);

        return $this;
    }

    /**
     * Importing store data from osCommerce to Magento
     *
     */
    public function importStores()
    {
        $locales = $this->getStoreLocales();
        $defaultStore = '';
        $storeInformation = $this->getOscStoreInformation();
        $defaultStoreCode = $storeInformation['DEFAULT_LANGUAGE'];
        $configModel = $this->getConfigModel();
        $storeModel = $this->getStoreModel();
        $storeGroupModel = $this->getStoreGroupModel();
        $websiteModel = $this->getWebsiteModel();
        $websiteId = $websiteModel->getId();
        $storePairs = array();
        if ($stores = $this->getOscStores()) {
            foreach ($stores as $store) {
                try {
                    if ($store['scode'] == $defaultStoreCode) {
                        $defaultStore = $storeModel->getId();
                    }
                    if ($defaultStore) {
                        $storeGroupModel->setDefaultStoreId($defaultStore);
                        $storeGroupModel->save();
                    }
                    $storeGroupId = $storeGroupModel->getId();
                    $oscStoreId = $store['id'];
                    unset($store['id']);
                    $store['group_id'] = $storeGroupId;
                    $store['website_id'] = $websiteId;
                    $storeModel->unsetData();
                    $storeModel->setOrigData();
                    $storeModel->load($store['code']);
                    if ($storeModel->getId() && $storeModel->getCode() == $store['code']) {
                        if (isset($locales[$store['code']])) {
                            $localeCode = $locales[$store['code']];
                            unset($locales[$store['code']]);
                        } else {
                            $localeCode = self::DEFAULT_LOCALE;
                        }
                        $store['code'] = $store['code'].'_'.$websiteId.time(); // for unique store code
                        $locales[$store['code']] = $localeCode;
                    }
                    $store['name'] = $this->convert($store['name']);
                    $storeModel->unsetData();
                    $storeModel->setOrigData();
                    $storeModel->setData($store);
                    $storeModel->save();

                    $storePairs[$oscStoreId]  = $storeModel->getId();

                    $storeLocale = isset($locales[$storeModel->getCode()])?$locales[$storeModel->getCode()]: $locales['default'];

                    $configModel->unsetData();
                    $configModel->setOrigData();
                    $configModel->setScope('stores')
                        ->setScopeId($storeModel->getId())
                        ->setPath('general/locale/code')
                        ->setValue($storeLocale)
                        ->save();
                    Mage::dispatchEvent('store_add', array('store'=>$storeModel));
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
        }
        if (sizeof($storePairs) > 0) {
            $this->saveLogs($storePairs, 'store');
        }
        $this->setStoreLocales($locales);

        Mage::app()->reinitStores();
        unset($stores);
    }

    /**
     * Importing customer/address from osCommerce to Magento
     *
     * @param unknown_type $startFrom
     * @param unknown_type $useStartFrom
     * @param unknown_type $sendSubscription
     */
    public function importCustomers($startFrom = 0, $useStartFrom = false, $sendSubscription = true)
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
                        $customer['sendSubscription'] = $sendSubscription;
                        $this->_saveCustomer($customer);
                    }
                }
            }
        } else {
            if ($customers = $this->getCustomers(array('from'=> $startFrom ,'max'=>$maxRows))) {
                foreach ($customers as $customer) {
                    $customer['sendSubscription'] = $sendSubscription;
                    $this->_saveCustomer($customer);
                }
            }
        }
    }

    /**
     * Save customer data
     *
     * @param array $data
     */
    protected function _saveCustomer($data = null)
    {
        $addressFieldMapping = array(
            'street' => 'entry_street_address',
            'firstname' => 'entry_firstname',
            'lastname'    => 'entry_lastname',
            'city'        => 'entry_city',
            'region'    => 'entry_state'
        );
        $importModel = $this->getImportModel();
        $timezone = $importModel->getTimezone();
        if (!is_null($data)) {
            $customerAddresses = array();
            // Getting customer group data
            $customerGroupId = Mage::getStoreConfig(Mage_Customer_Model_Group::XML_PATH_DEFAULT_ID);
            $customerGroupModel = $this->getCustomerGroupModel()->load($customerGroupId);
            $websiteId = $this->getWebsiteModel()->getId();
            $customerModel = $this->getCustomerModel();
            $addressModel = $this->getAddressModel();
            $oscCustomerId = $data['id'];
            $data['group_id'] = $customerGroupModel->getName();

            $prepareCreated = explode(' ', $data['created_at']);
               $dateFormat = 'YYYY-MM-dd HH:mm:ss';
            $dateCreated = new Zend_Date();
            $dateCreated->setTimezone($timezone);
            $dateCreated->setDate($prepareCreated[0], 'YYYY-MM-dd');
            $dateCreated->setTime($prepareCreated[1], 'HH:mm:ss');
               $dateCreated->setTimezone('GMT');
            $data['created_at'] =  $dateCreated->toString($dateFormat);

            foreach ($data as $field => $value) {
                if (in_array($field, array('firstname', 'lastname'))) {
                    $value = $this->convert($value);
                }
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

                        if (in_array($field, array_keys($addressFieldMapping))) {
                            $value = $this->convert($value);
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
                $customerModel->setImportMode(true);
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
                $this->saveLogs(array($oscCustomerId => $customerId), 'customer');
                $this->_saveRows++;
            } catch (Exception $e) {
                $this->_addErrors(Mage::helper('oscommerce')->__('Email %s cannot be saved because of %s.', $data['email'], $e->getMessage()));
            }
        }
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getCustomerIdPair()
    {
        if (!$this->_customerIdPair) {
            $this->_customerIdPair = $this->getLogPairsByTypeCode('customer');
        }
        return $this->_customerIdPair;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $data
     */
    public function setCustomerIdPair($data)
    {
        if (is_array($data)) {
            $this->_customerIdPair = $data;
        }
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $startFrom
     * @param unknown_type $useStartFrom
     */
    public function importCategories($startFrom = 0, $useStartFrom = false)
    {
        $importModel = $this->getImportModel();
        $this->_logData['type_id'] = $this->getImportTypeIdByCode('category');
        $this->_logData['import_id'] = $importModel->getId();

        $this->_resetSaveRows();
        $this->_resetErrors();
        $maxRows = $this->getMaxRows();
        $totalCategories = $this->getCategoriesCount();

        $pages = floor($totalCategories / $maxRows) + 1;
        if (!$useStartFrom) {
            for ($i = 0; $i < $pages; $i++) {
                if ($categories = $this->getCategories(array('from' => $i * $maxRows,'max' => $maxRows))) {
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

    /**
     * Save Category
     *
     * @param unknown_type $data
     */
    protected function _saveCategory($data)
    {
        $categoryModel = $this->getCategoryModel();
        $oscCategoryId = $data['id'];
        unset($data['id']);
        try {
            $data['store_id'] = 0;
            $data['is_active'] = 1;
            $data['display_mode'] = self::DEFAULT_DISPLAY_MODE;
            $data['is_anchor']    = self::DEFAULT_IS_ANCHOR;
            $data['attribute_set_id'] = $categoryModel->getDefaultAttributeSetId();
            $data['name'] = $this->convert($data['name']);
            $data['meta_title'] = html_entity_decode($data['name'], ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET);
            $categoryModel->setData($data);
            $categoryModel->save();
            $categoryId = $categoryModel->getId();
            $this->saveLogs(array($oscCategoryId => $categoryId), 'category');

            // saving data for different (encoding has been done in getCategoryToStores method)
            $storeData = $data['stores'];
            unset($data['stores']);
            if (isset($storeData)) {
                foreach ($storeData as $storeId=>$catData) {
                    $categoryModel->setStoreId($storeId)->setName($catData['name'])->setMetaTitle($catData['name'])
                    ->save();
                }
            }
            $this->_saveRows++;
        } catch (Exception $e) {
            $this->_addErrors(Mage::helper('oscommerce')->__('Category %s cannot be saved because of %s.', $data['name'], $e->getMessage()));
        }
    }

    /**
     * Build category path
     */
    public function buildCategoryPath()
    {
        $categoryIdPair = $this->getCategoryIdPair();
        if ($categoryIdPair) {
            foreach ($categoryIdPair as $oscommerceId => $magentoId) {
                $path = $this->getRootCategory()->getPath() . '/' . join('/', $this->getCategoryPath($oscommerceId));
                $bind = array('path' => $path);
                $where = array('entity_id = ?' => $magentoId);
                $this->_getWriteAdapter()->update($this->getTable('catalog_category'), $bind, $where);
            }
        }
    }

    /**
     * Retrieve category path
     *
     * @param integer $categoryId
     * @return array
     */
    public function getCategoryPath($categoryId)
    {
        $categoryIdPair = $this->getCategoryIdPair();
        $adapter = $this->_getForeignAdapter();
        $select = $adapter->select()
            ->from($this->getOscTable('categories'),
                array('parent_id'))
            ->where('categories_id=:categories_id');
        $bind = array(':categories_id' => $categoryId);
        $results = array();
        $result = array();
        if ($parentId = $this->_getForeignAdapter()->fetchOne($select, $bind)) {
            if ($result = $this->getCategoryPath($parentId)) {
                $results = array_merge($results, $result);
            } else {
                $results[] = $categoryIdPair[$parentId];
            }
        }
        $results[] = $categoryIdPair[$categoryId];
        return $results;
    }

    /**
     * Retrieve  loaded category ids
     *
     * @return array
     */
    public function getCategoryIdPair()
    {
        if (!$this->_categoryIdPair) {
            $this->_categoryIdPair = $this->getLogPairsByTypeCode('category');
        }
        return $this->_categoryIdPair;
    }

    /**
     * Set loaded category ids
     *
     * @param array $data
     */
    public function setCategoryIdPair($data)
    {
        if (is_array($data)) {
            $this->_categoryIdPair = $data;
        }
    }

    /**
     * Import products
     *
     * @param integer $startFrom
     * @param integer $useStartFrom
     */
    public function importProducts($startFrom = 0, $useStartFrom = false)
    {
        $taxCollections = $this->_getTaxCollections();
        $this->_resetSaveRows();
        $this->_resetErrors();
        $maxRows = $this->getMaxRows();
        $totalProducts = $this->getProductsCount();
        $pages = floor($totalProducts / $maxRows) + 1;
        if (!$useStartFrom) {
            for ($i = 0; $i < $pages; $i++) {
                if ($products = $this->getProducts(array('from'=>$i * $maxRows,'max'=>$maxRows))) {
                    foreach ($products as $product) {
                        if (!empty($product['tax_class_id'])) {
                            $product['tax_class_id'] = $taxCollections[$product['tax_class_id']];
                        }
                        $this->_saveProduct($product);
                    }
                }
            }
        } else {
            if ($products = $this->getProducts(array('from'=> $startFrom ,'max'=>$maxRows))) {
                foreach ($products as $product) {
                    if (!empty($product['tax_class_id'])) {
                        $product['tax_class_id'] = $taxCollections[$product['tax_class_id']];
                    }
                    $this->_saveProduct($product);
                }
            }
        }
    }

    /**
     * Save products data
     *
     * @param array $data
     */
    protected function _saveProduct($data)
    {
        $productAdapterModel = $this->getProductAdapterModel();
        $productModel = $this->getProductModel();
        $mageStores = $this->getLanguagesToStores();
        $storeInfo = $this->getOscStoreInformation();
        $storeName = $storeInfo['STORE_NAME'];
        $oscProductId = $data['id'];
        unset($data['id']);
        if ($this->_isProductWithCategories) {
            if ($categories = $this->getProductCategories($oscProductId)) {
                $data['category_ids'] = $categories;
            }
        }
        /**
         * Checking product by using sku and website
         */
        if (empty($data['sku'])) {
            $data['sku'] = $storeName . ' - ' . $oscProductId;
        }
        $productModel->unsetData();
        $productId = $productModel->getIdBySku($data['sku']);
        $productModel->load($productId);
        if ($productModel->getId()) {
            $websiteIds = $productModel->getWebsiteIds();

            if ($websiteIds) foreach ($websiteIds as $websiteId) {
                if ($websiteId == $this->getWebsiteModel()->getId()) {
                    $this->_addErrors(Mage::helper('oscommerce')->__('SKU %s was not imported since it already exists in %s',
                        $data['sku'],
                        $this->getWebsiteModel()->getName()));
                    return ;
                }
            }
        }
        try {
            if (isset($data['image'])) {
                if (substr($data['image'], 0, 1) != DS) {
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
                    $data['name'] = html_entity_decode($this->convert($store['name']), ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET);
                    $data['description'] = html_entity_decode($this->convert($store['description']), ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET);
                    $data['short_description'] = $data['description'];
                    $productAdapterModel->saveRow($data);
                }
            }

            $productId = $productAdapterModel->getProductModel()->getId();
            $this->saveLogs(array($oscProductId => $productId), 'product');
            $this->_saveRows++;
        } catch (Exception $e) {
            $this->_addErrors(Mage::helper('oscommerce')->__('SKU %s cannot be saved because of %s.', $data['sku'], $e->getMessage()));
        }
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $startFrom
     * @param unknown_type $useStartFrom
     */
    public function importOrders($startFrom = 0, $useStartFrom = false)
    {
        $importModel = $this->getImportModel();
        $this->_resetSaveRows();
        $this->_resetErrors();
        // Get orders

        $totalOrders = $this->getOrdersCount();
        $maxRows = $this->getMaxRows();
        $pages = floor($totalOrders / $maxRows) + 1;

        if (!$useStartFrom) {
            for ($i = 0; $i < $pages; $i++) {
                $orders = $this->getOrders(array('from' => $i * $maxRows, 'max' => $maxRows));
                if ($orders) foreach ($orders as $order) {
                    $this->_saveOrder($order);
                }
            }
        } else {
            $orders = $this->getOrders(array('from' => $startFrom, 'max' => $maxRows));
            if ($orders) foreach ($orders as $order) {
                $this->_saveOrder($order);
            }
        }
    }

    /**
     * Enter description here ...
     *
     */
    public function createOrderTables()
    {
        $importModel = $this->getImportModel();
        $importId  = $importModel->getId();

        $tables = array(
            'orders' => "CREATE TABLE `{$this->getTable('oscommerce/oscommerce_order')}` (
                  `osc_magento_id` int(11) NOT NULL auto_increment,
                  `orders_id` int(11) NOT NULL default '0',
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
                  `orders_status` varchar(32) default NULL,
                  `orders_date_finished` datetime default NULL,
                  `currency` varchar(3) default NULL,
                  `currency_value` decimal(14,6) default NULL,
                  `currency_symbol` varchar(3) default NULL,
                  `orders_total` decimal(14,6) default NULL,
                  PRIMARY KEY  (`osc_magento_id`),
                  KEY `idx_orders_customers_id` (`customers_id`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        "
            , 'orders_products' => "CREATE TABLE `{$this->getTable('oscommerce/oscommerce_order_products')}` (
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
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
                "

            , 'orders_total' => "CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce/oscommerce_order_total')}` (
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

            , 'orders_status_history'=>"CREATE TABLE IF NOT EXISTS `{$this->getTable('oscommerce/oscommerce_order_history')}` (
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
                echo $e;
                $conn->rollBack();
            }
        }

        $this->checkOrderField();
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $prefix
     */
    public function setTablePrefix($prefix)
    {
        if (isset($prefix)) $this->_prefix = $prefix;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getTablePrefix()
    {
        return $this->_prefix;
    }

    /**
     * Set if assign product to category.
     *
     * @param unknown_type $yn
     */
    public function setIsProductWithCategories($yn)
    {
        if (is_bool($yn)) {
            $this->_isProductWithCategories = $yn;
        }
    }

    /**
     * Logging imported data to oscommerce_ref table
     *
     *
     * @param array $data
     * @return Mage_Oscommerce_Model_Resource_Oscommerce
     */
    public function log($data = array())
    {
        if (isset($data)) {
            $adapter = $this->_getWriteAdapter();
            $adapter->beginTransaction();
            try {
                $adapter->insert($this->getTable('oscommerce/oscommerce_ref'), $data);
                $adapter->commit();
            } catch (Exception $e) {
                $adapter->rollBack();
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
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
     * @param unknown_type $limit
     * @return unknown
     */
    public function getProducts($limit = array())
    {
        $defaultLanguage = $this->getOscDefaultLanguage();
        $defaultLanguageId = $defaultLanguage['id'];
        $code = $this->getWebsiteModel()->getCode();
        $website = $code? $code: $this->getCurrentWebsite()->getCode();
        $connection = $this->_getForeignAdapter();
        $select = $connection->select()
            ->from(array('p' => $this->getOscTable('products')),
                array('id'            => 'products_id',
                      'qty'           => 'products_quantity',
                      'sku'           => 'products_model',
                      'price'         => 'products_price',
                      'image'         => 'products_image',
                      'weight'        => 'products_weight',
                      'status'        => 'IF(p.products_status,\'Enabled\',\'Disabled\')',
                      'is_in_stock'   => 'IF(p.products_status,\'1\',\'0\')',
                      'tax_class_id'  => 'products_tax_class_id',
                      'visibility'    => new Zend_Db_Expr($connection->quote(self::DEFAULT_VISIBILITY)),
                      'attribute_set' => new Zend_Db_Expr($connection->quote(self::DEFAULT_ATTRIBUTE_SET)),
                      'type'          => new Zend_Db_Expr($connection->quote(self::DEFAULT_PRODUCT_TYPE)),
                      'website'       => new Zend_Db_Expr($connection->quote($website)),
            ))
            ->joinInner(array('pd' => $this->getOscTable('products_description')),
                'pd.products_id=p.products_id AND pd.language_id='.$defaultLanguageId,
                array('name'        => 'products_name',
                      'description' => 'products_description'))
            ->joinLeft(array('sp' => $this->getOscTable('specials')),
                'sp.products_id=p.products_id',
                array('special_price'     => 'specials_new_products_price',
                      'special_from_date' => 'specials_date_added',
                      'special_to_date'   => 'expires_date'));
        if ($limit && isset($limit['from']) && isset($limit['max'])) {
            $select->limit($limit['max'], $limit['from']);
        }
        if (!($result = $this->_getForeignAdapter()->fetchAll($select))) {
            $result = array();
        }
        return $result;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getProductsCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('products')}`");
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getCategoriesCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('categories')}`");
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getCustomersCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('customers')}`");
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getOrdersCount()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('orders')}`");
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $limit
     * @return unknown
     */
    public function getOrders($limit = array())
    {
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

    /**
     * Enter description here ...
     *
     * @param unknown_type $data
     */
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
        $customerIdPair = $this->getCustomerIdPair();
        $importId  = $importModel->getId();
        $websiteId = $this->getWebsiteModel()->getId();
        if ($data['customers_id'] > 0 && isset($this->_customerIdPair[$data['customers_id']])) {
            foreach ($data as $field => $value) {
                if (!in_array($field, $fieldNoEnc)) {
                    $data[$field] = $this->convert($value);
                }
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
            $this->_getWriteAdapter()->insert($this->getTable('oscommerce/oscommerce_order'), $data);
            $oscMagentoId = $this->_getWriteAdapter()->lastInsertId();
            $this->_saveRows++;

            // Get orders products
            $select  = "SELECT `orders_products_id`, `orders_id`, `products_id` ";
            $select .= ", `products_model`, `products_name`, `products_price`, `final_price` ";
            $select .= ", `products_tax`, `products_quantity` ";
            $select .= " FROM `{$this->getOscTable('orders_products')}` WHERE `orders_id`= :order_id";
            if ($orderProducts = $this->_getForeignAdapter()->fetchAll($select, array(':order_id' => $data['orders_id']))) {
                foreach ($orderProducts as $orderProduct) {
                    unset($orderProduct['orders_id']);
                    unset($orderProduct['orders_products_id']);
                    $orderProduct['osc_magento_id'] = $oscMagentoId;
                    foreach ($orderProduct as $field => $value) {
                        if (!in_array($field, $fieldNoEnc)) {
                            $orderProduct[$field] = $this->convert($value);
                        }
                    }
                    $this->_getWriteAdapter()->insert($this->getTable('oscommerce/oscommerce_order_products'), $orderProduct);
                }
            }

            // Get orders totals
            $select  = "SELECT `orders_total_id`, `orders_id`, `title`, `text`, `value`, `class`, `sort_order` ";
            $select .= " FROM `{$this->getOscTable('orders_total')}` WHERE `orders_id`= :order_id ORDER BY `sort_order`";

            if ($orderTotals = $this->_getForeignAdapter()->fetchAll($select, array(':order_id' => $data['orders_id']))) {
                foreach ($orderTotals as $orderTotal) {

                    unset($orderTotal['orders_id']);
                    unset($orderTotal['orders_total_id']);
                    $orderTotal['osc_magento_id'] = $oscMagentoId;
                    $orderTotal['title'] = $this->convert($orderTotal['title']);
                    $orderTotal['text'] = $this->convert($orderTotal['text']);
                    $this->_getWriteAdapter()->insert($this->getTable('oscommerce/oscommerce_order_total'), $orderTotal);
                }
            }

            $defaultLanguage = $this->getOscDefaultLanguage();
            $defaultLanguageId = $defaultLanguage['id'];

            // Get orders status history
            $select  = "SELECT `osh`.`orders_status_history_id`, `osh`.`orders_id`, `osh`.`orders_status_id` ";
            $select .= ", `os`.`orders_status_name` `orders_status`, `osh`.`date_added`, `osh`.`customer_notified`, `osh`.`comments` ";
            $select .= " FROM `{$this->getOscTable('orders_status_history')}` osh ";
            $select .= " LEFT JOIN `{$this->getOscTable('orders_status')}` os ON `os`.`orders_status_id`=`osh`.`orders_status_id` ";
            $select .= " AND `os`.`language_id`= :lang_id";
            $select .= " WHERE `osh`.`orders_id`= :orders_id";
            $bind = array(
                ':lang_id' => $defaultLanguageId,
                ':orders_id' => $data['orders_id']
            );
            if ($orderHistories = $this->_getForeignAdapter()->fetchAll($select, $bind)) {
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
                    $orderHistory['orders_status'] = $this->convert($orderHistory['orders_status']);
                    $orderHistory['comments'] = $this->convert($orderHistory['comments']);
                    $orderHistory['customer_notified'] = $this->convert($orderHistory['customer_notified']);

                    $this->_getWriteAdapter()->insert($this->getTable('oscommerce/oscommerce_order_history'), $orderHistory);
                }
            }
        } else {
            $this->_addErrors(Mage::helper('oscommerce')->__('Order #%s failed to import because the customer ID #%s associated with this order could not be found.', $data['orders_id'], $data['customers_id']));
        }
    }

    /**
     * Getting product description for different stores
     *
     * @param integer $productId
     * @return mix array/boolean
     */
    public function getProductStores($productId)
    {
        if (!$this->_productsToStores) {
            $select =  "SELECT `products_id`, `language_id` `store`, `products_name` `name`, `products_description` `description`";
            $select .= " FROM `{$this->getOscTable('products_description')}` ";
            if ($results = $this->_getForeignAdapter()->fetchAll($select)) {
                foreach ($results as $result) {
                    $this->_productsToStores[$result['products_id']][$result['store']] = array(
                        'name'=>$result['name'],
                        'description' => $result['description']
                    );
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
            $adapter = $this->_getForeignAdapter();
            $select = $adapter->select()
                ->from($this->getOscTable('products_to_categories'), array('products_id', 'categories_id'));
            if ($results = $adapter->fetchAll($select)) {
                $categories = array();
                foreach ($results as $result) {
                    $categories[$result['products_id']][] = $result['categories_id'];
                }
                $importId = $importModel->getId();
                $typeId = $this->getImportTypeIdByCode('category');
                if ($categories) {
                    foreach ($categories  as  $product => $category) {
                        $select = $this->_getReadAdapter()->select();
                        $select->from(array('osc'=>$this->getTable('oscommerce/oscommerce_ref')),
                            array('id'     =>'id',
                                  'ref_id' =>'ref_id'));
                        $select->where("osc.import_id = :import_id");
                        $select->where("osc.type_id = :type_id");
                        $select->where("osc.value in (?)", $category);
                        $bind = array(
                            ':import_id' => $importId,
                            ':type_id'   => $typeId,
                        );
                        $resultCategories = $this->_getReadAdapter()->fetchPairs($select, $bind);
                        if ($resultCategories) {
                           $this->_productsToCategories[$product] = join(',', array_values($resultCategories));
                        }
                    }
                }
            }
        }
        if (isset($this->_productsToCategories[$productId])) {
            return $this->_productsToCategories[$productId];
        }
        return false;
    }

    /**
     * Get categories from osCommerce
     *
     * @param array $limit
     * @return array
     */
    public function getCategories($limit = array())
    {
        $defaultLanguage = $this->getOscDefaultLanguage();
        $defaultLanguageId = $defaultLanguage['id'];
        $adapter = $this->_getForeignAdapter();
        $select = $adapter->select()
            ->from(array('c'=>$this->getOscTable('categories')), array('id'=>'categories_id',  'parent_id'=>'parent_id'))
            ->joinInner(array('cd'=>$this->getOscTable('categories_description')),
                'cd.categories_id=c.categories_id AND cd.language_id=:language_id',
                array('name' => 'categories_name'));
        if ($limit && isset($limit['from']) && isset($limit['max'])) {
            $select->limit($limit['max'], $limit['from']);
        }
        $results = $adapter->fetchAll($select, array(':language_id' => $defaultLanguageId));
        if (!$results) {
            $results = array();
        } else {
            $stores = $this->getLanguagesToStores();
            foreach ($results as $index => $result) {
                if ($categoriesToStores = $this->getCategoriesToStores($result['id'])) {
                    foreach ($categoriesToStores as $store => $categoriesName) {
                        if (isset($stores[$store])) {
                            $storeLanguage = $stores[$store];
                        } else {
                            $storeLanguage = 0;
                        }
                        $results[$index]['stores'][$storeLanguage] = array(
                            'name'=>html_entity_decode($this->convert($categoriesName), ENT_QUOTES, self::DEFAULT_MAGENTO_CHARSET)
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
            $select = $this->_getReadAdapter()->select();
            $select->from(array('ref'=>$this->getTable('oscommerce/oscommerce_ref')),
                array('value'=>'value', 'ref_id'=>'ref_id'));
            $select->where('ref.import_id=:import_id');
            $select->where('ref.type_id=:type_id');
            $bind = array(
                ':import_id' => $importId,
                ':type_id'   => $typeId
            );
            $this->_languagesToStores = $this->_getReadAdapter()->fetchPairs($select, $bind);
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
        $adapter = $this->_getForeignAdapter();
        $select = $adapter->select()
            ->from($this->getOscTable('categories_description'), array('language_id', 'categories_name'))
            ->where('categories_id = ?', $categoryId);
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
            $adapter = $this->_getForeignAdapter();
            $select = $adapter->select()
                ->from($this->getOscTable('languages'),
                    array('id'        => 'languages_id',
                          'name'      => 'name',
                          'scode'     => 'code',
                          'code'      => 'directory',
                          'is_active' => new Zend_Db_Expr(1)));
            $this->_oscStores =  $adapter->fetchAll($select);
        }
        return $this->_oscStores;
    }

    /**
     * Default language
     *
     * @return unknown
     */
    public function getOscDefaultLanguage()
    {
        if (!$this->_oscDefaultLanguage) {
            $oscStoreInfo = $this->getOscStoreInformation();
            $languageCode = $oscStoreInfo['DEFAULT_LANGUAGE'];
            if ($stores = $this->getOscStores()) {
                foreach ($stores as $store) {
                    if ($store['scode'] == $languageCode) {
                        $this->_oscDefaultLanguage = $store;
                    }
                }
            }
        }
        return $this->_oscDefaultLanguage;
    }

    /**
     * Getting customers from osCommerce
     *
     * @param unknown_type $limit
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

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getTotalCustomers()
    {
        return $this->_getForeignAdapter()->fetchOne("SELECT count(*) FROM `{$this->getOscTable('customers')}`");
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $name
     * @return unknown
     */
    public function getCustomerName($name)
    {
        if (isset($name)) {
            $n = explode(" ", $name);
            if (sizeof($n) > 1) {
                $newName['lastname'] = $n[(sizeof($n) - 1)];
                $newName['fistname']  = Mage::helper('core/string')->substr($name, 0,
                    Mage::helper('core/string')->strlen($name) - (Mage::helper('core/string')->strlen($newName['lastname'] + 1))
                );
                return $newName;
            } else {
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
     * @param unknown_type $addressId
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
     * Getting importing types for loging into oscommerce_type
     *
     * @return array
     */
    public function getImportTypes()
    {
        if (!$this->_importType) {
            $adapter = $this->_getReadAdapter();
            $select = $adapter->select()
                ->from($this->getTable('oscommerce/oscommerce_type'));
            $this->_importType = $adapter->fetchAll($select);
        }
        return $this->_importType;
    }

    /**
     * Getting import_type_id by code
     *
     * @param integer $code
     * @return string/boolean
     */
    public function getImportTypeIdByCode($code = '')
    {
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
     * Load countries information
     */
    protected function _loadCountryCodeData()
    {
        $adapter = $this->_getForeignAdapter();
        $select = $adapter->select()
            ->from($this->getOscTable('countries'));
        $countries = $adapter->fetchAll($select);
        if ($countries) {
            foreach ($countries as $country) {
                $this->_countryIdToCode[$country['countries_id']] = $country['countries_iso_code_2'];
                $this->_countryNameToCode[$country['countries_name']] = $country['countries_iso_code_2'];
            }
        }
    }

    /**
     * Getting country code by country id
     *
     * @param integer $id
     * @return string|boolean
     */
    public function getCountryCodeById($id)
    {
        if (!$this->_countryIdToCode) {
            $this->_loadCountryCodeData();
        }
        $countries = $this->_countryIdToCode;
        if (isset($id) && isset($countries[$id])) {
            return $countries[$id];
        }
        return false;
    }

    /**
     * Getting country name by country id
     *
     * @param string $name
     * @return string|boolean
     */
    public function getCountryCodeByName($name)
    {
        if (!$this->_countryNameToCode) {
            $this->_loadCountryCodeData();
        }
        $countries = $this->_countryNameToCode;
        if (isset($countries[$name])) {
            return $countries[$name];
        }
        return false;
    }

    /**
     * Retrieve id of country by code.
     *
     * DON'T USED?
     *
     * @param unknown_type $countryCode
     * @return unknown
     */
    public function getCountryIdByCode($countryCode)
    {
        if (!$this->_countryIdToCode) {
            $this->_loadCountryCodeData();
        }
        return array_search($this->_countryIdToCode, $countryCode);
    }

    /**
     * Getting regions from osCommerce
     *
     * @return array
     */
    public function _loadRegions()
    {
        if (!$this->_regionCode) {
            $adapter = $this->_getForeignAdapter();
            $select = $adapter->select()
                ->from($this->getOscTable('zones'), array('zone_id', 'zone_name'));
            $this->_regionCode = $adapter->fetchPairs($select);
        }
        return $this->_regionCode;
    }

    /**
     * Getting region name by id
     *
     * @param integer $id
     * @return string/boolean
     */
    public function getRegionCode($id)
    {
        if (!$this->_regionCode) {
            $this->_loadRegions();
        }
        if (isset($id) && isset($this->_regionCode[$id])) {
            return $this->_regionCode[$id];
        }
        return false;
    }

    /**
     * Set Locale to store
     *
     * @param string $locale
     * @return Mage_Oscommerce_Model_Resource_Oscommerce
     */
    public function setStoreLocales($locale)
    {
        if (isset($locale) && is_array($locale)) {
            $this->_storeLocales = $locale;
        }
        return $this;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getStoreLocales()
    {
        if ($this->_storeLocales) {
            return $this->_storeLocales;
        } else {
            return array('default' => self::DEFAULT_LOCALE );
        }
    }

    /**
     * Enter description here ...
     *
     * @param Mage_Catalog_Model_Category $category
     */
    public function setRootCategory(Mage_Catalog_Model_Category $category)
    {
        $this->_rootCategory = $category;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getRootCategory()
    {
        if (!$this->_rootCategory) {
            $this->_rootCategory = $this->getCategoryModel()->load($this->getCurrentWebsite()->getDefaultStoreGroup()->getRootCategoryId());
        }
        return $this->_rootCategory;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $id
     */
    public function setWebsiteId($id)
    {
        $this->_websiteId = (int) ($id ? $id : 0);
    }

    /**
     * Enter description here ...
     *
     */
    public function importTaxClasses()
    {
        $taxModel = Mage::getModel('tax/class');
        $storeInfo = $this->getOscStoreInformation();
        $storeName = $storeInfo['STORE_NAME'];
        $taxPairs = array();
        if ($classes = $this->getTaxClasses()) {
            $existedClasses = $taxCollections = Mage::getResourceModel('tax/class_collection')
                ->addFieldToFilter('class_type', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
                ->load()
                ->toOptionHash();

            foreach ($classes as $id => $name) {
                $taxModel->unsData();
                $className = $name . '_' . $storeName;
                if (in_array($className, $existedClasses)) {
                    $taxId = array_search($className, $existedClasses);
                } else {
                    $taxModel->setId(null);
                    $taxModel->setClassType(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT);
                    $taxModel->setClassName($name . '_' . $storeName);
                    $taxModel->save();
                    $taxId = $taxModel->getId();
                }
                $taxPairs[$id] = $taxId;
            }
        }

        if (sizeof($taxPairs) > 0) {
            $this->saveLogs($taxPairs, 'taxclass');
        }
    }

    /**
     * Retrieve tax collection
     *
     * @return unknown
     */
    protected function _getTaxCollections()
    {
        $taxPairs = $this->getLogPairsByTypeCode('taxclass');
        $flipTaxPairs = array_flip($taxPairs);
        $newTaxPairs = array();
        $taxCollections = Mage::getResourceModel('tax/class_collection')
                ->addFieldToFilter('class_type', Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
                ->load()
                ->toOptionArray();
        if ($taxCollections) {
            foreach ($taxCollections as $tax) {
                if (isset($flipTaxPairs[$tax['value']])) {
                    $newTaxPairs[$flipTaxPairs[$tax['value']]] = $tax['label'];
                }
            }
        }
        return $newTaxPairs;
    }

    /**
     * Save type and data to oscommerce_ref
     *
     * @param unknown_type $data
     * @param unknown_type $type
     */
    public function saveLogs($data, $type = null)
    {
        $importId   = $this->getImportModel()->getImportId();
        $typeId     = $this->getImportTypeIdByCode($type);
        $userId     = $this->_getCurrentUserId();
        $createdAt  = $this->formatDate(time());
        if (is_array($data) && $typeId > 0) {
            foreach ($data as $value => $refId) {
                $log = array(
                    'value'     => $value,
                    'ref_id'    => $refId,
                    'import_id' => $importId,
                    'type_id'   => $typeId,
                    'user_id'   => $userId,
                    'created_at'=> $createdAt
                );
                $this->_getWriteAdapter()->insert($this->getTable('oscommerce/oscommerce_ref'), $log);
            }
        }
    }

    /**
     * Retrieve Log by code
     *
     * @param string $code
     * @return array
     */
    public function getLogPairsByTypeCode($code)
    {
        $typeId = $this->getImportTypeIdByCode($code);
        $importId = $this->getImportModel()->getId();
        $result = array();
        if (!is_null($typeId)) {
            $select =  $this->_getReadAdapter()->select();
            $select->from($this->getTable('oscommerce/oscommerce_ref'), array('value','ref_id'));
            $select->where('import_id = :import_id');
            $select->where('type_id   = :type_id');
            $bind = array(
                ':import_id' => $importId,
                ':type_id'   => $typeId
            );
            $result = $this->_getReadAdapter()->fetchPairs($select, $bind);
        }
        return $result;
    }

    /**
     * Retrieve tax classes from osCommerce
     *
     * @return array
     */
    public function getTaxClasses()
    {
        $adapter = $this->_getForeignAdapter();
        $select = $adapter->select()
            ->from($this->getOscTable('tax_class'), array(
                'id'    => 'tax_class_id',
                'title' => 'tax_class_title'));
        if (!($results = $adapter->fetchPairs($select))) {
            $results = array();
        }
        return $results;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $str
     * @return unknown
     */
    private function _format($str)
    {
        $str = preg_replace('#[^0-9a-z\/\.]+#i', '', $str);
        $str = strtolower(str_replace('\\s', '', $str));
        return $str;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $prefix
     */
    public function setPrefixPath($prefix)
    {
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
            $websiteId = $this->_currentWebsiteId;
        }
        $result = array();
        if (!empty($customerId)) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('ord'=>$this->getTable('oscommerce/oscommerce_order')))
                ->join(
                    array('order_total'=>$this->getTable('oscommerce/oscommerce_order_total')),
                    "order_total.osc_magento_id=ord.osc_magento_id AND order_total.class='ot_total'",
                    array('value'))
                ->where("ord.magento_customers_id= :customer_id")
                ->where("ord.website_id= :website_id");
            $bind = array(
                ':customer_id' => $customerId,
                ':website_id'  => $websiteId,
            );
            $result = $this->_getReadAdapter()->fetchAll($select, $bind);
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
            $select = $this->_getReadAdapter()->select()
                ->from($this->getTable('oscommerce/oscommerce_order'))
                ->where('osc_magento_id = :mage_id');
            $order = $this->_getReadAdapter()->fetchRow($select, array(':mage_id' => $id));
            if ($order) {
                $result['order'] = $order;
                foreach (array('products','total','history') as $table) {
                    $select = $this->_getReadAdapter()->select()
                        ->from($this->getTable('oscommerce/oscommerce_order_'. $table))
                        ->where('osc_magento_id = :mage_id');
                    $result[$table] = $this->_getReadAdapter()->fetchAll($select, array(':mage_id' => $id));
                }
            }

        }
        return $result;
    }

    /**
     * Enter description here ...
     *
     */
    protected function checkOrderField()
    {
        $columnName = 'currency_symbol';
        try {
            /** @var $adapter Varien_Db_Adapter_Interface */
            $adapter = $this->_setupConnection();
            $columns = $adapter->describeTable($this->getTable('oscommerce/oscommerce_order'));
            if (!isset($columns[$columnName])) {
                $adapter->addColumn($this->getTable('oscommerce/oscommerce_order'), $columnName, array(
                    'COLUMN_TYPE' => Varien_Db_Ddl_Table::TYPE_TEXT,
                    'LENGTH'      => 3,
                    'NULLABLE'    => true,
                    'COMMENT'     => 'Currency Symbol'
                ));

            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $rows
     */
    public function setMaxRows($rows)
    {
        if (is_integer($rows)) {
            $this->_maxRows = $rows;
        }
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
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

    /**
     * Enter description here ...
     *
     * @return unknown
     */
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

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getConfigModel()
    {
        if (is_null($this->_configModel)) {
            $object = Mage::getModel('core/config_data');
            $this->_configModel = Varien_Object_Cache::singleton()->save($object);
        }
        return Varien_Object_Cache::singleton()->load($this->_configModel);
    }

    /**
     * Import collection
     *
     * @param integer $importId
     * @return array
     */
    public function importCollection($importId = null)
    {
        $importTypes = array('website', 'root_category', 'group');
        $result = array();
        if (!is_null($importId)) {
            $select = $this->_getReadAdapter()->select()
                ->from(array('ref'=>$this->getTable('oscommerce/oscommerce_ref')))
                ->join(
                    array('type'=>$this->getTable('oscommerce/oscommerce_type')),
                    "type.type_id=ref.type_id AND type.type_code in ('" . join("','", $importTypes) . "')",
                    array('type.type_code'))
                ->where("ref.import_id = ?", (int)$importId);
            if ($results = $this->_getReadAdapter()->fetchAll($select)) {
                foreach ($results as $result) {
                    $this->_importCollection[$result['type_code']] = $result['ref_id'];
                }
            }

        }
        return $this->_importCollection;
    }

    /**
     * Enter description here ...
     *
     * @param Mage_Oscommerce_Model_Oscommerce $model
     */
    public function setImportModel(Mage_Oscommerce_Model_Oscommerce $model)
    {
        $this->_importModel = $model;
    }

    /**
     * Enter description here ...
     *
     * @return unknown
     */
    public function getImportModel()
    {
        if (!$this->_importModel) {
            $this->_importModel = Mage::registry('oscommerce_adminhtml_import');
        }
        return $this->_importModel;
    }

    /**
     * Enter description here ...
     *
     * @param unknown_type $code
     * @return unknown
     */
    public function getCollections($code)
    {
        if ($this->_importCollection) {
            return $this->_importCollection;
        }
        return;
    }

    /**
     * Deleting oscommerce reference records
     *
     * @param integer $id
     * @return Mage_Oscommerce_Model_Resource_Oscommerce
     */
    public function deleteRecords($id = null)
    {
        if (!is_null($id) && $id > 0) {
            $this->_getWriteAdapter()->delete($this->getTable('oscommerce/oscommerce_ref'),
                array('import_id = ?' => $id));
        }
        return $this;
    }

    /**
     * Formatting string
     *
     * @param unknown_type $data
     * @param integer $number
     * @return string
     */
    protected function _formatStringTruncate($data, $number)
    {
        if (str_word_count($data, 0)>$number) {
            $wordKey = str_word_count($data, 1);
            $posKey = str_word_count($data, 2);
            reset($posKey);
            foreach ($wordKey as $key => &$value) {
                $value=key($posKey);
                next($posKey);
            }
            return substr($data, 0, $wordKey[$number]);
        } else {
            return $data;
        }
    }

    /**
     * Getting current user ID
     *
     * @return string
     */
    protected function _getCurrentUserId()
    {
        if (!$this->_currentUserId) {
            $this->_currentUserId = Mage::getSingleton('admin/session')->getUser()->getId();
            $this->_logData['user_id'] = $this->_currentUserId;
        }
        return $this->_currentUserId;
    }

    /**
     * Getting oscommerce table with prefix
     *
     * @param string $table
     * @return string
     */
    public function getOscTable($table)
    {
        return $this->_prefix.$table;
    }

    /**
     * Setting connection charset
     *
     * @param string $charset
     */
    public function setConnectionCharset($charset)
    {
        $this->_connectionCharset = $charset;
    }

    /**
     * Getting connection charset, set deafult as utf8
     * if there is no predefine charset
     *
     * @return string
     */
    public function getConnectionCharset()
    {
        if (!$this->_connectionCharset) {
             $this->_connectionCharset = self::DEFAULT_FIELD_CHARSET;
        }
        return $this->_connectionCharset;
    }

    /**
     * Reset connection charset
     */
    public function resetConnectionCharset()
    {
        $charset = $this->getConnectionCharset();
        $this->_getForeignAdapter()->query("SET NAMES '{$charset}'");
    }

    /**
     * Setting dataCharset by user defined encoding charset
     *
     * @param string $charset
     */
    public function setDataCharset($charset)
    {
        if (!is_null($charset)) {
            $this->_dataCharset = $charset;
        }
    }

    /**
     * Getting dataCharset
     *
     * @return string
     */
    public function getDataCharset()
    {
        return $this->_dataCharset;
    }

    /**
     * Converting encoded charsets
     *
     * @param mixed $data
     * @param array $notIncludedFields
     * @return mixed
     */
    public function convert($data, array $notIncludedFields = array())
    {
        $charset = $this->getDataCharset();
        if (!is_null($charset) || $charset != self::DEFAULT_FIELD_CHARSET) {
            if (is_array($data)) {
                foreach ($data as $field => $value) {
                    if (!in_array($field, $notIncludedFields)) {
                        $newValue = @iconv($charset, self::DEFAULT_FIELD_CHARSET, $value);
                        if (strlen($newValue)) {
                            $data[$field] = $newValue;
                        }
                    }
                }
            } else {
                $newValue = @iconv($charset, self::DEFAULT_MAGENTO_CHARSET, $data);
                if (strlen($newValue)) {
                    $data = $newValue;
                }
            }
        }
        return $data;
    }

    /**
     * Getting saveRows
     *
     * @return integer
     */
    public function getSaveRows()
    {
        return $this->_saveRows;
    }

    /**
     * Resetting saveRows as zero
     *
     */
    protected function _resetSaveRows()
    {
        $this->_saveRows = 0;
    }

    /**
     * Adding error messages
     *
     * @param string $error
     */
    protected function _addErrors($error)
    {
        if (isset($error)) $this->_errors[] = $error;
    }

    /**
     * Getting all errors
     *
     * @return array
     */
    public function getErrors()
    {
        if (sizeof($this->_errors) > 0) {
            return $this->_errors;
        }
    }

    /**
     * Resetting error as empty array
     *
     */
    protected function _resetErrors()
    {
        $this->_errors = array();
    }
}
