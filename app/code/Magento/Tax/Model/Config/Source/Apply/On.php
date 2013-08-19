<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Tax_Model_Config_Source_Apply_On implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>__('Custom price if available')),
            array('value'=>1, 'label'=>__('Original price only')),
        );
    }

}
