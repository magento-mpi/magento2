<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Nooptreq implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('No')),
            array('value'=>'opt', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Optional')),
            array('value'=>'req', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Required')),
        );
    }

}
