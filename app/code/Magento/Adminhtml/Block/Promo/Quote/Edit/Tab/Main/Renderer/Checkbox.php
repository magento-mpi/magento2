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
     * Checkbox render function
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $checkbox = new \Magento\Data\Form\Element\Checkbox($element->getData());
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
