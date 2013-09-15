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

class Mark extends \Magento\Core\Block\Template
{
    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($coreData, $context, $data);
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
     * @return \Magento\GiftRegistry\Block\Cart\Product\Mark
     */
    public function setItem($newItem)
    {
        if ($this->hasItem() && $this->getItem()->getId() == $newItem->getId()) {
            return $this;
        }

        if ($newItem->getGiftregistryItemId()) {
            $this->setData('item', $newItem);
            $entity = \Mage::getModel('Magento\GiftRegistry\Model\Entity')->loadByEntityItem($newItem->getGiftregistryItemId());
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
