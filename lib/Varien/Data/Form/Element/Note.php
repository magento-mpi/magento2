<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form note element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Note extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
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