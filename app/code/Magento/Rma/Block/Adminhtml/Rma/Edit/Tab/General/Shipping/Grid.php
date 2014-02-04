<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\Shipping;

/**
 * Grid of packaging shipment
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Backend\Block\Template
{
    /**
     * Rma data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Rma\Helper\Data $rmaData
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Rma\Helper\Data $rmaData,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_rmaData = $rmaData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Return collection of shipment items
     *
     * @return array|bool
     */
    public function getCollection()
    {
        return $this->_coreRegistry->registry('current_rma')->getShippingMethods(true);
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = $this->_coreRegistry->registry('current_rma')->getStoreId();
        $order = $this->_coreRegistry->registry('current_rma')->getOrder();
        $address = $order->getShippingAddress();
        $shippingSourceCountryCode = $address->getCountryId();

        $shippingDestinationInfo = $this->_rmaData->getReturnAddressModel($storeId);
        $shippingDestinationCountryCode = $shippingDestinationInfo->getCountryId();

        if ($shippingSourceCountryCode != $shippingDestinationCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Format price
     *
     * @param   decimal $value
     * @return  double
     */
    public function formatPrice($value)
    {
        return sprintf('%.2F', $value);
    }
}
