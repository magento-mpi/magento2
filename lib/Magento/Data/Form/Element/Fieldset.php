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
 * Form fieldset
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Fieldset extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->_renderer = \Magento\Data\Form::getFieldsetRenderer();
        $this->setType('fieldset');
        if (isset($attributes['advancedSection'])) {
            $this->setAdvancedLabel($attributes['advancedSection']);
        }
    }

    /**
     * Get elements html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<fieldset id="' . $this->getHtmlId() . '"' . $this->serialize(array('class'))
            . $this->_getUiId() . '>' . "\n";
        if ($this->getLegend()) {
            $html.= '<legend ' . $this->_getUiId('legend') . '>' . $this->getLegend() . '</legend>' . "\n";
        }
        $html.= $this->getChildrenHtml();
        $html.= '</fieldset>' . "\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * Get Children element's array
     *
     * @return array
     */
    public function getChildren()
    {
        $elements = array();
        foreach ($this->getElements() as $element) {
            if ($element->getType() != 'fieldset') {
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * Get Children element's html
     *
     * @return string
     */
    public function getChildrenHtml()
    {
        return $this->_elementsToHtml($this->getChildren());
    }

    /**
     * Get Basic elements' array
     *
     * @return array
     */
    public function getBasicChildren()
    {
        $elements = array();
        foreach ($this->getElements() as $element) {
            if (!$element->isAdvanced()) {
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * Get Basic elements' html in sorted order
     *
     * @return string
     */
    public function getBasicChildrenHtml()
    {
        return $this->_elementsToHtml($this->getBasicChildren());
    }

    /**
     * Get Number of Bacic Children
     *
     * @return int
     */
    public function getCountBasicChildren()
    {
        return count($this->getBasicChildren());
    }

    /**
     * Get Advanced elements'
     *
     * @return string
     */
    public function getAdvancedChildren()
    {
        $elements = array();
        foreach ($this->getElements() as $element) {
            if ($element->isAdvanced()) {
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * Get Advanced elements' html in sorted order
     *
     * @return string
     */
    public function getAdvancedChildrenHtml()
    {
        return $this->_elementsToHtml($this->getAdvancedChildren());
    }

    /**
     * Whether fieldset contains advance section
     *
     * @return bool
     */
    public function hasAdvanced()
    {
        foreach ($this->getElements() as $element) {
            if ($element->isAdvanced()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get SubFieldset
     *
     * @return array
     */
    public function getSubFieldset()
    {
        $elements = array();
        foreach ($this->getElements() as $element) {
            if ($element->getType() == 'fieldset' && !$element->isAdvanced()) {
                $elements[] = $element;
            }
        }
        return $elements;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getSubFieldsetHtml()
    {
        return $this->_elementsToHtml($this->getSubFieldset());
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getDefaultHtml()
    {
        $html = '<div><h4 class="icon-head head-edit-form fieldset-legend">' . $this->getLegend() . '</h4>' . "\n";
        $html.= $this->getElementHtml();
        $html.= '</div>';
        return $html;
    }

    /**
     * Add field to fieldset
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param boolean $after
     * @return \Magento\Data\Form\Element\AbstractElement
     */
    public function addField($elementId, $type, $config, $after = false, $isAdvanced = false)
    {
        $element = parent::addField($elementId, $type, $config, $after);
        if ($renderer = \Magento\Data\Form::getFieldsetElementRenderer()) {
            $element->setRenderer($renderer);
        }
        $element->setAdvanced($isAdvanced);
        return $element;
    }

    /**
     * Return elements as html string
     *
     * @param array $elements
     * @return string
     */
    protected function _elementsToHtml($elements)
    {
        $html = '';
        foreach ($elements as $element) {
            $html .= $element->toHtml();
        }
        return $html;
    }
}
