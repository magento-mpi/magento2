<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Cart_Product_Mark extends Magento_Core_Block_Template
{
    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function getEnabled()
    {
        return  Mage::helper('Enterprise_GiftRegistry_Helper_Data')->isEnabled();
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


        if ($item instanceof  Mage_Sales_Model_Quote_Address_Item) {
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
     * @param Mage_Sales_Model_Quote_Item $newItem
     * @return Enterprise_GiftRegistry_Block_Cart_Product_Mark
     */
    public function setItem($newItem)
    {
        if ($this->hasItem() && $this->getItem()->getId() == $newItem->getId()) {
            return $this;
        }

        if ($newItem->getGiftregistryItemId()) {
            $this->setData('item', $newItem);
            $entity = Mage::getModel('Enterprise_GiftRegistry_Model_Entity')->loadByEntityItem($newItem->getGiftregistryItemId());
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
        return $this->getUrl('enterprise_giftregistry/view/index', array('id' => $this->getEntity()->getUrlKey()));
    }
}
