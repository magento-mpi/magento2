<?php
/**
 * Form block
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Form extends Mage_Core_Block_Template 
{
    /**
     * Form elements
     * 
     * array(
     *      [$elementId] => $elementType
     * )
     *
     * @var array
     */
    protected $_elements = array();
    
    /**
     * Form fields
     *
     * @var array([$elementId]=>$elementType)
     */
    protected $_fields = array();
    
    /**
     * Form buttons
     *
     * @var array([$elementId]=>$elementType)
     */
    protected $_buttons = array();
    
    /**
     * Elements group
     * 
     * array(
     *      [$groupId] => array(
     *          ['label']    => string
     *          ['elements'] => array($elementId)
     *      )
     * )
     *
     * @var array
     */
    protected $_elementGroups = array();
    
    /**
     * Constructor
     *
     * @param array $attributes form attributes
     */
    public public function __construct($attributes = array()) 
    {
        parent::__construct($attributes);
        
        if (is_null($this->getIsFormFramed())) {
            $this->setIsFormFramed(true);
        }
        if (is_null($this->getMethod())) {
            $this->setMethod('post');
        }
        if (is_null($this->getEnctype())) {
            $this->setEnctype('multipart/form-data');
        }
    }
    
    /**
     * Add form field
     *
     * @param  string $elementId
     * @param  string $elementType
     * @param  array  $elementConfig
     * @param  string || bool $after
     * @return Mage_Core_Block_Form
     */
    public function addField($elementId, $elementType, $elementConfig, $after=true)
    {
        $this->_addElement($elementId, $elementType, $elementConfig, $after);
        $this->_fields[$elementId] = $elementType;
        return $this;
    }
    
    /**
     * Add form button
     *
     * @param  string $elementId
     * @param  string $elementType
     * @param  array  $elementConfig
     * @param  string || bool $after
     * @return Mage_Core_Block_Form
     */
    public function addButton($elementId, $elementType, $elementConfig, $after=true)
    {
        $this->_addElement($elementId, $elementType, $elementConfig, $after);
        $this->_buttons[$elementId] = $elementType;
        return $this;
    }
    

    /**
     * Add form element
     *
     * @param  string $elementId
     * @param  string $elementType
     * @param  array  $elementConfig
     * @param  string || bool $after
     * @return Mage_Core_Block_Form
     */
    protected function _addElement($elementId, $elementType, $elementConfig, $after=true)
    {
        $this->setChild(
           $elementId,
           Mage_Core_Block_Form_Element::factory($elementType, $elementConfig)
        );
        $this->_elements[$elementId] = $elementType;
        return $this;
    }
    
    /**
     * Add elements group
     * 
     * TODO: after flag
     * 
     * @param  string $groupId
     * @param  array  $groupElements
     * @param  string $label
     * @param  string || bool $after
     * @return Mage_Core_Block_Form
     */
    public function addGroup($groupId, $groupElements, $label, $after=true)
    {
        $groupInfo = array();
        $groupInfo['label']     = $label;
        $groupInfo['elements']  = $groupElements;
        
        $this->_elementGroups[$groupId] = $groupInfo;
        
        return $this;
    }
    
    /**
     * Get form element
     *
     * @param   string $elementId
     * @return  Mage_Core_Block_Form_Element_Abstract
     */
    public function getElement($elementId)
    {
        return isset($this->_elements[$elementId]) ? $this->_children[$elementId] : null;
    }
    
    /**
     * Get form elements group
     *
     * @param   string $groupId
     * @return  array
     */
    function getGroup($groupId='')
    {
        if (empty($groupId)) {
            return $this->_elementGroups;
        }
        elseif (isset($this->_elementGroups[$groupId])){
            return $this->_elementGroups[$groupId];
        }
        return false;
    }
    
    /**
     * Get all form elements id
     *
     * @return array
     */
    function getFields()
    {
        return array_keys($this->_fields);
    }
    
    /**
     * Get all form buttons id
     *
     * @return unknown
     */
    function getButtons()
    {
        return array_keys($this->_buttons);
    }
    
    /**
     * Get elements by element type
     *
     * @param   string $type
     * @return  array
     */
    public function getElementsIdByType($type)
    {
        $arrElements = array();
        if (is_array($type)) {
            foreach ($type as $typeName) {
                $arrElements = array_merge($arrElements, array_keys($this->_elements, $typeName));
            }
        }
        else {
            $arrElements = array_keys($this->_elements, $type);
        }
        return $arrElements;
    }
    
    /**
     * Set element attribute
     *
     * @param   string $attribName
     * @param   string $attribValue
     * @return  Mage_Core_Block_Form
     */
    public function setElementAttribute($attribName, $attribValue)
    {
        return $this;
    }
    
    /**
     * Set element value
     *
     * @param   string $arrValues
     * @return  Mage_Core_Block_Form
     */
    public function setElementsValues($arrValues)
    {
        foreach ($arrValues as $elementId => $elementValue) {
            if (isset($this->_elements[$elementId])) {
                $this->getChild($elementId)->setAttribute('value', $elementValue);
            }
        }
        return $this;
    }
    
    /**
     * Delete form field
     *
     * @param   string $elementId
     * @return  Mage_Core_Block_Form
     */
    public function deleteField($elementId)
    {
        if (isset($this->_fields[$elementId])) {
            unset($this->_fields[$elementId]);
            
            $this->_deleteElement($elementId);
        }
        return $this;
    }
    
    /**
     * Delete form button
     *
     * @param   string $elementId
     * @return  Mage_Core_Block_Form
     */
    public function deleteButton($elementId)
    {
        if (isset($this->_buttons[$elementId])) {
            unset($this->_buttons[$elementId]);
            
            $this->_deleteElement($elementId);
        }
        return $this;
    }
    
    /**
     * Delete form element
     *
     * @param   string $elementId
     * @return  Mage_Core_Block_Form
     */
    protected function _deleteElement($elementId)
    {
        if (isset($this->_elements[$elementId])) {
            unset($this->_elements[$elementId]);
            unset($this->_children[$elementId]);
        }
        return $this;
    }
    
    /**
     * Delete form group
     *
     * @param   string $groupId
     * @return  Mage_Core_Block_Form
     */
    function deleteGroup($groupId)
    {
        if (isset($this->_elementGroups[$groupId])) {
            unset($this->_elementGroups[$groupId]);
        }
        return $this;
    }
    
    /**
     * Render form
     *
     * @return Mage_Core_Block_Form
     */
    public function renderView()
    {
        $formAttributes = array('name', 'id', 'method', 'enctype', 'action', 'target', 'onsubmit', 'class', 'style');
        $this->assign('formAttributes', $this->_attributesToString($formAttributes));
        
        return parent::renderView();
    }
    
    protected function __toArray()
    {
        $res = array();
        $res['config'] = $this->getData();
        /*$res['elements']['fieldsets']   = $this->getGroup();
        $res['elements']['columns']  = array();
        $res['elements']['fields']   = array();*/
        foreach ($this->getFields() as $fieldId) {
            $elementInfo = $this->getChild($fieldId)->toArray();
            $elementInfo['elementType'] = 'field';
            $res['elements'][] = $elementInfo;
        }
        //$res['buttons']  = array();
        
        return $res;
    }
}