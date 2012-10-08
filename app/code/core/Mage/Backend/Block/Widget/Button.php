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
 * Button widget
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Button extends Mage_Backend_Block_Widget
{
    /**
     * Define block template
     */
    protected function _construct()
    {
        $this->setTemplate('Mage_Backend::widget/button.phtml');
        parent::_construct();
    }

    /**
     * Retrieve button type
     *
     * @return string
     */
    public function getType()
    {
        if (in_array($this->getData('type'), array('reset', 'submit'))) {
            return $this->getData('type');
        }
        return 'button';
    }

    /**
     * Retrieve onclick handler
     *
     * @return null|string
     */
    public function getOnClick()
    {
        return $this->getData('on_click') ?: $this->getData('onclick');
    }

    /**
     * Retrieve attributes html
     *
     * @return string
     */
    public function getAttributesHtml()
    {
        $attributes = array(
            'id'        => $this->getId(),
            'name'      => $this->getElementName(),
            'title'     => $this->getTitle() ? $this->getTitle() : $this->getLabel(),
            'type'      => $this->getType(),
            'class'     => 'scalable ' . $this->getClass() . ($this->getDisabled() ? ' disabled' : ''),
            'onclick'   => $this->getOnClick(),
            'style'     => $this->getStyle(),
            'value'     => $this->getValue(),
            'disabled'  => $this->getDisabled() ? 'disabled' : ''
        );

        $html = '';
        foreach ($attributes as $attributeKey => $attributeValue) {
            if ($attributeValue === null || $attributeValue == '') {
                continue;
            }
            $html .= $attributeKey . '="'
                . $this->helper('Mage_Backend_Helper_Data')->escapeHtml($attributeValue) . '" ';
        }

        return $html;
    }
}
