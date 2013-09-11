<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity data model
 *
 * @method \Magento\GiftRegistry\Model\Resource\Entity _getResource()
 * @method \Magento\GiftRegistry\Model\Resource\Entity getResource()
 * @method \Magento\GiftRegistry\Model\Entity setTypeId(int $value)
 * @method int getCustomerId()
 * @method \Magento\GiftRegistry\Model\Entity setCustomerId(int $value)
 * @method int getWebsiteId()
 * @method \Magento\GiftRegistry\Model\Entity setWebsiteId(int $value)
 * @method int getIsPublic()
 * @method \Magento\GiftRegistry\Model\Entity setIsPublic(int $value)
 * @method string getUrlKey()
 * @method \Magento\GiftRegistry\Model\Entity setUrlKey(string $value)
 * @method string getTitle()
 * @method \Magento\GiftRegistry\Model\Entity setTitle(string $value)
 * @method string getMessage()
 * @method \Magento\GiftRegistry\Model\Entity setMessage(string $value)
 * @method string getShippingAddress()
 * @method \Magento\GiftRegistry\Model\Entity setShippingAddress(string $value)
 * @method string getCustomValues()
 * @method \Magento\GiftRegistry\Model\Entity setCustomValues(string $value)
 * @method int getIsActive()
 * @method \Magento\GiftRegistry\Model\Entity setIsActive(int $value)
 * @method string getCreatedAt()
 * @method \Magento\GiftRegistry\Model\Entity setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Model;

class Entity extends \Magento\Core\Model\AbstractModel
{
    /**
     * XML configuration paths
     */
    const XML_PATH_OWNER_EMAIL_IDENTITY  = 'magento_giftregistry/owner_email/identity';
    const XML_PATH_OWNER_EMAIL_TEMPLATE  = 'magento_giftregistry/owner_email/template';
    const XML_PATH_SHARE_EMAIL_IDENTITY  = 'magento_giftregistry/sharing_email/identity';
    const XML_PATH_SHARE_EMAIL_TEMPLATE  = 'magento_giftregistry/sharing_email/template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY = 'magento_giftregistry/update_email/identity';
    const XML_PATH_UPDATE_EMAIL_TEMPLATE = 'magento_giftregistry/update_email/template';

    /**
     * \Exception code
     */
    const EXCEPTION_CODE_HAS_REQUIRED_OPTIONS = 916;

    /**
     * Type object
     * @var \Magento\GiftRegistry\Model\Type
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
     * Helpers list
     *
     * @var array
     */
    protected $_helpers = array();

    /**
     * Store instance
     *
     * @var \Magento\Core\Model\Store
     */
    protected $_store;

    /**
     * Application instance
     *
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * Resource instance
     *
     * @var null
     */
    protected $_resource;

    /**
     * Translate instance
     *
     * @var \Magento\Core\Model\AbstractModel
     */
    protected $_translate;

    /**
     * @var Magento_Core_Model_Email_TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\App $application
     * @param \Magento\Core\Model\Store $store
     * @param \Magento\Core\Model\Translate $translate
     * @param Magento_Core_Model_Email_TemplateFactory $templateFactory
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\App $application,
        \Magento\Core\Model\Store $store,
        \Magento\Core\Model\Translate $translate,
        Magento_Core_Model_Email_TemplateFactory $templateFactory,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data= array()
    ) {
        $this->_app = $application;
        $this->_helpers = isset($data['helpers']) ? $data['helpers'] : array();
        $this->_store = $store;
        $this->_translate = $translate;
        $this->_templateFactory = $templateFactory;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     */
    protected function _construct()
    {
        $this->_init('\Magento\GiftRegistry\Model\Resource\Entity');
        parent::_construct();
    }

