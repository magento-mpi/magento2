<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Source;

/**
 * Sys config source model for stub page statuses
 *
 */
class Http extends \Magento\Framework\Object implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_503,
                'label' => __('503 Service Unavailable'),
            ],
            ['value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_200, 'label' => __('200 OK')]
        ];
    }
}
