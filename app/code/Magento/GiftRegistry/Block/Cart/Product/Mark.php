<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Cart\Product;

class Mark extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\GiftRegistry\Model\EntityFactory
     */
    protected $entityFactory;

    /**
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\GiftRegistry\Model\EntityFactory $entityFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\GiftRegistry\Model\EntityFactory $entityFactory,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        $this->entityFactory = $entityFactory;
        parent::__construct($context, $data);
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate($value, array(
            'length' => $length,
            'etc' => $etc,
            'remainder' => $remainder,
            'breakWords' => $breakWords
        ));
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  $this->_giftRegistryData->isEnabled();
    }

    /**
     * Get current quote item from parent block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setData('item', null);
        $item = null;
        if ($this->getLayout()->getBlock('additional.product.info')) {
            $item = $this->getLayout()->getBlock('additional.product.info')->getItem();
        }


        if ($item instanceof  \Magento\Sales\Model\Quote\Address\Item) {
            $item = $item->getQuoteItem();
        }

        if (!$item || !$item->getGiftregistryItemId()) {
            return '';
        }

        $this->setItem($item);

        if (!$this->getEntity() || !$this->getEntity()->getId()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * Get gifregistry params by quote item
     *
     * @param \Magento\Sales\Model\Quote\Item $newItem
     * @return $this
     */
    public function setItem($newItem)
    {
        if ($this->hasItem() && $this->getItem()->getId() == $newItem->getId()) {
            return $this;
        }

        if ($newItem->getGiftregistryItemId()) {
            $this->setData('item', $newItem);
            $entity = $this->entityFactory->create()->loadByEntityItem($newItem->getGiftregistryItemId());
            $this->setEntity($entity);
        }

        return $this;
    }

    /**
     * Return current giftregistry title
     *
     * @return string
     */
    public function getGiftregistryTitle()
    {
        return $this->escapeHtml($this->getEntity()->getTitle());
    }

    /**
     * Return current giftregistry view url
     *
     * @return string
     */
    public function getGiftregistryUrl()
    {
        return $this->getUrl('magento_giftregistry/view/index', array('id' => $this->getEntity()->getUrlKey()));
    }
}
