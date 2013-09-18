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
     * Gift registry data
     *
     * @var \Magento\GiftRegistry\Helper\Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param \Magento\GiftRegistry\Helper\Data $giftRegistryData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\GiftRegistry\Helper\Data $giftRegistryData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;
        parent::__construct($coreData, $context, $data);
    }

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
        if ($this->_giftRegistryData->isEnabled()) {
            /** @var $blockInstance \Magento\Customer\Block\Account\Navigation */
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
