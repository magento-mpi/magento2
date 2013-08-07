<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset element renderer
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Form_Renderer_Fieldset_Composite_Element
    extends Mage_Backend_Block_Template implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * Element to render
     *
     * @var Magento_Data_Form_Element_Abstract
     */
    protected $_element;


    /**
     * Initialize renderer
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::widget/form/renderer/fieldset/composite/element.phtml');
    }

    /**
     * Element getter
     *
     * @return Magento_Data_Form_Element_Abstract
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render element to html
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
