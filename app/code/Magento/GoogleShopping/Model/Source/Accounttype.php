<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Source;

/**
 * Google Data Api account types Source
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Accounttype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve option array with account types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'HOSTED_OR_GOOGLE', 'label' => __('Hosted or Google')),
            array('value' => 'GOOGLE', 'label' => __('Google')),
            array('value' => 'HOSTED', 'label' => __('Hosted'))
        );
    }
}
