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
 * Payment configuration save handler factory
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandlerFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new payment configuration save handler based on given payment method ID
     *
     * @param string $paymentId
     * @param array $arguments
     * @return Mage_Launcher_Model_Storelauncher_Payments_PaymentSaveHandler
     * @throws Mage_Launcher_Exception
     */
    public function create($paymentId, array $arguments = array())
    {
        $saveHandlerMap = $this->getPaymentSaveHandlerMap();
        if (!isset($saveHandlerMap[$paymentId])) {
            throw new Mage_Launcher_Exception('Illegal payment method ID specified.');
        }
        return $this->_objectManager->create($saveHandlerMap[$paymentId], $arguments, false);
    }

    /**
     * Retrieve payment ID - save handler map
     *
     * @return array
     */
    public function getPaymentSaveHandlerMap()
    {
        return array(
            'paypal_express_checkout'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_ExpressCheckoutSaveHandler',
            'paypal_payflow_link'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowLinkSaveHandler',
            'paypal_payflow_pro'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PayflowProSaveHandler',
            'paypal_payments_advanced'
                 => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsAdvancedSaveHandler',
            'paypal_payments_pro'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsProSaveHandler',
            'paypal_payments_standard'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_PaymentsStandardSaveHandler',
            'authorize_net'
                => 'Mage_Launcher_Model_Storelauncher_Payments_Savehandlers_AuthorizenetSaveHandler',
        );
    }
}
