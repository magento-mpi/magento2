<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Payment\Model\Config\Source;

class Allspecificcountries implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => __('All Allowed Countries')),
            array('value' => 1, 'label' => __('Specific Countries'))
        );
    }
}
