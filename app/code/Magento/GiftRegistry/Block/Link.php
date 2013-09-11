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
 * Front end helper block to add links
 */
namespace Magento\GiftRegistry\Block;

class Link extends \Magento\Core\Block\Template
{
    /**
     * Adding link to dashboard links block
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return \Magento\GiftRegistry\Block\Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if (\Mage::helper('Magento\GiftRegistry\Helper\Data')->isEnabled()) {
            /** @var $blockInstance \Magento\Customer\Block\Account\Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
