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
 * TaxGrid helper block
 *
 * Inserts entity property to widge template and allows widget only for printed template
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_TaxGrid_Editor
    extends Mage_Backend_Block_Template
    implements Mage_Widget_Block_Interface
{
    /**
     * Set template for the block
     */
    public function _construct()
    {
        $this->setTemplate('Saas_PrintedTemplate::widget/tax_grid/editor.phtml');
    }

    /**
     * Add additional HTML to element
     *
     * @param Magento_Data_Form_Element_Abstract $element Form Element
     * @return Magento_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        return $element->setAfterElementHtml($this->toHtml());
    }
}
