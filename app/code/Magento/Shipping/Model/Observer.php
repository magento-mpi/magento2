<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Model;

class Observer
{
    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_coreLocale;

    /**
     * @var \Magento\Sales\Model\Resource\Report\ShippingFactory
     */
    protected $_shippingFactory;

    /**
     * @param \Magento\Core\Model\LocaleInterface $coreLocale
     * @param \Magento\Sales\Model\Resource\Report\ShippingFactory $shippingFactory
     */
    public function __construct(
        \Magento\Core\Model\LocaleInterface $coreLocale,
        \Magento\Sales\Model\Resource\Report\ShippingFactory $shippingFactory
    ) {
        $this->_coreLocale = $coreLocale;
        $this->_shippingFactory = $shippingFactory;
    }

    /**
     * Refresh sales shipment report statistics for last day
     *
     * @return $this
     */
    public function aggregateSalesReportShipmentData()
    {
        $this->_coreLocale->emulate(0);
        $currentDate = $this->_coreLocale->date();
        $date = $currentDate->subHour(25);
        $this->_shippingFactory->create()->aggregate($date);
        $this->_coreLocale->revert();
        return $this;
    }
}
