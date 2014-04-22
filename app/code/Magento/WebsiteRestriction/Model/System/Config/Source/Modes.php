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
 * Sys config source model for restriction modes
 *
 */
class Modes extends \Magento\Object implements \Magento\Option\ArrayInterface
{
    /**
     * Get options for select
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\WebsiteRestriction\Model\Mode::ALLOW_NONE, 'label' => __('Website Closed')),
            array(
                'value' => \Magento\WebsiteRestriction\Model\Mode::ALLOW_LOGIN,
                'label' => __('Private Sales: Login Only')
            ),
            array(
                'value' => \Magento\WebsiteRestriction\Model\Mode::ALLOW_REGISTER,
                'label' => __('Private Sales: Login and Register')
            )
        );
    }
}
