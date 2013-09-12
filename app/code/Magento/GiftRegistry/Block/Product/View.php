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
 * Front end helper block to show GiftRegistry mark
 */
class Magento_GiftRegistry_Block_Product_View extends Magento_Catalog_Block_Product_View
{
    /**
     * GiftRegistry param flag value in url option params
     * @var string
     */
    const FLAG = 'giftregistry';

    /**
     * Set template to specified block
     *
     * @param string $blockName
     * @param string $template
     * @throws LogicException
     */
    public function setGiftRegistryTemplate($blockName, $template)
    {
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            throw new LogicException("Could not find block '$blockName'");
        }
        if ($this->_isGiftRegistryRedirect()) {
            $block->setTemplate($template);
        }
    }

    /**
     * Set GiftRegistry URL for the template
     *
     * @param string $blockName
     * @throws LogicException
     */
    public function setGiftRegistryUrl($blockName)
    {
        $block = $this->getLayout()->getBlock($blockName);
        if (!$block) {
            throw new LogicException("Could not find block '$blockName'");
        }
        if ($this->_isGiftRegistryRedirect()) {
            $block->setAddToGiftregistryUrl($this->getAddToGiftregistryUrl());
        }
    }

    /**
     * Return giftregistry add cart items url
     *
     * @return string
     */
    public function getAddToGiftregistryUrl()
    {
        return $this->getUrl('magento_giftregistry/index/cart',
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
