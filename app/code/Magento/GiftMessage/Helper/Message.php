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
     * @var int
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
     * Pages to skip message checks
     *
     * @var array
     */
    protected $skipMessageCheck = array();

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\View\LayoutFactory $layoutFactory
     * @param \Magento\GiftMessage\Model\MessageFactory $giftMessageFactory
     * @param \Magento\Escaper $escaper
     * @param array $skipMessageCheck
     * @param bool $dbCompatibleMode
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Magento\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\View\LayoutFactory $layoutFactory,
        \Magento\GiftMessage\Model\MessageFactory $giftMessageFactory,
        \Magento\Escaper $escaper,
        $skipMessageCheck = array(),
        $dbCompatibleMode = true
    ) {
        $this->_escaper = $escaper;
        $this->_productFactory = $productFactory;
        $this->_layoutFactory = $layoutFactory;
        $this->_giftMessageFactory = $giftMessageFactory;
        $this->skipMessageCheck = $skipMessageCheck;
        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $appState,
            $priceCurrency,
            $dbCompatibleMode
        );
    }

    /**
     * Retrieve inline giftmessage edit form for specified entity
     *
     * @param string $type
     * @param \Magento\Object $entity
     * @param bool $dontDisplayContainer
     * @return string
     */
    public function getInline($type, \Magento\Object $entity, $dontDisplayContainer = false)
    {
        if (!$this->skipPage($type) && !$this->isMessagesAvailable($type, $entity)) {
            return '';
        }
        return $this->_layoutFactory->create()->createBlock('Magento\GiftMessage\Block\Message\Inline')
            ->setId('giftmessage_form_' . $this->_nextId++)
            ->setDontDisplayContainer($dontDisplayContainer)
            ->setEntity($entity)
            ->setType($type)->toHtml();
    }

    /**
     * @param string $pageType
     * @return bool
     */
    protected function skipPage($pageType)
    {
        return in_array($pageType, $this->skipMessageCheck);
    }

    /**
     * Check availability of giftmessages for specified entity.
     *
     * @param string $type
     * @param \Magento\Object $entity
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool|string|null
     */
    public function isMessagesAvailable($type, \Magento\Object $entity, $store = null)
    {
        if ($type == 'items') {
            $items = $entity->getAllItems();
            if (!is_array($items) || empty($items)) {
                return $this->_scopeConfig->getValue(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
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
            return $this->_getDependenceFromStoreConfig($entity->getProduct()->getGiftMessageAvailable(), $store);
        } elseif ($type == 'order_item') {
            return $this->_getDependenceFromStoreConfig($entity->getGiftMessageAvailable(), $store);
        } elseif ($type == 'address_item') {
            $storeId = is_numeric($store) ? $store : $this->_storeManager->getStore($store)->getId();
            if (!$this->isCached('address_item_' . $entity->getProductId())) {
                $this->setCached(
                    'address_item_' . $entity->getProductId(),
                    $this->_productFactory->create()->setStoreId(
                        $storeId
                    )->load(
                        $entity->getProductId()
                    )->getGiftMessageAvailable()
                );
            }
            return $this->_getDependenceFromStoreConfig(
                $this->getCached('address_item_' . $entity->getProductId()),
                $store
            );
        } else {
            return $this->_scopeConfig->getValue(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        }
        return false;
    }

    /**
     * Check availablity of gift messages from store config if flag eq 2.
     *
     * @param bool $productGiftMessageAllow
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool|string|null
     */
    protected function _getDependenceFromStoreConfig($productGiftMessageAllow, $store = null)
    {
        $result = $this->_scopeConfig->getValue(self::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
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
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool|null|string
     */
    public function getIsMessagesAvailable($type, \Magento\Object $entity, $store = null)
    {
        return $this->isMessagesAvailable($type, $entity, $store);
    }

    /**
     * Retrieve escaped and preformated gift message text for specified entity
     *
     * @param \Magento\Object $entity
     * @return string|null
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
     * @return mixed
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
     * @return bool
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
     * @return $this
     */
    public function setCached($key, $value)
    {
        $this->_innerCache[$key] = $value;
        return $this;
    }

    /**
     * Check availability for onepage checkout items
     *
     * @param array $quote
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
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
     * Check availability for multishipping checkout items
     *
     * @param array $items
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
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
     * @param int $messageId
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
