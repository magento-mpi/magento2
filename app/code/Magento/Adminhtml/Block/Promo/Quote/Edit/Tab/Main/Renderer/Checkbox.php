<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Renderer for specific checkbox that is used on Rule Information tab in Shopping cart price rules
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Promo_Quote_Edit_Tab_Main_Renderer_Checkbox
    extends Magento_Backend_Block_Abstract
    implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * @var Magento_Data_Form_ElementFactory
     */
    protected $_elementFactory;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Data_Form_ElementFactory $elementFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Data_Form_ElementFactory $elementFactory,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Checkbox render function
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $checkbox = $this->_elementFactory->create('Magento_Data_Form_Element_Checkbox', $element->getData());
        $checkbox->setForm($element->getForm());

        $elementHtml = sprintf(
            '<div class="field no-label field-%s with-note">'
                    . '<div class="control">'
                        . '<div class="nested">'
                            . '<div class="field choice"> %s'
                                .'<label class="label" for="%s">%s</label>'
                                . '<p class="note">%s</p>'
                            . '</div>'
                        . '</div>'
                    . '</div>'
                . '</div>',
            $element->getHtmlId(), $checkbox->getElementHtml(), $element->getHtmlId(), $element->getLabel(), $element->getNote()
        );
        return $elementHtml;
    }
}