    /**
     * Get resource instance
     *
     * @return \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            return parent::_getResource();
        }
        return $this->_resource;
    }

    /**
     * Add items to registry
     *
     * @param array $itemsIds
     * @return int
     */
    public function addQuoteItems($itemsIds)
    {
        $skippedItems = 0;
        if (is_array($itemsIds)) {
            $quote = \Mage::getModel('\Magento\Sales\Model\Quote');
            $quote->setWebsite(\Mage::app()->getWebsite($this->getWebsiteId()));
            $quote->loadByCustomer(\Mage::getModel('\Magento\Customer\Model\Customer')->load($this->getCustomerId()));

            foreach ($quote->getAllVisibleItems() as $item) {
                if (in_array($item->getId(), $itemsIds)) {
                     if (!\Mage::helper('Magento\GiftRegistry\Helper\Data')->canAddToGiftRegistry($item)) {
                        $skippedItems++;
                        continue;
                    }
                    $this->addItem($item);
                }
            }
        }
        return $skippedItems;
    }

    /**
     * Add new product to registry
     *
     * @param int|\Magento\Sales\Model\Quote\Item $itemToAdd
     * @param null|\Magento\Object $request
     * @return false|\Magento\GiftRegistry\Model\Item
     * @throws \Magento\Core\Exception
     */
    public function addItem($itemToAdd, $request = null)
    {
        if ($itemToAdd instanceof \Magento\Sales\Model\Quote\Item) {
            $productId = $itemToAdd->getProductId();
            $qty = $itemToAdd->getQty();
        } else {
            $productId = $itemToAdd;
            $qty = ($request && $request->getQty()) ? $request->getQty() : 1;
        }
        $product = $this->getProduct($productId);

        if ($product->getTypeInstance()->hasRequiredOptions($product)
            && (!$request && !($itemToAdd instanceof \Magento\Sales\Model\Quote\Item))) {
            throw new \Magento\Core\Exception(null, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        if ($itemToAdd instanceof \Magento\Sales\Model\Quote\Item) {
            $cartCandidate = $itemToAdd->getProduct();
            $cartCandidate->setCustomOptions($itemToAdd->getOptionsByCode());
            $cartCandidates = array($cartCandidate);
        } else {
            if (!$request) {
                $request = new \Magento\Object();
                $request->setBundleOption(array());//Bundle options mocking for compatibility
            }
            $cartCandidates = $product->getTypeInstance()->prepareForCart($request, $product);
        }

        if (is_string($cartCandidates)) { //prepare process has error, seems like we have bundle
            throw new \Magento\Core\Exception($cartCandidates, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        $item = \Mage::getModel('\Magento\GiftRegistry\Model\Item');
        $items = $item->getCollection()->addRegistryFilter($this->getId());

        foreach ($cartCandidates as $currentCandidate) {
            if ($currentCandidate->getParentProductId()) {
                continue;
            }
            $alreadyExists = false;
            $productId = $currentCandidate->getId();

            foreach ($items as $itemForCheck) {
                if ($itemForCheck->isRepresentProduct($currentCandidate)) {
                    $alreadyExists = true;
                    $matchedItem = $itemForCheck;
                    break;
                }
            }

            $candidateQty = $currentCandidate->getCartQty();
            if (!empty($candidateQty)) {
                $qty = $candidateQty;
            }

            if ($alreadyExists) {
                $matchedItem->setQty($matchedItem->getQty() + $qty)
                    ->save();
            } else {
                $customOptions = $currentCandidate->getCustomOptions();

                $item = \Mage::getModel('\Magento\GiftRegistry\Model\Item');

                $item->setEntityId($this->getId())
                    ->setProductId($productId)
                    ->setOptions($customOptions)
                    ->setQty($qty)
                    ->save();
            }
        }

        return $item;
    }

    /**
     * Send share email
     *
     * @param string $recipient
     * @param int|null $storeId
     * @param string $message
     * @param null|array $sender
     * @return bool
     */
    public function sendShareRegistryEmail($recipient, $storeId, $message, $sender = null)
    {
        $translate = $this->_translate;
        $translate->setTranslateInline(false);

        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }
        $store = $this->_app->getStore($storeId);
        $mail = $this->_templateFactory->create();

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
            'store' => $store,
            'entity' => $this,
            'message' => $message,
            'recipient_name' => $recipientName,
            'url' => $this->_helper('\Magento\GiftRegistry\Helper\Data')->getRegistryLink($this)
        );

        $mail->setDesignConfig(array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => $store->getId()));
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
     * Send share emails
     *
     * @return \Magento\Object
     */
    public function sendShareRegistryEmails()
    {
        $senderMessage = $this->getSenderMessage();
        $senderName = $this->_helper('\Magento\GiftRegistry\Helper\Data')->escapeHtml($this->getSenderName());
        $senderEmail = $this->_helper('\Magento\GiftRegistry\Helper\Data')->escapeHtml($this->getSenderEmail());
        $result = new \Magento\Object(array('is_success' => false));

        if (empty($senderName) || empty($senderMessage) || empty($senderEmail)) {
            return $result->setErrorMessage(
                __('You need to enter sender data.')
            );
        }

        if (!Zend_Validate::is($senderEmail, 'EmailAddress')) {
            return $result->setErrorMessage(
                __('Please enter a valid sender email address.')
            );
        }

        $emails = array();
        foreach ($this->getRecipients() as $recipient) {
            $recipientEmail = trim($recipient['email']);
            if (!Zend_Validate::is($recipientEmail, 'EmailAddress')) {
                return $result->setErrorMessage(
                    __('Please enter a valid recipient email address.')
                );
            }

            $recipient['name'] = $this->_helper('\Magento\GiftRegistry\Helper\Data')->escapeHtml($recipient['name']);
            if (empty($recipient['name'])) {
                return $result->setErrorMessage(
                    __('Please enter a recipient name.')
                );
            }
            $emails[] = $recipient;
        }

        if (count($emails)) {
            $count = 0;
            $storeId = $this->_store->getId();

            foreach ($emails as $recipient) {
                $sender = array('name' => $senderName, 'email' => $senderEmail);
                if ($this->sendShareRegistryEmail($recipient, $storeId, $senderMessage, $sender)) {
                    $count++;
                }
            }
            if ($count > 0) {
                $result->setIsSuccess(true)
                    ->setSuccessMessage(
                        __('You shared the gift registry for %1 emails.', $count)
                    );
            } else {
                $result->setErrorMessage(
                    __("We couldn't share the registry.")
                );
            }
        }

        return $result;
    }

    /**
     * Send notification to owner on gift registry update -
     * gift registry items or their quantity purchased
     *
     * @param array $updatedQty
     * @return bool
     */
    public function sendUpdateRegistryEmail($updatedQty)
    {
        $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
        $translate->setTranslateInline(false);

        $owner = \Mage::getModel('\Magento\Customer\Model\Customer')
            ->load($this->getCustomerId());

        $store = \Mage::app()->getStore();
        $mail = $this->_templateFactory->create();

        $this->setUpdatedQty($updatedQty);

        $templateVars = array(
            'store' => $store,
            'owner' => $owner,
            'entity' => $this
        );

        $mail->setDesignConfig(array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => $store->getId()));
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE),
            $store->getConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY),
            $owner->getEmail(),
            $owner->getName(),
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
        $translate = \Mage::getSingleton('Magento\Core\Model\Translate');
        $translate->setTranslateInline(false);

