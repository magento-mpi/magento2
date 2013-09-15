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
namespace Magento\Adminhtml\Block\Promo\Quote\Edit\Tab\Main\Renderer;

class Checkbox
    extends \Magento\Backend\Block\AbstractBlock
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * @var Magento_Data_Form_Element_Factory
     */
    protected $_elementFactory;

    /**
     * @param Magento_Data_Form_Element_Factory $elementFactory
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $elementFactory,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * Checkbox render function
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        /** @var Magento_Data_Form_Element_Checkbox $checkbox */
        $checkbox = $this->_elementFactory->create('checkbox', array('attributes' => $element->getData()));
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
