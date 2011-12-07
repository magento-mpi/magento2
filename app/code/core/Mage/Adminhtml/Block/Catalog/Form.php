<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Base block for rendering category and product forms
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Form_Renderer_Element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element')
        );
    }
}
