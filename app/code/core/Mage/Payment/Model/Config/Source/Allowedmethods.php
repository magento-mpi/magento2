<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Model_Config_Source_Allowedmethods
    extends Mage_Payment_Model_Config_Source_Allmethods
{
    protected function _getPaymentMethods()
    {
        return Mage::getSingleton('Mage_Payment_Model_Config')->getActiveMethods();
    }

//    public function toOptionArray()
//    {
//        $methods = array(array('value'=>'', 'label'=>''));
//        $payments = Mage::getSingleton('Mage_Payment_Model_Config')->getActiveMethods();
//        foreach ($payments as $paymentCode=>$paymentModel) {
//            $paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
//            $methods[$paymentCode] = array(
//                'label'   => $paymentTitle,
//                'value' => $paymentCode,
//            );
//        }
//
//        return $methods;
//    }
}
