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
namespace Magento\Data\Form\Element;

class Checkboxes extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        $data = array()
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('checkbox');
        $this->setExtType('checkboxes');
    }

    /**
     * Retrieve allow attributes
     *
     * @return string[]
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

    /**
     * @param mixed $value
     * @return string
     */
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

    /**
     * @param mixed $value
     * @return string
     */
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

    /**
     * @param mixed $value
     * @return mixed
     */
    public function getOnclick($value)
    {
        if ($onclick = $this->getData('onclick')) {
            return str_replace('$value', $value, $onclick);
        }
        return ;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
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

    /**
     * @param array $option
     * @return string
     */
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
