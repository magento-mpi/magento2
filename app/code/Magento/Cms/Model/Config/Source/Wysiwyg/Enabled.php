<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration source model for Wysiwyg toggling
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Cms\Model\Config\Source\Wysiwyg;

class Enabled implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\Cms\Model\Wysiwyg\Config::WYSIWYG_ENABLED,
                'label' => __('Enabled by Default')
            ),
            array(
                'value' => \Magento\Cms\Model\Wysiwyg\Config::WYSIWYG_HIDDEN,
                'label' => __('Disabled by Default')
            ),
            array(
                'value' => \Magento\Cms\Model\Wysiwyg\Config::WYSIWYG_DISABLED,
                'label' => __('Disabled Completely')
            )
        );
    }
}
