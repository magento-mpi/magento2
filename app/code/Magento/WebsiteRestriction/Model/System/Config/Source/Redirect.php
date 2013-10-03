<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sys config source model for private sales redirect modes
 *
 */
namespace Magento\WebsiteRestriction\Model\System\Config\Source;

class Redirect extends \Magento\Object implements \Magento\Core\Model\Option\ArrayInterface
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
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_302_LOGIN,
                'label' => __('To login form (302 Found)'),
            ),
            array(
                'value' => \Magento\WebsiteRestriction\Model\Mode::HTTP_302_LANDING,
                'label' => __('To landing page (302 Found)'),
            ),
        );
    }
}
