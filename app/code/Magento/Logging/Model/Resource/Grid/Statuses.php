<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Logging\Model\Resource\Grid;

class Statuses implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            \Magento\Logging\Model\Event::RESULT_SUCCESS => __('Success'),
            \Magento\Logging\Model\Event::RESULT_FAILURE => __('Failure')
        ];
    }
}
