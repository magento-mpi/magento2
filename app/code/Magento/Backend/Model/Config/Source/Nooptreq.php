<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source;

class Nooptreq implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label' => __('No')),
            array('value' => 'opt', 'label' => __('Optional')),
            array('value' => 'req', 'label' => __('Required'))
        );
    }
}
