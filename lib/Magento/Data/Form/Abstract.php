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
 * Abstract class for form, coumn and fieldset
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Abstract extends Magento_Object
{

    /**
     * Form level elements collection
     *
     * @var Magento_Data_Form_Element_Collection
     */
    protected $_elements;

    /**
     * Element type classes
     *
     * @var unknown_type
     */
    protected $_types = array();

    /**
     * Enter description here...
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->_construct();
    }

    /**
     * Internal constructor, that is called from real constructor
     *
     * Please override this one instead of overriding real __construct constructor
     *
     */
    protected function _construct()
    {
    }

    /**
     * Add element type
     *
     * @param string $type
     * @param string $className
     * @return Magento_Data_Form_Abstract
     */
    public function addType($type, $className)
    {
        $this->_types[$type] = $className;
        return $this;
    }

    /**
     * Get elements collection
     *
     * @return Magento_Data_Form_Element_Collection
     */
    public function getElements()
    {
        if (empty($this->_elements)) {
            $this->_elements = new Magento_Data_Form_Element_Collection($this);
        }
        return $this->_elements;
    }

    /**
     * Disable elements
     *
     * @param boolean $readonly
     * @param boolean $useDisabled
     * @return Magento_Data_Form_Abstract
     */
    public function setReadonly($readonly, $useDisabled = false)
    {
        if ($useDisabled) {
            $this->setDisabled($readonly);
            $this->setData('readonly_disabled', $readonly);
        } else {
            $this->setData('readonly', $readonly);
        }
        foreach ($this->getElements() as $element) {
            $element->setReadonly($readonly, $useDisabled);
        }

        return $this;
    }

    /**
     * Add form element
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @param bool|string|null $after
     *
     * @return Magento_Data_Form
     */
    public function addElement(Magento_Data_Form_Element_Abstract $element, $after = null)
    {
        $element->setForm($this);
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
     * @return Magento_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after=false)
    {
        if (isset($this->_types[$type])) {
            $className = $this->_types[$type];
        } else {
            $className = 'Magento_Data_Form_Element_' . ucfirst(strtolower($type));
        }
        $element = Mage::getObjectManager()->create($className, array($config));
        $element->setId($elementId);
        $this->addElement($element, $after);
        return $element;
    }

    /**
     * Enter description here...
     *
     * @param string $elementId
     * @return Magento_Data_Form_Abstract
     */
    public function removeField($elementId)
    {
        $this->getElements()->remove($elementId);
        return $this;
    }

    /**
     * Add fieldset
     *
     * @param string $elementId
     * @param array $config
     * @param bool|string|null $after
     * @param bool $isAdvanced
     * @return Magento_Data_Form_Element_Fieldset
     */
    public function addFieldset($elementId, $config, $after = false, $isAdvanced = false)
    {
        $element = new Magento_Data_Form_Element_Fieldset($config);
        $element->setId($elementId);
        $element->setAdvanced($isAdvanced);
        $this->addElement($element, $after);
        return $element;
    }

    /**
     * Add column element
     *
     * @param string $elementId
     * @param array $config
     * @return Magento_Data_Form_Element_Column
     */
    public function addColumn($elementId, $config)
    {
        $element = new Magento_Data_Form_Element_Column($config);
        $element->setForm($this)
            ->setId($elementId);
        $this->addElement($element);
        return $element;
    }

    /**
     * Convert elements to array
     *
     * @param array $arrAttributes
     * @return array
     */
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
