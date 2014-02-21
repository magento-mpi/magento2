<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Source;

/**
 * Sys config source model for stub page statuses
 *
 */
class Http extends \Magento\Object implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_503,
                'label' => __('503 Service Unavailable'),
            ),
            array(
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_200,
                'label' => __('200 OK'),
            ),
        );
    }
}
