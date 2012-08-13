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
     * List of button's html attributes
     *
     * @var array
     */
    protected $_attributes = array();

    /**
     * Define block template
     */
    protected function _construct()
    {
        $this->setTemplate('Mage_Backend::widget/button.phtml');
        parent::_construct();
    }

    /**
     * Retrieve type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getData('type') ?: 'button';
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
     * Add html attribute
     *
     * @param string $attributeName
     * @param string $attributeValue
     * @return Mage_Backend_Block_Widget_Button
     */
    public function addAttribute($attributeName, $attributeValue)
    {
        $this->_attributes[$attributeName] = $attributeValue;
        return $this;
    }

    /**
     * Retrieve attributes html
     *
     * @return string
     */
    public function getAttributesHtml()
    {
        $html = '';
        foreach ($this->_attributes as $attributeKey => $attributeValue) {
            $html .= $attributeKey . '="'
                . $this->helper('Mage_Backend_Helper_Data')->quoteEscape($attributeValue) . '"';
        }

        return $html;
    }
}
