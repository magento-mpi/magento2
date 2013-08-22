<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Backend_Model_Config_Source_Email_Smtpauth implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'NONE', 'label'=>'NONE'),
            array('value'=>'PLAIN', 'label'=>'PLAIN'),
            array('value'=>'LOGIN', 'label'=>'LOGIN'),
            array('value'=>'CRAM-MD5', 'label'=>'CRAM-MD5'),
        );
    }
}
