<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Usps\Block\Backend\Order\Plugin;

/**
 * Plugin class
 */
class DisplayGirth
{
    /**
     * Usps data helper
     *
     * @var \Magento\Usps\Helper\Data
     */
    protected $helper;

    /**
     * Construct
     *
     * @param \Magento\Usps\Helper\Data $helper
     */
    public function __construct(\Magento\Usps\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Is display girth value for specified shipping method
     *
     * @param \Magento\Shipping\Block\Adminhtml\Order\Packaging $subject
     * @param $result
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsDisplayGirthValue(\Magento\Shipping\Block\Adminhtml\Order\Packaging $subject, $result)
    {
        return $this->helper->displayGirthValue($subject->getShipment()->getOrder()->getShippingMethod());
    }
}
