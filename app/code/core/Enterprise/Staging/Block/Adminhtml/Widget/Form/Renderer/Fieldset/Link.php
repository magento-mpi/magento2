<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging link element renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Widget_Form_Renderer_Fieldset_Link extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{
    protected function _construct()
    {
        $this->setTemplate('Mage_Backend::widget/form/renderer/fieldset/element.phtml');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($this);

        $this->getType($element->getType());
        $this->setLabelHtml($this->_getLabelHtml($element));
        $this->setElementHtml($this->_getElementHtml($element));

        return $this->toHtml();
    }

    protected function _getLabelHtml($element)
    {
        return $element->getLabelHtml();
    }

    protected function _getElementHtml($element)
    {
        $link = $element->getValue();
        if ($element->getTitle()) {
            $title = $element->getTitle();
        } else {
            $title = $link;
        }

        if ($element->getLength() && strlen($title) > $element->getLength()) {
            $title = substr($title, 0, $element->getLength()) . '...';
        }

        $html = $element->getBold() ? '<strong>' : '';
        $html.= '<a href="'.$link.'" target="_stagingWebsite">'.$title.'</a>';
        $html.= $element->getBold() ? '</strong>' : '';
        $html.= $element->getAfterElementHtml();
        return $html;
    }
}
