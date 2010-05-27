<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Entity data model
 */
class Enterprise_GiftRegistry_Model_Entity extends Enterprise_Enterprise_Model_Core_Abstract
{
    /**
     * Type object
     * @var Enterprise_GiftRegistry_Model_Type
     */
    protected $_type = null;

    /**
     * Type id
     *
     * @var int
     */
    protected $_typeId = null;

    /**
     * Attributes array
     *
     * @var array
     */
    protected $_attributes = null;

    /**
     * Directory region models
     *
     * @var array
     */
    const XML_PATH_SHARE_EMAIL_IDENTITY = 'enterprise_giftregistry/sharing_email/identity';
    const XML_PATH_SHARE_EMAIL_TEMPLATE = 'enterprise_giftregistry/sharing_email/template';

   /**
     * Init resource model
     */
    protected function _construct() {
        $this->_init('enterprise_giftregistry/entity');
        parent::_construct();
    }

    /**
     * Add items to registry
     *
     * @param array $itemsIds
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function addQuoteItems($itemsIds)
    {
        if (is_array($itemsIds)) {
            foreach($itemsIds as $itemId) {
                $quoteItem = Mage::getModel('sales/quote_item')->load($itemId);
                if ($quoteItem && $quoteItem->getId()) {
                    $this->addItem($quoteItem);
                }
            }
        }
        return $this;
    }

    /**
     * Add new product to registry
     *
     * @param int|Mage_Sales_Model_Quote_Item $itemToAdd
     * @return Enterprise_GiftRegistry_Model_Item
     */
    public function addItem($itemToAdd)
    {
        if ($itemToAdd instanceof Mage_Sales_Model_Quote_Item) {
            $productId = $itemToAdd->getProductId();
            $qty = $itemToAdd->getQty();
        } else {
            $productId = $itemToAdd;
            $qty = 1;
        }

        $item = Mage::getModel('enterprise_giftregistry/item');
        $item->loadByProductRegistry($this->getId(), $productId);

        if ($item->getId()) {
            $item->setQty($item->getQty() + $qty)
                ->save();
        } else {
            $item->setEntityId($this->getId())
                ->setProductId($productId)
                ->setQty($qty)
                ->save();
        }
        return $item;
    }

    /**
     * Send share email
     *
     * @param string $email
     * @param int $storeId
     * @param string $message
     * @return bool
     */
    public function sendShareEmail($recipient, $storeId, $message, $sender = null)
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $store = Mage::app()->getStore($this->getStoreId());
        $mail  = Mage::getModel('core/email_template');
        $template = $store->getConfig(self::XML_PATH_SHARE_EMAIL_TEMPLATE);

        if (is_array($recipient)) {
            $email = $recipient['email'];
        } else {
            $email = $recipient;
        }

        if (is_array($sender)) {
            $identity = $sender;
        } else {
            $identity = $store->getConfig(self::XML_PATH_SHARE_EMAIL_IDENTITY);
        }

