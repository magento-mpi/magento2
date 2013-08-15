<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payments Tile Block
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Block_Adminhtml_Storelauncher_Payments_Tile extends Saas_Launcher_Block_Adminhtml_Tile
{
    /**
     * Get activated payment methods from store config
     *
     * @return array
     */
    public function getConfiguredMethods()
    {
        $result = array();
        $paymentSection = $this->_storeConfig->getConfig('payment');
        foreach ($this->_getPaymentMethods() as $path => $name) {
            if (isset($paymentSection[$path]['active']) && $paymentSection[$path]['active'] == 1) {
                $result[] = $name;
            }
        }

        return $result;
    }

    /**
     * Retrieve a list of payment methods related to 'Payments' tile
     *
     * @return array
     */
    protected function _getPaymentMethods()
    {
        return array(
            'paypal_express' => $this->__('PayPal Express Checkout'),
            'paypal_standard' => $this->__('PayPal Standard'),
            'payflow_advanced' => $this->__('PayPal Payments Advanced'),
            'paypal_direct' => $this->__('PayPal Payments Pro'),
            'payflow_link' => $this->__('PayPal Payflow Link'),
            'verisign' => $this->__('PayPal Payflow Pro'),
            'authorizenet' => $this->__('Authorize.net'),
        );
    }
}
