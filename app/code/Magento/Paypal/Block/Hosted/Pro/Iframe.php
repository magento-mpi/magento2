<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hosted Pro iframe block
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Block\Hosted\Pro;

class Iframe extends \Magento\Paypal\Block\Iframe
{
    /**
     * Internal constructor
     * Set payment method code
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_paymentMethodCode = \Magento\Paypal\Model\Config::METHOD_HOSTEDPRO;
    }

    /**
     * Get iframe action URL
     *
     * @return string
     */
    public function getFrameActionUrl()
    {
        return $this->_getOrder()
            ->getPayment()
            ->getAdditionalInformation('secure_form_url');
    }
}
