<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Tax_Model_System_Config_Source_Tax_Region
{
    protected $_options;

    public function toOptionArray($noEmpty=false, $country = null)
    {
        $options = Mage::getModel('Mage_Directory_Model_Region')
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
