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
 * Front end helper block to add links
 */
class Enterprise_GiftRegistry_Block_Link extends Magento_Core_Block_Template
{
    /**
     * Adding link to dashboard links block
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return Enterprise_GiftRegistry_Block_Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if (Mage::helper('Enterprise_GiftRegistry_Helper_Data')->isEnabled()) {
            /** @var $blockInstance Magento_Customer_Block_Account_Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
