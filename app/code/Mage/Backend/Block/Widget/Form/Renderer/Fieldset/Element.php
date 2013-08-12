<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fieldset element renderer
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Form_Renderer_Fieldset_Element extends Mage_Backend_Block_Template
    implements Magento_Data_Form_Element_Renderer_Interface
{
    protected $_element;

    protected $_template = 'Mage_Backend::widget/form/renderer/fieldset/element.phtml';

    public function getElement()
    {
        return $this->_element;
    }

    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
}
