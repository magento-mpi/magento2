<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Source_Payment_Allowedmethods
    extends Mage_Backend_Model_Config_Source_Payment_Allmethods
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
