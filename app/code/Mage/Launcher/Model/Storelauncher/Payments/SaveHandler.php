<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save handler for Payments Tile
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Payments_SaveHandler extends Mage_Launcher_Model_Tile_MinimalSaveHandler
{
    /**
     * Payment information save handler factory
     *
     * @var Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory
     */
    protected $_saveHandlerFactory;

    /**
     * @param Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory $saveHandlerFactory
     */
    public function __construct(
        Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory $saveHandlerFactory
    ) {
        $this->_saveHandlerFactory = $saveHandlerFactory;
    }

    /**
     * Save data related to payment method
     *
     * @param array $data request data
     * @throws Mage_Launcher_Exception
     */
    public function savePaymentMethod(array $data)
    {
        $paymentMethodId = isset($data['payment_method']) ? (string)$data['payment_method'] : null;
        if (!in_array($paymentMethodId, $this->getRelatedPaymentMethods())) {
            throw new Mage_Launcher_Exception('Illegal payment method ID specified.');
        }
        $this->_saveHandlerFactory->create($paymentMethodId)->save($data);
    }

    /**
     * Retrieve a list of payment method IDs related to 'Payments' tile
     *
     * @return array
     */
    public function getRelatedPaymentMethods()
    {
        return array(
            'paypal_express_checkout',
            'paypal_payments_standard',
            'paypal_payments_advanced',
            'paypal_payments_pro',
            'paypal_payflow_link',
            'paypal_payflow_pro',
            'authorize_net',
        );
    }
}
