<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Helper;

/**
 * Gift Message helper
 */
class Message extends \Magento\Core\Helper\Data
{
    /**
     * Gift messages allow section in configuration
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
     * Inner cache
     *
     * @var array
     */
    protected $_innerCache = array();

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\View\LayoutFactory
     */
    protected $_layoutFactory;

    /**
     * @var \Magento\GiftMessage\Model\MessageFactory
     */
    protected $_giftMessageFactory;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\Core\Model\Date $dateModel
     * @param \Magento\App\State $appState
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\View\LayoutFactory $layoutFactory
     * @param \Magento\GiftMessage\Model\MessageFactory $giftMessageFactory
     * @param \Magento\Escaper $escaper
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\Core\Model\Date $dateModel,
        \Magento\App\State $appState,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\View\LayoutFactory $layoutFactory,
        \Magento\GiftMessage\Model\MessageFactory $giftMessageFactory,
        \Magento\Escaper $escaper,
        $dbCompatibleMode = true
    ) {
        $this->_escaper = $escaper;
        $this->_productFactory = $productFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_giftMessageFactory = $giftMessageFactory;
        parent::__construct(
            $context,
            $eventManager,
            $coreStoreConfig,
            $storeManager,
            $locale,
            $dateModel,
            $appState,
            $dbCompatibleMode
        );
    }

    /**
     * Retrieve inline giftmessage edit form for specified entity
     *
     * @param string $type
     * @param \Magento\Object $entity
     * @param boolean $dontDisplayContainer
     * @return string
     */
    public function getInline($type, \Magento\Object $entity, $dontDisplayContainer = false)
    {
        if (!in_array($type, array('onepage_checkout', 'multishipping_address'))
            && !$this->isMessagesAvailable($type, $entity)
        ) {
            return '';
        }
        return $this->_layoutFactory->create()->createBlock('Magento\GiftMessage\Block\Message\Inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    /**
     * Check availability of giftmessages for specified entity.
     *
     * @param string $type
     * @param \Magento\Object $entity
     * @param \Magento\Core\Model\Store|integer $store
     * @return boolean
     */
    public function isMessagesAvailable($type, \Magento\Object $entity, $store = null)
    {
        if ($type == 'items') {
            $items = $entity->getAllItems();
            if (!is_array($items) || empty($items)) {
                return $this->_coreStoreConfig->getConfig(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, $store);
            }
            if ($entity instanceof \Magento\Sales\Model\Quote) {
                $_type = $entity->getIsMultiShipping() ? 'address_item' : 'item';
            } else {
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
     * @param \Magento\Core\Model\Store|integer $store
     * @return boolean
     */
    protected function _getDependenceFromStoreConfig($productGiftMessageAllow, $store = null)
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
     * @param \Magento\Object $entity
     * @param \Magento\Core\Model\Store|integer $store
     * @return boolen
     */
    public function getIsMessagesAvailable($type, \Magento\Object $entity, $store = null)
    {
        return $this->isMessagesAvailable($type, $entity, $store);
    }

    /**
     * Retrieve escaped and preformated gift message text for specified entity
     *
     * @param \Magento\Object $entity
     * @return unknown
     */
    public function getEscapedGiftMessage(\Magento\Object $entity)
    {
        $message = $this->getGiftMessageForEntity($entity);
        if ($message) {
            return nl2br($this->_escaper->escapeHtml($message->getMessage()));
        }
        return null;
    }

    /**
     * Retrieve gift message for entity. If message not exists return null
     *
     * @param \Magento\Object $entity
     * @return \Magento\GiftMessage\Model\Message
     */
    public function getGiftMessageForEntity(\Magento\Object $entity)
    {
        if ($entity->getGiftMessageId() && !$entity->getGiftMessage()) {
            $message = $this->getGiftMessage($entity->getGiftMessageId());
            $entity->setGiftMessage($message);
        }
        return $entity->getGiftMessage();
    }

    /**
     * Retrieve internal cached data with specified key.
     *
     * If cached data not found return null.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getCached($key)
    {
        if ($this->isCached($key)) {
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
     * @return \Magento\GiftMessage\Helper\Message
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
     * @param \Magento\Core\Model\Store|integer $store
     * @return boolen
     */
    public function getAvailableForQuoteItems($quote, $store = null)
    {
        foreach ($quote->getAllItems() as $item) {
            if ($this->isMessagesAvailable('item', $item, $store)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check availability for multishiping checkout items
     *
     * @param array $items
     * @param \Magento\Core\Model\Store|integer $store
     * @return boolen
     */
    public function getAvailableForAddressItems($items, $store = null)
    {
        foreach ($items as $item) {
            if ($this->isMessagesAvailable('address_item', $item, $store)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve gift message with specified id
     *
     * @param integer $messageId
     * @return \Magento\GiftMessage\Model\Message
     */
    public function getGiftMessage($messageId = null)
    {
        $message = $this->_giftMessageFactory->create();
        if (!is_null($messageId)) {
            $message->load($messageId);
        }
        return $message;
    }
}
