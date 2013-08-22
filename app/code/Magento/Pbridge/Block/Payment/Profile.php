<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Payment Profiles Iframe block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Block_Payment_Profile extends Magento_Pbridge_Block_Iframe_Abstract
{
    /**
     * Default iframe height
     *
     * @var string
     */
    protected $_iframeHeight = '600';

    /**
     * Getter for Payment Profiles Iframe source URL.
     * Return Payment Bridge url with required parameters (such as merchant code, merchant key etc.)
     * Can include quote shipping and billing address if its required in payment processing
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return Mage::helper('Magento_Pbridge_Helper_Data')->getPaymentProfileUrl(
            array(
                'billing_address' => $this->_getAddressInfo(),
                'css_url'         => $this->getCssUrl(),
                'customer_id'     => $this->getCustomerIdentifier(),
                'customer_name'   => $this->getCustomerName(),
                'customer_email'  => $this->getCustomerEmail()
            )
        );
    }
}
