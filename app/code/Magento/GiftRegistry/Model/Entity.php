<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model;

/**
 * Entity data model
 *
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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Entity extends \Magento\Framework\Model\AbstractModel
{
    /**
     * XML configuration paths
     */
    const XML_PATH_OWNER_EMAIL_IDENTITY = 'magento_giftregistry/owner_email/identity';

    const XML_PATH_OWNER_EMAIL_TEMPLATE = 'magento_giftregistry/owner_email/template';

    const XML_PATH_SHARE_EMAIL_IDENTITY = 'magento_giftregistry/sharing_email/identity';

    const XML_PATH_SHARE_EMAIL_TEMPLATE = 'magento_giftregistry/sharing_email/template';

    const XML_PATH_UPDATE_EMAIL_IDENTITY = 'magento_giftregistry/update_email/identity';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE = 'magento_giftregistry/update_email/template';

    /**
     * \Exception code
     */
    const EXCEPTION_CODE_HAS_REQUIRED_OPTIONS = 916;

    /**
     * Type object
     * @var Type
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
     * @var \Magento\GiftRegistry\Model\Attribute\Config
     */
    protected $attributeConfig;

    /**
     * @var Item
     */
    protected $itemModel;

    /**
     * @var \Magento\CatalogInventory\Service\V1\StockItemService
     */
    protected $stockItemService;

    /**
     * Store instance
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * Resource instance
     *
     * @var null
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\GiftRegistry\Model\PersonFactory
     */
    protected $personFactory;

    /**
     * @var \Magento\GiftRegistry\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\GiftRegistry\Model\Type $type
     * @param \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig
     * @param Item $itemModel
     * @param \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\GiftRegistry\Model\PersonFactory $personFactory
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\GiftRegistry\Model\Resource\Entity $resource
     * @param \Magento\GiftRegistry\Model\Resource\Entity\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\GiftRegistry\Model\Type $type,
        \Magento\GiftRegistry\Model\Attribute\Config $attributeConfig,
        Item $itemModel,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\GiftRegistry\Model\PersonFactory $personFactory,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\GiftRegistry\Model\Resource\Entity $resource = null,
        \Magento\GiftRegistry\Model\Resource\Entity\Collection $resourceCollection = null,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->_store = $storeManager->getStore();
        $this->_transportBuilder = $transportBuilder;
        $this->_type = $type;
        $this->attributeConfig = $attributeConfig;
        $this->itemModel = $itemModel;
        $this->stockItemService = $stockItemService;
        $this->customerSession = $customerSession;
        $this->quoteFactory = $quoteFactory;
        $this->customerFactory = $customerFactory;
        $this->personFactory = $personFactory;
        $this->itemFactory = $itemFactory;
        $this->addressFactory = $addressFactory;
        $this->productFactory = $productFactory;
        $this->dateFactory = $dateFactory;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
        $this->mathRandom = $mathRandom;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->inlineTranslation = $inlineTranslation;
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftRegistry\Model\Resource\Entity');
        parent::_construct();
    }

    /**
     * Get resource instance
     *
     * @return \Magento\Framework\Model\Resource\Db\AbstractDb
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
            $quote = $this->quoteFactory->create();
            $quote->setWebsite($this->storeManager->getWebsite($this->getWebsiteId()));
            $quote->loadByCustomer($this->getCustomerId());

            foreach ($quote->getAllVisibleItems() as $item) {
                if (in_array($item->getId(), $itemsIds)) {
                    if (!$this->_giftRegistryData->canAddToGiftRegistry($item)) {
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
     * @param null|\Magento\Framework\Object $request
     * @return false|Item
     * @throws \Magento\Framework\Model\Exception
     */
    public function addItem($itemToAdd, $request = null)
    {
        if ($itemToAdd instanceof \Magento\Sales\Model\Quote\Item) {
            $productId = $itemToAdd->getProductId();
            $qty = $itemToAdd->getQty();
        } else {
            $productId = $itemToAdd;
            $qty = $request && $request->getQty() ? $request->getQty() : 1;
        }
        $product = $this->getProduct($productId);

        if ($product->getTypeInstance()->hasRequiredOptions(
            $product
        ) && (!$request && !$itemToAdd instanceof \Magento\Sales\Model\Quote\Item)
        ) {
            throw new \Magento\Framework\Model\Exception(null, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        if ($itemToAdd instanceof \Magento\Sales\Model\Quote\Item) {
            $cartCandidate = $itemToAdd->getProduct();
            $cartCandidate->setCustomOptions($itemToAdd->getOptionsByCode());
            $cartCandidates = array($cartCandidate);
        } else {
            if (!$request) {
                $request = new \Magento\Framework\Object();
                //Bundle options mocking for compatibility
                $request->setBundleOption(array());
            }
            $cartCandidates = $product->getTypeInstance()->prepareForCart($request, $product);
        }

        if (is_string($cartCandidates)) {
            //prepare process has error, seems like we have bundle
            throw new \Magento\Framework\Model\Exception($cartCandidates, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        $item = $this->itemFactory->create();
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
                $matchedItem->setQty($matchedItem->getQty() + $qty)->save();
            } else {
                $customOptions = $currentCandidate->getCustomOptions();

                $item = $this->itemFactory->create();

                $item->setEntityId(
                    $this->getId()
                )->setProductId(
                    $productId
                )->setOptions(
                    $customOptions
                )->setQty(
                    $qty
                )->save();
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
        $this->inlineTranslation->suspend();

        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }
        $store = $this->storeManager->getStore($storeId);

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
            $identity = $this->_scopeConfig->getValue(
                self::XML_PATH_SHARE_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );
        }

        $templateIdentifier = $this->_scopeConfig->getValue(
            self::XML_PATH_SHARE_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $templateVars = array(
            'store' => $store,
            'entity' => $this,
            'message' => $message,
            'recipient_name' => $recipientName,
            'url' => $this->_giftRegistryData->getRegistryLink($this)
        );

        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $templateIdentifier
        )->setTemplateOptions(
            array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId())
        )->setTemplateVars(
            $templateVars
        )->setFrom(
            $identity
        )->addTo(
            $recipientEmail,
            $recipientName
        )->getTransport();

        try {
            $transport->sendMessage();
            $result = true;
        } catch (\Magento\Framework\Mail\Exception $e) {
            $result = false;
        }

        $this->inlineTranslation->resume();

        return $result;
    }

    /**
     * Send share emails
     *
     * @return \Magento\Framework\Object
     */
    public function sendShareRegistryEmails()
    {
        $senderMessage = $this->getSenderMessage();
        $senderName = $this->_escaper->escapeHtml($this->getSenderName());
        $senderEmail = $this->_escaper->escapeHtml($this->getSenderEmail());
        $result = new \Magento\Framework\Object(array('is_success' => false));

        if (empty($senderName) || empty($senderMessage) || empty($senderEmail)) {
            return $result->setErrorMessage(__('You need to enter sender data.'));
        }

        if (!\Zend_Validate::is($senderEmail, 'EmailAddress')) {
            return $result->setErrorMessage(__('Please enter a valid sender email address.'));
        }

        $emails = array();
        foreach ($this->getRecipients() as $recipient) {
            $recipientEmail = trim($recipient['email']);
            if (!\Zend_Validate::is($recipientEmail, 'EmailAddress')) {
                return $result->setErrorMessage(__('Please enter a valid recipient email address.'));
            }

            $recipient['name'] = $this->_escaper->escapeHtml($recipient['name']);
            if (empty($recipient['name'])) {
                return $result->setErrorMessage(__('Please enter a recipient name.'));
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
                $result->setIsSuccess(
                    true
                )->setSuccessMessage(
                    __('You shared the gift registry for %1 emails.', $count)
                );
            } else {
                $result->setErrorMessage(__("We couldn't share the registry."));
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
        $this->inlineTranslation->suspend();

        $owner = $this->customerFactory->create()->load($this->getCustomerId());

        $store = $this->storeManager->getStore();

        $this->setUpdatedQty($updatedQty);

        $templateVars = array('store' => $store, 'owner' => $owner, 'entity' => $this);

        $templateIdentifier = $this->_scopeConfig->getValue(
            self::XML_PATH_UPDATE_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $from = $this->_scopeConfig->getValue(
            self::XML_PATH_UPDATE_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $templateIdentifier
        )->setTemplateOptions(
            array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId())
        )->setTemplateVars(
            $templateVars
        )->setFrom(
            $from
        )->addTo(
            $owner->getEmail(),
            $owner->getName()
        )->getTransport();

        try {
            $transport->sendMessage();
            $result = true;
        } catch (\Magento\Framework\Mail\Exception $e) {
            $result = false;
        }

        $this->inlineTranslation->resume();

        return $result;
    }

    /**
     * Send notification to owner on successful creation of gift registry
     *
     * @return bool
     */
    public function sendNewRegistryEmail()
    {
        $this->inlineTranslation->suspend();

        $owner = $this->customerFactory->create()->load($this->getCustomerId());

        $store = $this->storeManager->getStore();

        $templateVars = array(
            'store' => $store,
            'owner' => $owner,
            'entity' => $this,
            'url' => $this->_giftRegistryData->getRegistryLink($this)
        );

        $templateIdentifier = $this->_scopeConfig->getValue(
            self::XML_PATH_OWNER_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $from = $this->_scopeConfig->getValue(
            self::XML_PATH_OWNER_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $templateIdentifier
        )->setTemplateOptions(
            array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $store->getId())
        )->setTemplateVars(
            $templateVars
        )->setFrom(
            $from
        )->addTo(
            $owner->getEmail(),
            $owner->getName()
        )->getTransport();

        try {
            $transport->sendMessage();
            $result = true;
        } catch (\Magento\Framework\Mail\Exception $e) {
            $result = false;
        }

        $this->inlineTranslation->resume();

        return $result;
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
            foreach ($collection as $item) {
                $registrants[] = $item->getFirstname() . ' ' . $item->getLastname();
            }
            return implode(', ', $registrants);
        }
        return '';
    }

    /**
     * Return array of entity registrant roles
     *
     * @return array
     */
    public function getRegistrantRoles()
    {
        $collection = $this->getRegistrantsCollection();
        $roles = array();
        if ($collection->getSize()) {
            foreach ($collection as $item) {
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
        $collection = $this->personFactory->create()->getCollection()->addRegistryFilter($this->getId());

        return $collection;
    }

    /**
     * Return entity items collection
     *
     * @return \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    public function getItemsCollection()
    {
        $collection = $this->itemFactory->create()->getCollection()->addRegistryFilter($this->getId());
        return $collection;
    }

    /**
     * Load entity model by gift registry item id
     *
     * @param int $itemId
     * @return $this
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
     * @return $this
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
        $address = $this->addressFactory->create();
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
     *
     * @param int $typeId
     * @return $this|false
     */
    public function setTypeById($typeId)
    {
        $this->_typeId = (int)$typeId;
        $this->_type->setStoreId($this->storeManager->getStore()->getStoreId());
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
    public function getTypeId()
    {
        return $this->_typeId;
    }

    /**
     * Get Entity type Name
     * @return string|null
     */
    public function getTypeLabel()
    {
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
        return $this->attributeConfig->getStaticTypesCodes();
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
            $this->_optionsIsPublic = array('0' => __('Private'), '1' => __('Public'));
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
            $this->_optionsStatus = array('0' => __('Inactive'), '1' => __('Active'));
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

        if (!\Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = __('Please enter the title.');
        }

        if (!\Zend_Validate::is($this->getMessage(), 'NotEmpty')) {
            $errors[] = __('Please enter the message.');
        }

        if (!\Zend_Validate::is($this->getIsPublic(), 'NotEmpty')) {
            $errors[] = __('Please enter correct the Privacy setting.');
        } else {
            if (!array_key_exists($this->getIsPublic(), $this->getOptionsIsPublic())) {
                $errors[] = __('Please enter correct the Privacy setting.');
            }
        }

        $allCustomValues = $this->getCustomValues();
        foreach ($this->getStaticTypeIds() as $static) {
            if ($this->hasData($static)) {
                $allCustomValues[$static] = $this->getData($static);
            }
        }

        $errorsCustom = $this->_giftRegistryData->validateCustomAttributes(
            $allCustomValues,
            $this->getRegistryAttributes()
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
                throw new \Magento\Framework\Model\Exception(__('We cannot specify the product.'));
            }

            $product = $this->productFactory->create()->load($productId);

            $this->setData('product', $product);
        }
        return $product;
    }

    /**
     * Import POST data to entity model
     *
     * @param array $data
     * @param bool $isAddAction
     * @return $this
     */
    public function importData($data, $isAddAction = true)
    {
        foreach ($this->getStaticTypeIds() as $code) {
            if (isset($data[$code])) {
                $this->setData($code, $data[$code]);
            }
        }

        $this->addData(
            array(
                'is_public' => isset($data['is_public']) ? (int)$data['is_public'] : null,
                'title' => !empty($data['title']) ? $data['title'] : null,
                'message' => !empty($data['message']) ? $data['message'] : null,
                'custom_values' => !empty($data['registry']) ? $data['registry'] : null,
                'is_active' => !empty($data['is_active']) ? $data['is_active'] : 0
            )
        );

        if ($isAddAction) {
            $this->addData(
                array(
                    'customer_id' => $this->customerSession->getCustomer()->getId(),
                    'website_id' => $this->storeManager->getStore()->getWebsiteId(),
                    'url_key' => $this->getGenerateKeyId(),
                    'created_at' => $this->dateFactory->create()->date(),
                    'is_add_action' => true
                )
            );
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
        } else {
            if (isset($data['custom_values']) && isset($data['custom_values'][$field])) {
                $value = $data['custom_values'][$field];
            }
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
        return $this->mathRandom->getUniqueHash();
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
                if (isset($attribute['type']) && $attribute['type'] == 'date' && isset($attribute['date_format'])) {
                    $dateFields[$id] = $attribute['date_format'];
                }
            }
            $this->_dateFields = $dateFields;
        }
        return $this->_dateFields;
    }

    /**
     * Load entity model by url key
     *
     * @param string $urlKey
     * @return $this
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
     * @return void
     * @throws \Magento\Framework\Exception
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _validateItems($items)
    {
        foreach ($items as $id => $item) {
            $model = $this->itemModel->load($id);
            if ($model->getId() && $model->getEntityId() == $this->getId()) {
                if (!isset($item['delete'])) {
                    $stockItemDo = $this->stockItemService->getStockItem($model->getProductId());
                    // not \Magento\Framework\Model\Exception intentionally
                    if ($stockItemDo->getIsQtyDecimal() == 0 && $item['qty'] != (int)$item['qty']) {
                        throw new \Magento\Framework\Exception(__('Please correct the  gift registry item quantity.'));
                    }
                }
            } else {
                throw new \Magento\Framework\Model\Exception(__('Please correct the gift registry item ID.'));
            }
        }
    }

    /**
     * Update gift registry items
     *
     * @param array $items
     * @return $this
     */
    public function updateItems($items)
    {
        $this->_validateItems($items);
        foreach ($items as $id => $item) {
            $model = $this->itemModel->load($id);
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
}
