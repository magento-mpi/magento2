<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Tax_Model_System_Config_Source_Tax_Region implements Magento_Core_Model_Option_ArrayInterface
{
    protected $_options;

    public function toOptionArray($noEmpty=false, $country = null)
    {
        $options = Mage::getModel('Magento_Directory_Model_Region')
            ->getCollection()
            ->addCountryFilter($country)
            ->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } else {
            if ($options) {
                $options[0]['label'] = '*';
            } else {
                $options = array(array('value'=>'', 'label'=>'*'));
            }
        }

        return $options;
    }
}
