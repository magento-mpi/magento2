<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttributeManagement
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for Boolean
 *
 * @category    Magento
 * @package     Magento_CustomAttributeManagement
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

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
            array(
                'value' => '',
                'label' => ''
            ),
            array(
                'value' => '0',
                'label' => __('No')
            ),
            array(
                'value' => '1',
                'label' => __('Yes')
            ),
        );
    }
}
