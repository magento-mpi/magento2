<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Request Details Block at RMA page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

class Shippingaddress extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\AbstractGeneral
{
    /**
     * Get order shipping address
     *
     * @return string|null
     */
    public function getOrderShippingAddress()
    {
        $address = $this->getOrder()->getShippingAddress();
        if ($address instanceof \Magento\Sales\Model\Order\Address) {
            return $address->format('html');
        } else {
            return null;
        }
    }
}
