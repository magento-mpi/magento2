<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV Entity Attribute Form Renderer Block for Boolean
 *
 * @category    Enterprise
 * @package     Enterprise_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Eav_Block_Form_Renderer_Boolean extends Enterprise_Eav_Block_Form_Renderer_Select
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
                'label' => Mage::helper('Enterprise_Eav_Helper_Data')->__('No')
            ),
            array(
                'value' => '1',
                'label' => Mage::helper('Enterprise_Eav_Helper_Data')->__('Yes')
            ),
        );
    }
}
