<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Web_Protocol implements Magento_Core_Model_Option_ArrayInterface
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>''),
            array('value'=>'http', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('HTTP (unsecure)')),
            array('value'=>'https', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('HTTPS (SSL)')),
        );
    }

}
