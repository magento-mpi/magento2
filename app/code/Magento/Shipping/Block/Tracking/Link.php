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
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * Shipping data
     *
     * @var \Magento\Shipping\Helper\Data
     */
    protected $_shippingData;

    /**
     * @var string;
     */
    protected $windowName;

    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Shipping\Helper\Data $shippingData,
        array $data = array()
    ) {
        $this->_shippingData = $shippingData;
        $this->_coreRegistry = $registry;
        $this->windowName = $data['windowName'];
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

    /**
     * @return string
     */
    public function getWindowName()
    {
        return $this->windowName;
    }
}
