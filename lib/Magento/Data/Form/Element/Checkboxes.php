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
 * Form select element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Checkboxes extends Magento_Data_Form_Element_Abstract
{
    /**
     * @param Magento_Data_Form_Element_Factory $factoryElement
     * @param Magento_Data_Form_Element_CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        Magento_Data_Form_Element_Factory $factoryElement,
        Magento_Data_Form_Element_CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        parent::__construct($factoryElement, $factoryCollection, $attributes);
        $this->setType('checkbox');
        $this->setExtType('checkboxes');
    }

    /**
     * Retrieve allow attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return array('type', 'name', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled');
    }

    /**
     * Prepare value list
     *
     * @return array
     */
    protected function _prepareValues() {
        $options = array();
        $values  = array();

        if ($this->getValues()) {
            if (!is_array($this->getValues())) {
                $options = array($this->getValues());
            }
            else {
                $options = $this->getValues();
            }
        }
        elseif ($this->getOptions() && is_array($this->getOptions())) {
            $options = $this->getOptions();
        }
        foreach ($options as $k => $v) {
            if (is_array($v)) {
                if (isset($v['value'])) {
                    if (!isset($v['label'])) {
                        $v['label'] = $v['value'];
                    }
                    $values[] = array(
                        'label' => $v['label'],
                        'value' => $v['value']
                    );
                }
            } else {
                $values[] = array(
                    'label' => $v,
                    'value' => $k
                );
            }
        }

        return $values;
    }

    /**
     * Retrieve HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->_prepareValues();

        if (!$values) {
            return '';
        }

        $html  = '<div class=nested>';
        foreach ($values as $value) {
            $html.= $this->_optionToHtml($value);
        }
        $html .= '</div>'
            . $this->getAfterElementHtml();

        return $html;
    }

    public function getChecked($value)
    {
        if ($checked = $this->getValue()) {
        }
        elseif ($checked = $this->getData('checked')) {
        }
        else {
            return ;
        }
        if (!is_array($checked)) {
            $checked = array(strval($checked));
        }
        else {
            foreach ($checked as $k => $v) {
                $checked[$k] = strval($v);
            }
        }
        if (in_array(strval($value), $checked)) {
            return 'checked';
        }
        return ;
    }

    public function getDisabled($value)
    {
        if ($disabled = $this->getData('disabled')) {
            if (!is_array($disabled)) {
                $disabled = array(strval($disabled));
            }
            else {
                foreach ($disabled as $k => $v) {
                    $disabled[$k] = strval($v);
                }
            }
            if (in_array(strval($value), $disabled)) {
                return 'disabled';
            }
        }
        return ;
    }

    public function getOnclick($value)
    {
        if ($onclick = $this->getData('onclick')) {
            return str_replace('$value', $value, $onclick);
        }
        return ;
    }

    public function getOnchange($value)
    {
        if ($onchange = $this->getData('onchange')) {
            return str_replace('$value', $value, $onchange);
        }
        return ;
    }

//    public function getName($value)
//    {
//        if ($name = $this->getData('name')) {
//            return str_replace('$value', $value, $name);
//        }
//        return ;
//    }

    protected function _optionToHtml($option)
    {
        $id = $this->getHtmlId().'_'.$this->_escape($option['value']);

        $html = '<div class="field choice"><input id="'.$id.'"';
        foreach ($this->getHtmlAttributes() as $attribute) {
            if ($value = $this->getDataUsingMethod($attribute, $option['value'])) {
                $html .= ' '.$attribute.'="'.$value.'"';
            }
        }
        $html .= ' value="'.$option['value'].'" />'
            . ' <label for="'.$id.'">' . $option['label'] . '</label></div>'
            . "\n";
        return $html;
    }
}
