<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Config_Source_Apply_On
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__('Custom price if available')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__('Original price only')),
        );
    }

}
