<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Model\Resource\Grid;

class Statuses implements \Magento\Option\ArrayInterface
{
    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            \Magento\Logging\Model\Event::RESULT_SUCCESS => __('Success'),
            \Magento\Logging\Model\Event::RESULT_FAILURE => __('Failure'),
        );
    }
}
