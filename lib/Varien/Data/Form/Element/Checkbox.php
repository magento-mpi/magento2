<?php
/**
 * Form checkbox element
 *
 * @package    Varien
 * @subpackage Form
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @author     Ivan Chepurnyi <mitch@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Checkbox extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }
    
    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'checked', 'onclick', 'onchange');
    }
    
    public function getElementHtml()
    {
        if ($checked = $this->getChecked()) {
            $this->setData('checked', true);
        }
        else {
            $this->unsetData('checked');
        }
        return parent::getElementHtml();
    }
    
    /**
     * Set check status of checkbox
     *
     * @param boolean $value
     * @return Varien_Data_Form_Element_Checkbox
     */
    public function setIsChecked($value=false) 
    {
        $this->setData('checked', $value);
        return $this;
    }
    
    /**
     * Return check status of checkbox
     *
     * @return boolean
     */
    public function getIsChecked() {
        return $this->getData('checked');
    }
}                               