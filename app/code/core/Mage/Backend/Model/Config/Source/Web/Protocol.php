<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Web_Protocol
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>''),
            array('value'=>'http', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('HTTP (unsecure)')),
            array('value'=>'https', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('HTTPS (SSL)')),
        );
    }

}
