<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Special Price Attribute Block
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

class Special extends \Magento\Adminhtml\Block\Catalog\Form\Renderer\Fieldset\Element
{
    public function getElementHtml()
    {
        $html = '<input id="'.$this->getElement()->getHtmlId().'" name="'.$this->getElement()->getName()
             .'" value="'.$this->getElement()->getEscapedValue().'" '.$this->getElement()->serialize($this->getElement()->getHtmlAttributes()).'/>'."\n"
             .'<label class="addafter" for="' . $this->getElement()->getHtmlId() . '"><strong>[%]</strong></label>';
        return $html;
    }
}
