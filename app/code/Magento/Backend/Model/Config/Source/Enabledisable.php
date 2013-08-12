<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Source_Enabledisable implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('Enable')),
            array('value'=>0, 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('Disable')),
        );
    }
}
