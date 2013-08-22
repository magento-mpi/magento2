<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Source_Web_Redirect implements Magento_Core_Model_Option_ArrayInterface
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>__('No')),
            array('value' => 1, 'label'=>__('Yes (302 Found)')),
            array('value' => 301, 'label'=>__('Yes (301 Moved Permanently)')),
        );
    }

}
