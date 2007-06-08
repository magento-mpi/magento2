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

    public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
        $this->_elements = new Varien_Data_Form_Element_Collection($this);        
    }

    /**
     * Add form element
     *
     * @param   Varien_Data_Form_Element_Abstract $element
     * @return  Varien_Data_Form
     */
    protected function _addElement($element)
    {
        $this->_elements->add($element);
        return $this;
    }

    public function addField($elementId, $type, $config)
    {
        $className = 'Varien_Data_Form_Element_'.ucfirst(strtolower($type));
        try {
            $element = new $className($config);
            $element->setForm($this)
                ->setId($elementId);
            $this->_addElement($element);
        }
        catch (Exception $e){
            throw new Exception('Form not support element type "'.$type.'"');
        }
        return $element;
    }

    public function addFieldset($elementId, $config)
    {
        $element = new Varien_Data_Form_Element_Fieldset($config);
        $element->setForm($this)
            ->setId($elementId);
        $this->_addElement($element);
        return $element;
    }
    
    public function addColumn($elementId, $config)
    {
        $element = new Varien_Data_Form_Element_Column($config);
        $element->setForm($this)
            ->setId($elementId);
        $this->_addElement($element);
        return $element;
    }

    public function __toArray(array $arrAttributes = array())
    {
        $res = array();
        $res['config']  = $this->getData();
        $res['formElements']= array();
        foreach ($this->_elements as $element) {
            $res['formElements'][] = $element->toArray();
        }
        return $res;
    }

}