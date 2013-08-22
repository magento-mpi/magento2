<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Source_Design_Robots implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'INDEX,FOLLOW', 'label'=>'INDEX, FOLLOW'),
            array('value'=>'NOINDEX,FOLLOW', 'label'=>'NOINDEX, FOLLOW'),
            array('value'=>'INDEX,NOFOLLOW', 'label'=>'INDEX, NOFOLLOW'),
            array('value'=>'NOINDEX,NOFOLLOW', 'label'=>'NOINDEX, NOFOLLOW'),
        );
    }
}
