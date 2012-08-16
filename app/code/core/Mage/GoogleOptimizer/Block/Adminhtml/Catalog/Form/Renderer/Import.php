<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleOptimizer import controls renderer
 *
 * @category   Mage
 * @package    Mage_GoogleOptimizer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Form_Renderer_Import
    extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
{

    protected $_template = 'catalog/form/renderer/import.phtml';

    /**
     * Render form
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * Element setter
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return Mage_GoogleOptimizer_Block_Adminhtml_Catalog_Form_Renderer_Import
     */
    public function setElement(Varien_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Element getter
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }
}
