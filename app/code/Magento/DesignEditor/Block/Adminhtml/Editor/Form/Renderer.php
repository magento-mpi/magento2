<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Color-picker form element renderer
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer extends Magento_Backend_Block_Template
    implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element to render
     *
     * @var Magento_Data_Form_Element_Abstract
     */
    protected $_element;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template;

    /**
     * Get element renderer bound to
     *
     * @return Magento_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render form element as HTML
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}
