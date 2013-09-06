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
 * Data form abstract class
 *
 * @category   Magento
 * @package    Magento_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Data_Form_Element_Abstract extends Magento_Data_Form_Abstract
{
    protected $_id;
    protected $_type;
    /** @var Magento_Data_Form */
    protected $_form;
    protected $_elements;
    protected $_renderer;

    /**
     * Shows whether current element belongs to Basic or Advanced form layout
     *
     * @var bool
     */
    protected $_advanced = false;

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        $this->_coreData = $coreData;
        parent::__construct($factoryElement, $factoryCollection, $attributes);
        $this->_renderer = Magento_Data_Form::getElementRenderer();
    }

    /**
     * Add form element
     *
     * @param   Magento_Data_Form_Element_Abstract $element
     * @return  Magento_Data_Form
     */
    public function addElement(Magento_Data_Form_Element_Abstract $element, $after=false)
    {
        if ($this->getForm()) {
            $this->getForm()->checkElementId($element->getId());
            $this->getForm()->addElementToCollection($element);
        }

        parent::addElement($element, $after);
        return $this;
    }

    /**
     * Shows whether current element belongs to Basic or Advanced form layout
     *
     * @return  bool
     */
    public function isAdvanced() {
        return $this->_advanced;
    }

    /**
     * Set _advanced layout property
     *
     * @param bool $advanced
     * @return Magento_Data_Form_Element_Abstract
     */
    public function setAdvanced($advanced) {
        $this->_advanced = $advanced;
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

    /**
     * Get form
     *
     * @return Magento_Data_Form
     */
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
        return $this->getForm()->getHtmlIdPrefix() . $this->getData('html_id') . $this->getForm()->getHtmlIdSuffix();
    }

    public function getName()
    {
        $name = $this->getData('name');
        if ($suffix = $this->getForm()->getFieldNameSuffix()) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
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
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex', 'placeholder');
    }

    public function addClass($class)
    {
        $oldClass = $this->getClass();
        $this->setClass($oldClass.' '.$class);
        return $this;
    }

    /**
     * Remove CSS class
     *
     * @param string $class
     * @return Magento_Data_Form_Element_Abstract
     */
    public function removeClass($class)
    {
        $classes = array_unique(explode(' ', $this->getClass()));
        if (false !== ($key = array_search($class, $classes))) {
            unset($classes[$key]);
        }
        $this->setClass(implode(' ', $classes));
        return $this;
    }

    protected function _escape($string)
    {
        return htmlspecialchars($string, ENT_COMPAT);
    }

    public function getEscapedValue($index=null)
    {
        $value = $this->getValue($index);

        if ($filter = $this->getValueFilter()) {
            $value = $filter->filter($value);
        }
        return $this->_escape($value);
    }

    public function setRenderer(Magento_Data_Form_Element_Renderer_Interface $renderer)
    {
        $this->_renderer = $renderer;
        return $this;
    }

    public function getRenderer()
    {
        return $this->_renderer;
    }

    protected function _getUiId($suffix = null)
    {
        if ($this->_renderer instanceof Magento_Core_Block_Abstract) {
            return $this->_renderer->getUiId($this->getType(), $this->getName(), $suffix);
        } else {
            return ' data-ui-id="form-element-' . $this->getName() . ($suffix ? : '') . '"';
        }
    }

    public function getElementHtml()
    {
        $html = '';
        if ($this->getBeforeElementHtml()) {
            $html .= '<label class="addbefore" for="' . $this->getHtmlId() . '">' . $this->getBeforeElementHtml() . '</label>';            
        }
        $html .= '<input id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" '
            . $this->_getUiId()
            . ' value="' . $this->getEscapedValue() . '" ' . $this->serialize($this->getHtmlAttributes()) . '/>';
        if ($this->getAfterElementHtml()) {
            $html.= '<label class="addafter" for="' . $this->getHtmlId() . '">' . $this->getAfterElementHtml() . '</label>';            
        }
        return $html;
    }

    public function getBeforeElementHtml()
    {
        return $this->getData('before_element_html');
    }

    public function getAfterElementHtml()
    {
        return $this->getData('after_element_html');
    }

    /**
     * Render HTML for element's label
     *
     * @param string $idSuffix
     * @return string
     */
    public function getLabelHtml($idSuffix = '')
    {
        if (!is_null($this->getLabel())) {
            $html = '<label class="label" for="' . $this->getHtmlId() . $idSuffix . '"' . $this->_getUiId('label')
                . '><span>'
                . $this->_escape($this->getLabel())
                . ($this->getRequired() ? ' <span class="required">*</span>' : '') . '</span></label>' . "\n";
        } else {
            $html = '';
        }
        return $html;
    }

    public function getDefaultHtml()
    {
        $html = $this->getData('default_html');
        if (is_null($html)) {
            $html = ( $this->getNoSpan() === true ) ? '' : '<span class="field-row">'."\n";
            $html.= $this->getLabelHtml();
            $html.= $this->getElementHtml();
            $html.= ( $this->getNoSpan() === true ) ? '' : '</span>'."\n";
        }
        return $html;
    }

    public function getHtml()
    {
        if ($this->getRequired()) {
            $this->addClass('required-entry');
        }
        if ($this->_renderer) {
            $html = $this->_renderer->render($this);
        } else {
            $html = $this->getDefaultHtml();
        }
        return $html;
    }

    public function toHtml()
    {
        return $this->getHtml();
    }

    public function serialize($attributes = array(), $valueSeparator='=', $fieldSeparator=' ', $quote='"')
    {
        if (in_array('disabled', $attributes) && !empty($this->_data['disabled'])) {
            $this->_data['disabled'] = 'disabled';
        } else {
            unset($this->_data['disabled']);
        }
        if (in_array('checked', $attributes) && !empty($this->_data['checked'])) {
            $this->_data['checked'] = 'checked';
        } else {
            unset($this->_data['checked']);
        }
        return parent::serialize($attributes, $valueSeparator, $fieldSeparator, $quote);
    }

    public function getReadonly()
    {
        if ($this->hasData('readonly_disabled')) {
            return $this->_getData('readonly_disabled');
        }

        return $this->_getData('readonly');
    }

    public function getHtmlContainerId()
    {
        if ($this->hasData('container_id')) {
            return $this->getData('container_id');
        } elseif ($idPrefix = $this->getForm()->getFieldContainerIdPrefix()) {
            return $idPrefix . $this->getId();
        }
        return '';
    }

    /**
     * Add specified values to element values
     *
     * @param string|int|array $values
     * @param bool $overwrite
     * @return Magento_Data_Form_Element_Abstract
     */
    public function addElementValues($values, $overwrite = false)
    {
        if (empty($values) || (is_string($values) && trim($values) == '')) {
            return $this;
        }
        if (!is_array($values)) {
            $values = $this->_coreData->escapeHtml(trim($values));
            $values = array($values => $values);
        }
        $elementValues = $this->getValues();
        if (!empty($elementValues)) {
            foreach ($values as $key => $value) {
                if ((isset($elementValues[$key]) && $overwrite) || !isset($elementValues[$key])) {
                    $elementValues[$key] = $this->_coreData->escapeHtml($value);
                }
            }
            $values = $elementValues;
        }
        $this->setValues($values);

        return $this;
    }
}
