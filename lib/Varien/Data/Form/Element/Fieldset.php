<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Form fieldset
 *
 * @category   Varien
 * @package    Varien_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Element_Fieldset extends Varien_Data_Form_Element_Abstract
{
    /**
     * Sort child elements by specified data key
     *
     * @var string
     */
    protected $_sortChildrenByKey = '';

    /**
     * Children sort direction
     *
     * @var int
     */
    protected $_sortChildrenDirection = SORT_ASC;

    /**
     * Enter description here...
     *
     * @param array $attributes
     */
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->_renderer = Varien_Data_Form::getFieldsetRenderer();
        $this->setType('fieldset');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '<fieldset id="'.$this->getHtmlId().'"'.$this->serialize(array('class')).'>'."\n";
        if ($this->getLegend()) {
            $html.= '<legend>'.$this->getLegend().'</legend>'."\n";
        }
        $html.= $this->getChildrenHtml();
        $html.= '</fieldset></div>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getChildrenHtml()
    {
        $html = '';
        foreach ($this->getSortedElements() as $element) {
            if ($element->getType() != 'fieldset') {
                $html.= $element->toHtml();
            }
        }
        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getSubFieldsetHtml()
    {
        $html = '';
        foreach ($this->getSortedElements() as $element) {
            if ($element->getType() == 'fieldset') {
                $html.= $element->toHtml();
            }
        }
        return $html;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getDefaultHtml()
    {
        $html = '<div><h4 class="icon-head head-edit-form fieldset-legend">'.$this->getLegend().'</h4>'."\n";
        $html.= $this->getElementHtml();
        return $html;
    }

    /**
     * Enter description here...
     *
     * @param string $elementId
     * @param string $type
     * @param array $config
     * @param boolean $after
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addField($elementId, $type, $config, $after=false)
    {
        $element = parent::addField($elementId, $type, $config, $after);
        if ($renderer = Varien_Data_Form::getFieldsetElementRenderer()) {
            $element->setRenderer($renderer);
        }
        return $element;
    }

    /**
     * Commence sorting elements by values by specified data key
     *
     * @param string $key
     * @param int $direction
     * @return Varien_Data_Form_Element_Fieldset
     */
    public function setSortElementsByAttribute($key, $direction = SORT_ASC)
    {
        $this->_sortChildrenByKey = $key;
        $this->_sortDirection = $direction;
        return $this;
    }

    /**
     * Get sorted elements as array
     *
     * @return array
     */
    public function getSortedElements()
    {
        $elements = array();
        // sort children by value by specified key
        if ($this->_sortChildrenByKey) {
            $sortKey = $this->_sortChildrenByKey;
            $uniqueIncrement = 0; // in case if there are elements with same values
            foreach ($this->getElements() as $e) {
                $key = '_' . $uniqueIncrement;
                if ($e->hasData($sortKey)) {
                    $key = $e->getDataUsingMethod($sortKey) . $key;
                }
                $elements[$key] = $e;
                $uniqueIncrement++;
            }
            ksort($elements, $this->_sortChildrenDirection);
            $elements = array_values($elements);
        } else {
            foreach ($this->getElements() as $element) {
                $elements[] = $element;
            }
        }
        return $elements;
    }
}
