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
 * Heading field block
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Blocks
 */
class Saas_PrintedTemplate_Block_Widget_Field_Heading
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    /**
     * Prepare heading html element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return Magento_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $element->setRenderer($this->_getRenderer());

        return $element;
    }

    /**
     * Get heading renderer object
     *
     * @return Magento_Data_Form_Element_Renderer_Interface
     */
    protected function _getRenderer()
    {
        return Mage::getBlockSingleton('Saas_PrintedTemplate_Block_Widget_Field_Heading_Renderer');
    }
}
