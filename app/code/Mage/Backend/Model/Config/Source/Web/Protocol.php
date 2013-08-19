<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Web_Protocol implements Mage_Core_Model_Option_ArrayInterface
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
