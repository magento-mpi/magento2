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
    protected $_renderer;

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
    public function addElement(Varien_Data_Form_Element_Abstract $element, $after=false)
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
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange');
    }

    public function addClass($class)
    {
        $oldClass = $this->getClass();
        $this->setClass($oldClass.' '.$class);
        return $this;
    }

    protected function _escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT);
    }

    public function getEscapedValue()
    {
        return $this->_escape($this->getValue());
    }

    public function setRenderer(Varien_Data_Form_Element_Renderer_Interface $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }
    
    public function getElementHtml()
    {
        $html = '<input id="'.$this->getHtmlId().'" name="'.$this->getName()
             .'" value="'.$this->getEscapedValue().'"'.$this->serialize($this->getHtmlAttributes()).'/>'."\n";
        return $html;
    }
    
    public function getDefaultHtml()
    {
        $html = ( $this->getNoSpan() === true ) ? '' : '<span class="field-row">'."\n";
        if ($this->getLabel()) {
            $html.= '<label for="'.$this->getHtmlId().'">'.$this->getLabel().'</label>'."\n";
        }
        $html.= $this->getElementHtml();
        $html.= ( $this->getNoSpan() === true ) ? '' : '</span>'."\n";
        return $html;
    }
    
    public function getHtml()
    {
        $html = '';

        if ($this->_renderer) {
            $html = $this->_renderer->render($this);
        }
        else {
            $html = $this->getDefaultHtml();
        }
        return $html;
    }
    
    public function toHtml()
    {
        return $this->getHtml();
    }
}