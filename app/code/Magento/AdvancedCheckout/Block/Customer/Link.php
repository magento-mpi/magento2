<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend helper block to add links
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 */
class Magento_AdvancedCheckout_Block_Customer_Link extends Magento_Core_Block_Template
{
    /**
     * Adding link to dashboard links block
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return Magento_AdvancedCheckout_Block_Customer_Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if (Mage::helper('Magento_AdvancedCheckout_Helper_Data')->isSkuApplied()) {
            /** @var $blockInstance Magento_Customer_Block_Account_Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
