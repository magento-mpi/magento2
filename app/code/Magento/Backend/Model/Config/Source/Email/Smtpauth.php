<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Email;

class Smtpauth implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'NONE', 'label' => 'NONE'),
            array('value' => 'PLAIN', 'label' => 'PLAIN'),
            array('value' => 'LOGIN', 'label' => 'LOGIN'),
            array('value' => 'CRAM-MD5', 'label' => 'CRAM-MD5')
        );
    }
}
