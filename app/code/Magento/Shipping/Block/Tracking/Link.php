<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Block\Tracking;

/**
 * Tracking info link
 */
class Link extends \Magento\View\Element\Html\Link
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * Shipping data
     *
     * @var \Magento\Shipping\Helper\Data
     */
    protected $_shippingData;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Shipping\Helper\Data $shippingData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Shipping\Helper\Data $shippingData,
        array $data = array()
    ) {
        $this->_shippingData = $shippingData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Sales\Model\AbstractModel $model
     * @return string
     */
    public function getWindowUrl($model)
    {
        return $this->_shippingData->getTrackingPopupUrlBySalesModel($model);
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
}
