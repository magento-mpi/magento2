<?php
/**
 * Abstract class for form, coumn and fieldset
 *
 * @package    Ecom
 * @subpackage Data
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Abstract extends Varien_Object
{
    /**
     * Form level elements collection
     *
     * @var Varien_Data_Form_Element_Collection
     */
    protected $_elements;

    /**
     * Element type classes
     *
     * @var unknown_type
     */
    protected $_types = array();

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

    }

    public function addType($type, $className)
    {
        $this->_types[$type] = $className;
        return $this;
    }

    public function getElements()
    {
        if (empty($this->_elements)) {
            $this->_elements = new Varien_Data_Form_Element_Collection($this);
        }
        return $this->_elements;
    }

    /**
     * Add form element
     *
     * @param   Varien_Data_Form_Element_Abstract $element
     * @return  Varien_Data_Form
     */
    public function addElement(Varien_Data_Form_Element_Abstract $element, $after=null)
    {
        $this->getElements()->add($element, $after);
        return $this;
    }

    /**
     * Add child element
     *
     * if $after parameter is false - then element adds to end of collection
     * if $after parameter is null - then element adds to befin of collection
     * if $after parameter is string - then element adds after of the element with some id
     *
     * @param   string $elementId
     * @param   string $type
     * @param   array  $config
     * @param   mixed  $after
     * @return unknown
     */
    public function addField($elementId, $type, $config, $after=false)
    {
        if (isset($this->_types[$type])) {
            $className = $this->_types[$type];
        }
        else {
            $className = 'Varien_Data_Form_Element_'.ucfirst(strtolower($type));
        }
        $element = new $className($config);
        $element->setId($elementId);
        if ($element->getRequired()) {
            $element->addClass('required-entry');
        }
        $this->addElement($element, $after);
        return $element;
    }

    public function removeField($elementId)
    {
        $this->getElements()->remove($elementId);
        return $this;
    }

    public function addFieldset($elementId, $config, $after=false)
    {
        $element = new Varien_Data_Form_Element_Fieldset($config);
        $element->setForm($this)
            ->setId($elementId);
        $this->addElement($element, $after);
        return $element;
    }

    public function addColumn($elementId, $config)
    {
        $element = new Varien_Data_Form_Element_Column($config);
        $element->setForm($this)
            ->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    public function __toArray(array $arrAttributes = array())
    {
        $res = array();
        $res['config']  = $this->getData();
        $res['formElements']= array();
        foreach ($this->getElements() as $element) {
            $res['formElements'][] = $element->toArray();
        }
        return $res;
    }

}
