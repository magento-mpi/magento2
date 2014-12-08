<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Source;

/**
 * Sys config source model for private sales redirect modes
 *
 */
class Redirect extends \Magento\Framework\Object implements \Magento\Framework\Option\ArrayInterface
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
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_302_LOGIN,
                'label' => __('To login form (302 Found)'),
            ],
            [
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_302_LANDING,
                'label' => __('To landing page (302 Found)')
            ]
        ];
    }
}
