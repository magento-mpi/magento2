<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone template Action Dropdown source
 */
namespace Magento\Ogone\Model\Source;

class Template
{
    /**
     * Prepare ogone template mode list as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => \Magento\Ogone\Model\Api::TEMPLATE_OGONE, 'label' => __('Ogone')),
            array('value' => \Magento\Ogone\Model\Api::TEMPLATE_MAGENTO, 'label' => __('Magento')),
        );
    }
}
