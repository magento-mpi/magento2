<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Enabledisable
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Enable')),
            array('value'=>0, 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Disable')),
        );
    }
}
