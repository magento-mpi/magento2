<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Model_Config_Source_Cctype implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::getSingleton('Mage_Payment_Model_Config')->getCcTypes() as $code => $name) {
            $options[] = array(
               'value' => $code,
               'label' => $name
            );
        }

        return $options;
    }
}
