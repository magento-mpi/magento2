<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form fieldset renderer
 */
class Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset
    extends Magento_Backend_Block_Template implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * Form element which re-rendering
     *
     * @var Magento_Data_Form_Element_Fieldset
     */
    protected $_element;

    protected $_template = 'store/switcher/form/renderer/fieldset.phtml';

    /**
     * Retrieve an element
     *
     * @return Magento_Data_Form_Element_Fieldset
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Render element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }

    /**
     * Return html for store switcher hint
     *
     * @return string
     */
    public function getHintHtml()
    {
        /** @var $storeSwitcher Magento_Backend_Block_Store_Switcher */
        $storeSwitcher = $this->_layout->getBlockSingleton('Magento_Backend_Block_Store_Switcher');
        return $storeSwitcher->getHintHtml();
    }
}
