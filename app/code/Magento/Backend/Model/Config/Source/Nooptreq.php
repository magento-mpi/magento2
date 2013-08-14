<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Source_Nooptreq implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('No')),
            array('value'=>'opt', 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('Optional')),
            array('value'=>'req', 'label'=>Mage::helper('Magento_Backend_Helper_Data')->__('Required')),
        );
    }

}
