<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for Boolean
 *
 * @category    Magento
 * @package     Magento_CustomAttribute
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomAttribute_Block_Form_Renderer_Boolean extends Magento_CustomAttribute_Block_Form_Renderer_Select
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
