<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

/**
 * EAV Entity Attribute Form Renderer Block for Boolean
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Boolean extends \Magento\CustomAttributeManagement\Block\Form\Renderer\Select
{
    /**
     * Return array of select options
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'label' => ''),
            array('value' => '0', 'label' => __('No')),
            array('value' => '1', 'label' => __('Yes'))
        );
    }
}
