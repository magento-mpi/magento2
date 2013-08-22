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
            array('value'=>'', 'label'=>__('No')),
            array('value'=>'opt', 'label'=>__('Optional')),
            array('value'=>'req', 'label'=>__('Required')),
        );
    }

}
