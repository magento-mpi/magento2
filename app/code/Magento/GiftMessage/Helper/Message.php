<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message helper
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Helper_Message extends Magento_Core_Helper_Data
{
    /**
     * Giftmessages allow section in configuration
     *
     */
    const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS = 'sales/gift_options/allow_items';
    const XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER = 'sales/gift_options/allow_order';

    /**
     * Next id for edit gift message block
     *
     * @var integer
     */
    protected $_nextId = 0;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * Inner cache
     *
     * @var array
     */
    protected $_innerCache = array();

    /**
     * @var Magento_Core_Model_LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var Magento_GiftMessage_Model_MessageFactory
     */
    protected $_giftMessageFactory;

    /**
     * @param Magento_GiftMessage_Model_MessageFactory $giftMessageFactory
     * @param Magento_Core_Model_LayoutFactory $layoutFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Locale $locale
     * @param Magento_Core_Model_Date $dateModel
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_Config_Resource $configResource
     */
    public function __construct(
        Magento_GiftMessage_Model_MessageFactory $giftMessageFactory,
        Magento_Core_Model_LayoutFactory $layoutFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Http $coreHttp,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Locale $locale,
        Magento_Core_Model_Date $dateModel,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_Config_Resource $configResource
    ) {
        $this->_productFactory = $productFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_storeManager = $storeManager;
        $this->_giftMessageFactory = $giftMessageFactory;
        parent::__construct(
            $eventManager,
            $coreHttp,
            $context,
            $config,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $dateModel,
            $appState,
            $configResource
        );
    }

    /**
     * Retrive inline giftmessage edit form for specified entity
     *
     * @param string $type
     * @param Magento_Object $entity
     * @param boolean $dontDisplayContainer
     * @return string
     */
    public function getInline($type, Magento_Object $entity, $dontDisplayContainer=false)
    {
        if (!in_array($type, array('onepage_checkout','multishipping_address'))
            && !$this->isMessagesAvailable($type, $entity)
        ) {
            return '';
        }

        return $this->_layoutFactory->create()->createBlock('Magento_GiftMessage_Block_Message_Inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    /**
     * Check availability of giftmessages for specified entity.
     *
     * @param string $type
     * @param Magento_Object $entity
     * @param Magento_Core_Model_Store|integer $store
     * @return boolean
     */
    public function isMessagesAvailable($type, Magento_Object $entity, $store = null)
    {
        if ($type == 'items') {
            $items = $entity->getAllItems();
            if(!is_array($items) || empty($items)) {
                return $this->_coreStoreConfig->getConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
            }
            if($entity instanceof Magento_Sales_Model_Quote) {
                $_type = $entity->getIsMultiShipping() ? 'address_item' : 'item';
            }
            else {
                $_type = 'order_item';
            }

            foreach ($items as $item) {
                if ($item->getParentItem()) {
                    continue;
                }
                if ($this->isMessagesAvailable($_type, $item, $store)) {
                    return true;
                }
            }
        } elseif ($type == 'item') {
            return $this->_getDependenceFromStoreConfig(
                $entity->getProduct()->getGiftMessageAvailable(),
                $store
            );
        } elseif ($type == 'order_item') {
            return $this->_getDependenceFromStoreConfig(
                $entity->getGiftMessageAvailable(),
                $store
            );
        } elseif ($type == 'address_item') {
            $storeId = is_numeric($store) ? $store : $this->_storeManager->getStore($store)->getId();

            if (!$this->isCached('address_item_' . $entity->getProductId())) {
                $this->setCached(
                    'address_item_' . $entity->getProductId(),
                    $this->_productFactory->create()
                        ->setStoreId($storeId)
                        ->load($entity->getProductId())
                        ->getGiftMessageAvailable()
                );
            }
            return $this->_getDependenceFromStoreConfig(
                $this->getCached('address_item_' . $entity->getProductId()),
                $store
            );
        } else {
            return $this->_coreStoreConfig->getConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER, $store);
        }

        return false;
    }

    /**
     * Check availablity of gift messages from store config if flag eq 2.
     *
     * @param int $productGiftMessageAllow
     * @param Magento_Core_Model_Store|integer $store
     * @return boolean
     */
    protected function _getDependenceFromStoreConfig($productGiftMessageAllow, $store=null)
    {
        $result = $this->_coreStoreConfig->getConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
        if ($productGiftMessageAllow === '' || is_null($productGiftMessageAllow)) {
            return $result;
        } else {
            return $productGiftMessageAllow;
        }
    }

    /**
     * Alias for isMessagesAvailable(...)
     *
     * @param string $type
     * @param Magento_Object $entity
     * @param Magento_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getIsMessagesAvailable($type, Magento_Object $entity, $store=null)
    {
        return $this->isMessagesAvailable($type, $entity, $store);
    }

    /**
     * Retrive escaped and preformated gift message text for specified entity
     *
     * @param Magento_Object $entity
     * @return unknown
     */
    public function getEscapedGiftMessage(Magento_Object $entity)
    {
        $message = $this->getGiftMessageForEntity($entity);
        if ($message) {
            return nl2br($this->escapeHtml($message->getMessage()));
        }
        return null;
    }

    /**
     * Retrive gift message for entity. If message not exists return null
     *
     * @param Magento_Object $entity
     * @return Magento_GiftMessage_Model_Message
     */
    public function getGiftMessageForEntity(Magento_Object $entity)
    {
        if($entity->getGiftMessageId() && !$entity->getGiftMessage()) {
            $message = $this->getGiftMessage($entity->getGiftMessageId());
            $entity->setGiftMessage($message);
        }
        return $entity->getGiftMessage();
    }

    /**
     * Retrive internal cached data with specified key.
     *
     * If cached data not found return null.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getCached($key)
    {
        if($this->isCached($key)) {
            return $this->_innerCache[$key];
        }

        return null;
    }

    /**
     * Check availability for internal cached data with specified key
     *
     * @param string $key
     * @return boolean
     */
    public function isCached($key)
    {
        return isset($this->_innerCache[$key]);
    }

    /**
     * Set internal cache data with specified key
     *
     * @param string $key
     * @param mixed $value
     * @return Magento_GiftMessage_Helper_Message
     */
    public function setCached($key, $value)
    {
        $this->_innerCache[$key] = $value;
        return $this;
    }

    /**
     * Check availability for onepage checkout items
     *
     * @param array $items
     * @param Magento_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getAvailableForQuoteItems($quote, $store=null)
    {
        foreach($quote->getAllItems() as $item) {
            if($this->isMessagesAvailable('item', $item, $store)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check availability for multishiping checkout items
     *
     * @param array $items
     * @param Magento_Core_Model_Store|integer $store
     * @return boolen
     */
    public function getAvailableForAddressItems($items, $store=null)
    {
        foreach($items as $item) {
            if($this->isMessagesAvailable('address_item', $item, $store)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrive gift message with specified id
     *
     * @param integer $messageId
     * @return Magento_GiftMessage_Model_Message
     */
    public function getGiftMessage($messageId=null)
    {
        $message = $this->_giftMessageFactory->create();
        if(!is_null($messageId)) {
            $message->load($messageId);
        }

        return $message;
    }

}
