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
        return array('type', 'name', 'title', 'class', 'style', 'onclick', 'onchange');
    }
    
    public function toHtml()
    {
        $this->addClass('textarea');
        $html = '<span class="field-row">'."\n";
        if ($this->getLabel()) {
            $html.= '<label for="'.$this->getHtmlId().'">'.$this->getLabel().'</label>'."\n";
        }
        $html.= '<textarea id="'.$this->getHtmlId().'" '.$this->serialize($this->getHtmlAttributes()).'/>';
        $html.= $this->getValue();
        $html.= "</textarea>\n";
        $html.= '</span>'."\n";
        return $html;
    }
}