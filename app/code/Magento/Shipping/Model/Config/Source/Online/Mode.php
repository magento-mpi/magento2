<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Model\Config\Source\Online;

use Magento\Data\OptionSourceInterface;

/**
 * Shippers Modesource model
 */
class Mode implements OptionSourceInterface
{
    /**
     * Returns array to be used in packages request type on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => __('Development')),
            array('value' => '1', 'label' => __('Live')),
        );
    }
}
