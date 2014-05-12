<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model;

/**
 * User statuses option array
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class MassOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => '', 'value' => ''),
            array('label' => __('Enabled'), 'value' => '1'),
            array('label' => __('Disabled'), 'value' => '0')
        );
    }
}