        $templateVars = array(
            'store'   => $store,
            'message' => $message,
            'registry_link' => $this->getRegistryLink()
        );

        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));
        $mail->sendTransactional($template, $identity, $email, null, $templateVars);

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Return frontend registry link
     *
     * @return string
     */
    public function getRegistryLink()
    {
        return $this->getUrl('*/*/*');
    }

    /**
     * Return comma-separated list of entitity registrants
     *
     * @return string
     */
    public function getRegistrants()
    {
        $collection = $this->getRegistrantsCollection();
        if ($collection->getSize()) {
            $registrants = array();
            foreach($collection as $item) {
                $registrants[] =  $item->getFirstname().' '.$item->getLastname();
            }
            return implode(', ', $registrants);
        }
        return '';
    }

    /**
     * Return entity registrants collection
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Person_Collection
     */
    public function getRegistrantsCollection()
    {
        $collection = Mage::getModel('enterprise_giftregistry/person')->getCollection()
            ->addRegistryFilter($this->getId());

        return $collection;
    }

    /**
     * Return entity items collection
     *
     * @return Enterprise_GiftRegistry_Model_Mysql4_Item_Collection
     */
    public function getItemsCollection()
    {
        $collection = Mage::getModel('enterprise_giftregistry/item')->getCollection()
            ->addRegistryFilter($this->getId());

        return $collection;
    }

    /**
     * Get active entity
     *
     * @param int $customerId
     * @return false|Enterprise_GiftRegistry_Model_Entity
     */
    public function getActiveEntity($customerId)
    {
        $collection = $this->getCollection()
            ->filterByCustomerId($customerId)
            ->filterByActive();

        foreach ($collection as $entity) {
            return $entity;
        }
        return false;
    }

    /**
     * Set active entity
     *
     * @param int $customerId
     * @param int $entityId
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function setActiveEntity($customerId, $entityId)
    {
        $this->_getResource()->setActiveEntity($customerId, $entityId);
        return $this;
    }

    /**
     * Search
     *
     * @param array $params
     * @return array
     */
    public function search($params)
    {
        $params = $this->_prepareSearchParams($params);
        return $this->_getResource()->quickSearch($params);
    }

    /**
     * Prepare search params
     *
     * @param array $params
     * @return array
     */
    protected function _prepareSearchParams($params)
    {
        $params['store_id'] = Mage::app()->getStore()->getId();
        $params['website_id'] = Mage::app()->getStore()->getWebsiteId();

        if (isset($params['firstname'])) {
            $params['firstname'] = substr(trim($params['firstname']), 0 , 2);
        }

        if (isset($params['lastname'])) {
            $params['lastname'] = substr(trim($params['lastname']), 0 , 2);
        }

        return $params;
    }

    /**
     * Return address object entity on data in GiftRegistry entity
     *
     * @return Mage_Customer_Model_Address
     */
    public function exportAddress()
    {
        $address = Mage::getModel('customer/address');
        $address->setData(unserialize($this->getData('shipping_address')));
        return $address;
    }

     /**
     * Sets up address data to the GiftRegistry entity  object
     *
     * @param Mage_Customer_Model_Address $address
     * @return $this
     */
    public function importAddress(Mage_Customer_Model_Address $address)
    {
        $data = array();
        $attributes = $address->getAttributes();
        foreach ($attributes as $attribute) {
            $data[$attribute->getAttributeCode()] = $address->getData($attribute->getAttributeCode());
        }
        $this->setData('shipping_address', serialize($data));
        return $this;
    }

    /**
     * Set type for Model using typeId
     * @param int $typeId
     * @return Enterprise_GiftRegistry_Model_Entity | false
     */
    public function setType($typeId) {
        $this->_typeId = (int) $typeId;
        $this->_type = Mage::getSingleton('enterprise_giftregistry/type');
        $this->_type->setStoreId(Mage::app()->getStore()->getStoreId());
        $this->setData('type_id', $typeId);
        $this->_type->load($this->_typeId);
        if ($this->_type->getId()) {
            $this->_attributes = $this->_type->getAttributes();
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Get Entity type id
     * @return int|null
     */
    public function getTypeId() {
        return $this->_typeId;
    }

    /**
     * Get Entity type Name
     * @return string|null
     */
    public function getTypeLabel() {
        if ($this->_type !== null) {
            return $this->_type->getLabel();
        }
        return null;
    }

    /**
     * Getter, returns all type custom attributes
     *
     * @return array
     */
    public function getCustomAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Getter, returns registrants custom attributes
     *
     * @return array
     */
    public function getRegistrantAttributes()
    {
        $attributes = $this->getCustomAttributes();
        return is_array($attributes) && !empty($attributes['registrant']) ? $attributes['registrant'] : array();
    }

    /**
     * Getter, return registry custom attributes
     *
     * @return array
     */
    public function getRegistryAttributes()
    {
        $attributes = $this->getCustomAttributes();
        return is_array($attributes) && !empty($attributes['registry']) ? $attributes['registry'] : array();
    }

    /**
     * Getter, return array of valid values for privacy field
     *
     * @return array
     */
    public function getOptionsIsPublic()
    {
        if (!isset($this->_optionsIsPublic)) {
            $this->_optionsIsPublic = array(
                '0' => Mage::helper('enterprise_giftregistry')->__('Private'),
                '1' => Mage::helper('enterprise_giftregistry')->__('Public'));
        }
        return $this->_optionsIsPublic;
    }

    /**
     * Validate entity attribute values
     *
     * @return bool
     */
    public function validate()
    {
        $errors = array();
        $helper = Mage::helper('enterprise_giftregistry');

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the title.');
        }

        if (!Zend_Validate::is($this->getMessage(), 'NotEmpty')) {
            $errors[] = $helper->__('Please enter the message.');
        }

        if (!key_exists($this->getIsPublic(), $this->_optionsIsPublic)) {
            $errors[] = $helper->__('Please enter correct Privacy setting.');
        }

        $customValues = $this->getCustomValues();
        $attributes = Mage::getSingleton('enterprise_giftregistry/entity')->getRegistryAttributes();
        $errors = $helper->validateCustomAttributes($customValues, $attributes);
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Import Post data
     *
     * @return this
     */
    public function importData ($data, $isAddAction = true)
    {
        $this->addData(array(
                'event_region_id' => !empty($data['event_region_id']) ? $data['event_region_id'] : null,
                'event_region' => !empty($data['event_region']) ? $data['event_region'] : null,
                'event_date' => !empty($data['event_date']) ? $data['event_date'] : null,
                'event_location' => !empty($data['event_location']) ? $data['event_location'] : null,
                'event_country_code' => !empty($data['event_country_code']) ? $data['event_country_code'] : null))

            ->addData(array(
//              'type_id' => $this->getTypeId(),
                'is_public' => !empty($data['is_public']) ? (bool) (int) $data['is_public'] : null,
                'title' => !empty($data['title']) ? $data['title'] : null,
                'message' => !empty($data['message']) ? $data['message'] : null,
                'custom_values' => !empty($data['registry']) ? $data['registry'] : null
            ));

        if ($isAddAction) {
            $this->addData(array(
                'customer_id' => Mage::getSingleton('customer/session')->getCustomer()->getId(),
                'website_id' => Mage::app()->getStore()->getWebsiteId(),
                'url_key' => $this->getGenerateKeyId(),
                'created_at' => Mage::getModel('core/date')->date(),
                'is_add_action' => true
            ));

        }
        return $this;
    }

    /**
     * Retrieve region name
     *
     * @return string
     */
    public function getRegion()
    {

        $regionId = $this->getData('region_id');
        $region   = $this->getData('region');
        $country_id = $this->getData('event_country_code');

        $regionModelId = Mage::getModel('directory/region')->load($regionId);
        $regionModelName = Mage::getModel('directory/region')->load($region);

        if ($regionId) {
            if ($regionModelId->getCountryId() == $country_id) {
               $region = $regionModelId->getName();
                $this->setData('region', $region);
            }
        }

        if (!empty($region) && is_string($region)) {
            $this->setData('region', $region);
        }
        elseif (!$regionId && is_numeric($region)) {
            if ($regionModelName->getCountryId() == $country_id) {
                $this->setData('region', $regionModelName->getName());
                $this->setData('region_id', $region);
            }
        }
        elseif ($regionId && !$region) {
               if ($regionModelId->getCountryId() == $country_id) {
                $this->setData('region', $regionModelId->getName());
            }
        }

        return $this->getData('region');
    }

    /**
     * Generate uniq url key
     *
     * @return string
     */
    public function getGenerateKeyId()
    {
        return Mage::helper('core')->uniqHash();
    }

    /**
     * Fetch array of custom date types fields id
     *
     * @return array
     */
    public function getCustomDateFields()
    {
        $dateFields = array();
        $attributes = $this->getRegistryAttributes();
        foreach ($attributes as $id => $attribute) {
            if ($attribute['type'] == 'date') {
                $dateFields[] = $id;
            }
        }
        return $dateFields;
    }
}
