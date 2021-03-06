<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Source;

/**
 * Sys config source model for restriction modes
 *
 */
class Modes extends \Magento\Framework\Object implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE, 'label' => __('Website Closed')],
            [
                'value' => \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN,
                'label' => __('Private Sales: Login Only')
            ],
            [
                'value' => \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
                'label' => __('Private Sales: Login and Register')
            ]
        ];
    }
}
