<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Model_System_Config_Source_Nooptreq
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('No')),
            array('value'=>'opt', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Optional')),
            array('value'=>'req', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Required')),
        );
    }

}
