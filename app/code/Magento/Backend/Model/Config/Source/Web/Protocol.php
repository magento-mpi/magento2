<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Backend_Model_Config_Source_Web_Protocol implements Magento_Core_Model_Option_ArrayInterface
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>''),
            array('value'=>'http', 'label'=>__('HTTP (unsecure)')),
            array('value'=>'https', 'label'=>__('HTTPS (SSL)')),
        );
    }

}
