<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to show giftregistry mark
 */
class Enterprise_GiftRegistry_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * Giftregistry param flag value in url option params
     * @var string
     */
    const FLAG = 'giftregistry';

    /**
     * Prepare layout
     *
     * @return Enterprise_GiftRegistry_Block_Product_View
     */
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->getBlock('customize.button');
        if ($block && $this->_isGiftRegistryRedirect()) {
            $block->setTemplate('Enterprise_GiftRegistry::product/customize.phtml');
        }

        $block = $this->getLayout()->getBlock('product.info.addtocart');
        if ($block && $this->_isGiftRegistryRedirect()) {
            $block->setTemplate('Enterprise_GiftRegistry::product/addtocart.phtml');
            $block->setAddToGiftregistryUrl($this->getAddToGiftregistryUrl());
        }
        return parent::_prepareLayout();
    }

    /**
     * Return giftregistry add cart items url
     *
     * @return string
     */
    public function getAddToGiftregistryUrl()
    {
        return $this->getUrl('enterprise_giftregistry/index/cart',
            array('entity' => $this->getRequest()->getParam('entity')));
    }

    /**
     * Return gift registry redirect flag.
     *
     * @return bool
     */
    protected function _isGiftRegistryRedirect()
    {
        return $this->getRequest()->getParam('options') == self::FLAG;
    }
}
