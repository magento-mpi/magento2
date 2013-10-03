<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Request Details Block at RMA page
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General;

class Details
    extends \Magento\Rma\Block\Adminhtml\Rma\Edit\Tab\General\AbstractGeneral
{
    /**
     * Get order link (href address)
     *
     * @return string
     */
    public function getOrderLink()
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$this->getOrder()->getId()));
    }

    /**
     * Get order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * Get Link to Customer's Page
     *
     * Gets address for link to customer's page.
     * Returns null for guest-checkout orders
     *
     * @return string|null
     */
    public function getCustomerLink()
    {
        if ($this->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return $this->getUrl('*/customer/edit', array('id' => $this->getOrder()->getCustomerId()));
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->escapeHtml($this->getOrder()->getCustomerEmail());
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getCustomerContactEmail()
    {
        return $this->escapeHtml($this->getRmaData('customer_custom_email'));
    }

}
