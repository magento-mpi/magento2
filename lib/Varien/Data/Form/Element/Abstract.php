<?php
/**
 * Data form abstract class
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Varien_Data_Form_Element_Abstract extends Varien_Data_Form_Abstract 
{
    protected $_id;
    protected $_type;
    protected $_form;
    protected $_elements;
    
    public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
    }
    
    /**
     * Add form element
     *
     * @param   Varien_Data_Form_Element_Abstract $element
     * @return  Varien_Data_Form
     */
    public function addElement(Varien_Data_Form_Element_Abstract $element, $after=null)
    {
        if ($this->getForm()) {
            $this->getForm()->checkElementId($element->getId());
            $this->getForm()->addElementToCollection($element);
        }
        
        parent::addElement($element, $after);
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function getForm()
    {
        return $this->_form;
    }
    
    public function getElements()
    {
        return $this->_elements;
    }
    
    public function setId($id)
    {
        $this->_id = $id;
        $this->setData('html_id', $id);
        return $this;
    }
    
    public function getHtmlId()
    {
        return $this->getData('html_id').$this->getForm()->getHtmlIdPrefix();
    }
    
    public function getName()
    {
        $name = $this->getData('name');
        if ($prefix = $this->getForm()->getFieldNamePrefix()) {
            $name = $this->getForm()->addPrefixToName($name, $prefix);
        }
        return $name;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
        $this->setData('type', $type);
        return $this;
    }
    
    public function setForm($form)
    {
        $this->_form = $form;
        return $this;
    }

    public function removeField($elementId)
    {
        $this->getForm()->removeField($elementId);
        return parent::removeField($elementId);
    }
    
    public function getHtmlAttributes()
    {
        return array('type', 'name', 'value', 'title', 'class', 'style', 'onclick', 'onchange');
    }
    
    public function addClass($class)
    {
        $oldClass = $this->getClass();
        $this->setClass($oldClass.' '.$class);
        return $this;
    }
    
    public function toHtml()
    {
        $html = '<span class="field-row">'."\n";
        if ($this->getLabel()) {
            $html.= '<label for="'.$this->getHtmlId().'">'.$this->getLabel().'</label>'."\n";
        }
        $html.= '<input id="'.$this->getHtmlId().'" '.$this->serialize($this->getHtmlAttributes()).'/>'."\n";
        $html.= '</span>'."\n";
        return $html;
    }
}