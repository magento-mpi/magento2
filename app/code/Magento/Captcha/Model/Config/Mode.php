<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha image model
 *
 * @category   Magento
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Captcha\Model\Config;

class Mode
{
    /**
     * Get options for captcha mode selection field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'label' => __('Always'),
                'value' => \Magento\Captcha\Helper\Data::MODE_ALWAYS
            ),
            array(
                'label' => __('After number of attempts to login'),
                'value' => \Magento\Captcha\Helper\Data::MODE_AFTER_FAIL
            ),
        );
    }
}
