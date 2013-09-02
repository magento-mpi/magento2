<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form note element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Note extends Magento_Data_Form_Element_Abstract
{
    public function __construct(Magento_Data_Form_ElementFactory $elementFactory, $attributes = array())
    {
        parent::__construct($elementFactory, $attributes);
        $this->setType('note');
        //$this->setExtType('textfield');
    }

    public function getElementHtml()
    {
        $html = '<div id="' . $this->getHtmlId() . '" class="control-value">' . $this->getText() . '</div>';
        $html.= $this->getAfterElementHtml();
        return $html;
    }
}