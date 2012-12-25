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
 * Renderer for header field
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Field_Heading_Renderer
    extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    /**
     * Render heading element
     *
     * @see Varien_Data_Form_Element_Renderer_Interface::render()
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
       return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="2"><h4 id="%s">%s</h4></td></tr>',
           $element->getHtmlId(), $element->getHtmlId(), $this->escapeHtml($element->getLabel())
       );
    }
}
