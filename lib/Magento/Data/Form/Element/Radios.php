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
 * Radio buttons collection
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Radios extends \Magento\Data\Form\Element\AbstractElement
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
        $this->setType('radios');
    }

    /**
     * @return mixed
     */
    public function getSeparator()
    {
        $separator = $this->getData('separator');
        if (is_null($separator)) {
            $separator = '&nbsp;';
        }
        return $separator;
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $value = $this->getValue();
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                $html.= $this->_optionToHtml($option, $value);
            }
        }
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    /**
     * @param array $option
     * @param array $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected)
    {
        $html = '<input type="radio"'.$this->serialize(array('name', 'class', 'style'));
        if (is_array($option)) {
            $html.= 'value="'.$this->_escape($option['value']).'"  id="'.$this->getHtmlId().$option['value'].'"';
            if ($option['value'] == $selected) {
                $html.= ' checked="checked"';
            }
            $html.= ' />';
            $html.= '<label class="inline" for="'.$this->getHtmlId().$option['value'].'">'.$option['label'].'</label>';
        }
        elseif ($option instanceof \Magento\Object) {
        	$html.= 'id="'.$this->getHtmlId().$option->getValue().'"'.$option->serialize(array('label', 'title', 'value', 'class', 'style'));
        	if (in_array($option->getValue(), $selected)) {
        	    $html.= ' checked="checked"';
        	}
        	$html.= ' />';
        	$html.= '<label class="inline" for="'.$this->getHtmlId().$option->getValue().'">'.$option->getLabel().'</label>';
        }
        $html.= $this->getSeparator() . "\n";
        return $html;
    }
}
