<?php
/**
 * Form textarea element
 *
 * @package    Varien
 * @subpackage Form
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Textarea extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('textarea');
        $this->setExtType('textarea');
    }

    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'rows', 'cols');
    }

    public function getElementHtml()
    {
        $this->addClass('textarea');
        $html = '';
        // TOFIX
        if (strstr($this->getStyle(), 'width: 100%')) {
            $html .= '<table style="width: 100%; border-collapse: collapse;"><tbody><tr><td>';
        }
        $html .= '<textarea id="'.$this->getHtmlId().'" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).' >';
        $html .= $this->getEscapedValue();
        $html .= "</textarea>";
        // TOFIX
        if (strstr($this->getStyle(), 'width: 100%')) {
            $html .= '</td></tr></tbody></table>';
        }
        return $html;
    }
}