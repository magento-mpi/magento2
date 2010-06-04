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
     * XML configuration paths
     */
    const XML_PATH_OWNER_EMAIL_IDENTITY  = 'enterprise_giftregistry/owner_email/identity';
    const XML_PATH_OWNER_EMAIL_TEMPLATE  = 'enterprise_giftregistry/owner_email/template';
    const XML_PATH_SHARE_EMAIL_IDENTITY  = 'enterprise_giftregistry/sharing_email/identity';
    const XML_PATH_SHARE_EMAIL_TEMPLATE  = 'enterprise_giftregistry/sharing_email/template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY = 'enterprise_giftregistry/update_email/identity';
    const XML_PATH_UPDATE_EMAIL_TEMPLATE = 'enterprise_giftregistry/update_email/template';

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

    const EXCEPTION_CODE_HAS_REQUIRED_OPTIONS = 916;

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
     * @param Varien_Object $request
     * @return Enterprise_GiftRegistry_Model_Item
     */
    public function addItem($itemToAdd, $request = null)
    {
        if ($itemToAdd instanceof Mage_Sales_Model_Quote_Item) {
            $productId = $itemToAdd->getProductId();
            $qty = $itemToAdd->getQty();
        } else {
            $productId = $itemToAdd;
            $qty = ($request && $request->getQty()) ? $request->getQty() : 1;
        }
        $product = $this->getProduct($productId);

        if ($product->getTypeInstance(true)->hasRequiredOptions($product)
            && (!$request && !($itemToAdd instanceof Mage_Sales_Model_Quote_Item))) {
            throw new Mage_Core_Exception(null, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        $item = Mage::getModel('enterprise_giftregistry/item');
        $item->loadByProductRegistry($this->getId(), $productId);

        if ($itemToAdd instanceof Mage_Sales_Model_Quote_Item) {
            $cartCandidate = $itemToAdd->getProduct();
            $cartCandidate->setCustomOptions($itemToAdd->getOptionsByCode());
        } else {
            if (!$request) {
                $request = new Varien_Object();
            }
            $cartCandidate = $product->getTypeInstance(true)->prepareForCart($request, $product);
            if (is_array($cartCandidate)) {
                $cartCandidate = array_shift($cartCandidate);
            }
        }

        $alreadyExists = false;
        if ($cartCandidate) {
            $items = $item->getCollection()->addRegistryFilter($this->getId());
            foreach ($items as $itemForCheck) {
                if ($itemForCheck->isRepresentProduct($cartCandidate)) {
                    $alreadyExists = true;
                    $item = $itemForCheck;
                    break;
                }
            }
        }

        if ($alreadyExists) {
            $item->setQty($item->getQty() + $qty)
                ->save();
        } else {
            $customOptions = $cartCandidate->getCustomOptions();
            $item = Mage::getModel('enterprise_giftregistry/item');
            $item->setEntityId($this->getId())
                ->setProductId($productId)
                ->setCustomOptions($customOptions['info_buyRequest']->getValue())
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
    public function sendShareRegistryEmail($recipient, $storeId, $message, $sender = null)
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $store = Mage::app()->getStore($this->getStoreId());
        $mail  = Mage::getModel('core/email_template');

        if (is_array($recipient)) {
            $recipientEmail = $recipient['email'];
            $recipientName = $recipient['name'];
        } else {
            $recipientEmail = $recipient;
            $recipientName = null;
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
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_SHARE_EMAIL_TEMPLATE),
            $identity,
            $recipientEmail,
            $recipientName,
            $templateVars
        );

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Send notification to owner on gift registry update -
     * gift registry items or their quantity purchased
     *
     * @return bool
     */
    public function sendUpdateRegistryEmail()
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $customer = Mage::getModel('customer/customer')
            ->load($this->getCustomerId());

        $store = Mage::app()->getStore($customer->getStoreId());
        $mail = Mage::getModel('core/email_template');

        $templateVars = array(
            'store'   => $store,
            'customer' => $customer
        );

        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE),
            $store->getConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY),
            $customer->getEmail(),
            $customer->getName(),
            $templateVars
        );

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Send notification to owner on successful creation of gift registry
     *
     * @return bool
     */
    public function sendNewRegistryEmail()
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $customer = Mage::getModel('customer/customer')
            ->load($this->getCustomerId());

        $store = Mage::app()->getStore($customer->getStoreId());
        $mail = Mage::getModel('core/email_template');

        $templateVars = array(
            'store'   => $store,
            'customer' => $customer
        );

        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_OWNER_EMAIL_TEMPLATE),
            $store->getConfig(self::XML_PATH_OWNER_EMAIL_IDENTITY),
            $customer->getEmail(),
            $customer->getName(),
            $templateVars
       );

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
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

        if ($collection->getSize()) {
            foreach ($collection as $entity) {
                return $entity;
            }
        } else {
            Mage::throwException(Mage::helper('enterprise_giftregistry')->__('There is no active gift registries.'));
        }
        return false;
    }

    /**
     * Load entity model by gift registry item id
     *
     * @param int $itemId
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function loadByEntityItem($itemId)
    {
        $this->_getResource()->loadByEntityItem($this, $itemId);
        return $this;
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
     * Return formated address data
     *
     * @return string
     */
    public function getFormatedShippingAddress()
    {
        return $this->exportAddress()->format('html');
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
        $skip = array('increment_id', 'entity_type_id', 'parent_id', 'entity_id', 'attribute_set_id');
        $data = array();
        $attributes = $address->getAttributes();
        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $skip)) {
                $data[$attribute->getAttributeCode()] = $address->getData($attribute->getAttributeCode());
            }
        }
        $this->setData('shipping_address', serialize($data));
        return $this;
    }

    /**
     * Set type for Model using typeId
     * @param int $typeId
     * @return Enterprise_GiftRegistry_Model_Entity | false
     */
    public function setTypeById($typeId) {
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
     * Retrieve item product instance
     *
     * @throws Mage_Core_Exception
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($productId)
    {
        $product = $this->_getData('product');
        if (is_null($product)) {
            if (!$productId) {
                Mage::throwException(Mage::helper('enterprise_giftregistry')->__('Cannot specify product.'));
            }

            $product = Mage::getModel('catalog/product')
                ->load($productId);

            $this->setData('product', $product);
        }
        return $product;
    }

    /**
     * Import Post data
     *
     * @return this
     */
    public function importData ($data, $isAddAction = true)
    {
        $this->addData(array(
                'event_region' => !empty($data['event_region']) ? $data['event_region'] : null,
                'event_region_text' => !empty($data['event_region_text']) ? $data['event_region_text'] : null,
                'event_date' => !empty($data['event_date']) ? $data['event_date'] : null,
                'event_location' => !empty($data['event_location']) ? $data['event_location'] : null,
                'event_country_code' => !empty($data['event_country_code']) ? $data['event_country_code'] : null))

            ->addData(array(
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
     * Fetches field value from Entity object
     * @param string $field
     * @return mixed
     */
    public function getFieldValue($field)
    {
        $data = $this->getData();
        $value = isset($data[$field]) ? $data[$field] : null;
        return $value;
    }

    /**
     * Retrieve region name
     *
     * @return string
     */
    public function getEventRegionText()
    {

        $regionId = $this->getData('event_region');
        $region   = $this->getData('event_region_text');
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
            $this->setData('event_region_text', $region);
        }
        elseif (!$regionId && is_numeric($region)) {
            if ($regionModelName->getCountryId() == $country_id) {
                $this->setData('event_region_text', $regionModelName->getName());
                $this->setData('event_region', $region);
            }
        }
        elseif ($regionId && !$region) {
               if ($regionModelId->getCountryId() == $country_id) {
                $this->setData('event_region_text', $regionModelId->getName());
            }
        }

        return $this->getData('event_region_text');
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

    /**
     * Custom handler for giftregistry share email action
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchShare($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        $change = Mage::getModel('enterprise_logging/event_changes');

        $emails = $request->getParam('emails', '');
        if ($emails) {
            $processor->addEventChanges(clone $change->setSourceName('share')
                ->setOriginalData(array())
                ->setResultData(array('emails' => $emails)));
        }

        $message = $request->getParam('message', '');
        if ($emails) {
            $processor->addEventChanges(clone $change->setSourceName('share')
                ->setOriginalData(array())
                ->setResultData(array('message' => $message)));
        }

        return $eventModel;
    }
}