        $owner = \Mage::getModel('\Magento\Customer\Model\Customer')
            ->load($this->getCustomerId());

        $store = \Mage::app()->getStore();
        $mail = $this->_templateFactory->create();

        $templateVars = array(
            'store' => $store,
            'owner' => $owner,
            'entity' => $this,
            'url' => \Mage::helper('Magento\GiftRegistry\Helper\Data')->getRegistryLink($this)
        );

        $mail->setDesignConfig(array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => $store->getId()));
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_OWNER_EMAIL_TEMPLATE),
            $store->getConfig(self::XML_PATH_OWNER_EMAIL_IDENTITY),
            $owner->getEmail(),
            $owner->getName(),
            $templateVars
       );

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Return comma-separated list of entity registrants
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
     * Return array of entity registrant roles
     *
     * @return string
     */
    public function getRegistrantRoles()
    {
        $collection = $this->getRegistrantsCollection();
        $roles = array();
        if ($collection->getSize()) {
            foreach($collection as $item) {
                $roles[] = $item->getRole();
            }
        }
        return $roles;
    }

    /**
     * Return entity registrants collection
     *
     * @return \Magento\GiftRegistry\Model\Resource\Person\Collection
     */
    public function getRegistrantsCollection()
    {
        $collection = \Mage::getModel('\Magento\GiftRegistry\Model\Person')->getCollection()
            ->addRegistryFilter($this->getId());

        return $collection;
    }

    /**
     * Return entity items collection
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function getItemsCollection()
    {
        $collection = \Mage::getModel('\Magento\GiftRegistry\Model\Item')->getCollection()
            ->addRegistryFilter($this->getId());
        return $collection;
    }

    /**
     * Load entity model by gift registry item id
     *
     * @param int $itemId
     * @return \Magento\GiftRegistry\Model\Entity
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
     * @return \Magento\GiftRegistry\Model\Entity
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
     * @return \Magento\Customer\Model\Address
     */
    public function exportAddress()
    {
        $address = \Mage::getModel('\Magento\Customer\Model\Address');
        $shippingAddressData = unserialize($this->getData('shipping_address'));
        if (is_array($shippingAddressData)) {
            $address->setData($shippingAddressData);
        }
        return $address;
    }

     /**
     * Sets up address data to the GiftRegistry entity  object
     *
     * @param \Magento\Customer\Model\Address $address
     * @return $this
     */
    public function importAddress(\Magento\Customer\Model\Address $address)
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
     * @return \Magento\GiftRegistry\Model\Entity | false
     */
    public function setTypeById($typeId) {
        $this->_typeId = (int) $typeId;
        $this->_type = \Mage::getSingleton('Magento\GiftRegistry\Model\Type');
        $this->_type->setStoreId(\Mage::app()->getStore()->getStoreId());
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
     * Getter, returns all ids for type custom attributes
     *
     * @return array
     */
    public function getStaticTypeIds()
    {
        return \Mage::getSingleton('Magento\GiftRegistry\Model\Attribute\Config')
            ->getStaticTypesCodes();
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
     * Getter, return registry attributes
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
                '0' => __('Private'),
                '1' => __('Public'));
        }
        return $this->_optionsIsPublic;
    }

    /**
     * Getter, return array of valid values for status field
     *
     * @return array
     */
    public function getOptionsStatus()
    {
        if (!isset($this->_optionsStatus)) {
            $this->_optionsStatus = array(
                '0' => __('Inactive'),
                '1' => __('Active'));
        }
        return $this->_optionsStatus;
    }

    /**
     * Validate entity attribute values
     *
     * @return array|bool
     */
    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = __('Please enter the title.');
        }

        if (!Zend_Validate::is($this->getMessage(), 'NotEmpty')) {
            $errors[] = __('Please enter the message.');
        }

        if (!Zend_Validate::is($this->getIsPublic(), 'NotEmpty')) {
            $errors[] = __('Please enter correct the Privacy setting.');
        } else if (!key_exists($this->getIsPublic(), $this->getOptionsIsPublic())) {
            $errors[] = __('Please enter correct the Privacy setting.');
        }

        $allCustomValues = $this->getCustomValues();
        foreach ($this->getStaticTypeIds() as $static) {
            if ($this->hasData($static)) {
                $allCustomValues[$static] = $this->getData($static);
            }
        }

        $errorsCustom = \Mage::helper('Magento\GiftRegistry\Helper\Data')->validateCustomAttributes(
            $allCustomValues, $this->getRegistryAttributes()
        );
        if ($errorsCustom !== true) {
            $errors = empty($errors) ? $errorsCustom : array_merge($errors, $errorsCustom);
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Retrieve item product instance
     *
     * @param int $productId
     * @return \Magento\Catalog\Model\Product|mixed
     */
    public function getProduct($productId)
    {
        $product = $this->_getData('product');
        if (is_null($product)) {
            if (!$productId) {
                \Mage::throwException(__('We cannot specify the product.'));
            }

            $product = \Mage::getModel('\Magento\Catalog\Model\Product')
                ->load($productId);

            $this->setData('product', $product);
        }
        return $product;
    }

    /**
     * Import POST data to entity model
     *
     * @param array $data
     * @param bool $isAddAction
     * @return this
     */
    public function importData($data, $isAddAction = true)
    {
        foreach ($this->getStaticTypeIds() as $code){
            if (isset($data[$code])) {
                $this->setData($code, $data[$code]);
            }
        }

        $this->addData(array(
                'is_public' => isset($data['is_public']) ? (int) $data['is_public'] : null,
                'title' => !empty($data['title']) ? $data['title'] : null,
                'message' => !empty($data['message']) ? $data['message'] : null,
                'custom_values' => !empty($data['registry']) ? $data['registry'] : null,
                'is_active' => !empty($data['is_active']) ? $data['is_active'] : 0,
            ));

        if ($isAddAction) {
            $this->addData(array(
                'customer_id' => \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getId(),
                'website_id' => \Mage::app()->getStore()->getWebsiteId(),
                'url_key' => $this->getGenerateKeyId(),
                'created_at' => \Mage::getModel('\Magento\Core\Model\Date')->date(),
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
        $value = null;
        if (isset($data[$field])) {
            $value = $data[$field];
        } else if (isset($data['custom_values']) && isset($data['custom_values'][$field])) {
            $value = $data['custom_values'][$field];
        }
        return $value;
    }

    /**
     * Generate uniq url key
     *
     * @return string
     */
    public function getGenerateKeyId()
    {
        return \Mage::helper('Magento\Core\Helper\Data')->uniqHash();
    }

    /**
     * Fetch array of custom date types fields id and their format
     *
     * @return array
     */
    public function getDateFieldArray()
    {
        if (!isset($this->_dateFields)) {
            $dateFields = array();
            $attributes = $this->getRegistryAttributes();
            foreach ($attributes as $id => $attribute) {
                if (isset($attribute['type']) && ($attribute['type'] == 'date') && isset($attribute['date_format'])) {
                    $dateFields[$id] = $attribute['date_format'];
                }
            }
            $this->_dateFields = $dateFields;
        }
        return $this->_dateFields;
    }

    /**
     * Custom handler for giftregistry share email action
     *
     * @param \Magento\Simplexml\Element $config
     * @param \Magento\Logging\Model\Event $eventModel
     * @return \Magento\Logging\Model\Event
     */
    public function postDispatchShare($config, $eventModel, $processor)
    {
        $request = \Mage::app()->getRequest();
        $change = \Mage::getModel('\Magento\Logging\Model\Event\Changes');

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

    /**
     * Load entity model by url key
     *
     * @param string $urlKey
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function loadByUrlKey($urlKey)
    {
        $this->_getResource()->loadByUrlKey($this, $urlKey);
        return $this;
    }

    /**
     * Validate gift registry items
     *
     * @param array $items
     * @throws \Magento\Exception
     */
    protected function _validateItems($items)
    {
        foreach ($items as $id => $item) {
            $model = \Mage::getSingleton('Magento\GiftRegistry\Model\Item')->load($id);
            if ($model->getId() && $model->getEntityId() == $this->getId()) {
                if (!isset($item['delete'])) {
                    /** @var $stockItem \Magento\CatalogInventory\Model\Stock\Item */
                    $stockItem = \Mage::getSingleton('Magento\CatalogInventory\Model\Stock\Item');
                    $stockItem->loadByProduct($model->getProductId());
                    // not \Magento\Core\Exception intentionally
                    if ($stockItem->getIsQtyDecimal() == 0 && $item['qty'] != (int)$item['qty']) {
                        throw new \Magento\Exception(__('Please correct the  gift registry item quantity.'));
                    }
                }
            } else {
                \Mage::throwException(
                    __('Please correct the gift registry item ID.')
                );
            }
        }
    }

    /**
     * Update gift registry items
     *
     * @param array $items
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function updateItems($items)
    {
        $this->_validateItems($items);
        foreach ($items as $id => $item) {
            $model = \Mage::getSingleton('Magento\GiftRegistry\Model\Item')->load($id);
            if (isset($item['delete'])) {
                $model->delete();
            } else {
                $model->setQty($item['qty']);
                $model->setNote($item['note']);
                $model->save();
            }
        }
        return $this;
    }

    /**
     * Retrieve helper by specified name
     *
     * @param string $name
     * @return \Magento\Core\Helper\AbstractHelper
     */
    protected function _helper($name)
    {
        if (!isset($this->_helpers[$name])) {
            $this->_helpers[$name] = \Mage::helper($name);
        }
        return $this->_helpers[$name];
    }
}
