<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Web;

class Protocol implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => ''),
            array('value' => 'http', 'label' => __('HTTP (unsecure)')),
            array('value' => 'https', 'label' => __('HTTPS (SSL)'))
        );
    }
}
