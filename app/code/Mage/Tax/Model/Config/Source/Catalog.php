<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Config_Source_Catalog implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>__('No (price without tax)')),
            array('value'=>1, 'label'=>__('Yes (only price with tax)')),
            array('value'=>2, 'label'=>__("Both (without and with tax)")),
        );
    }

}
