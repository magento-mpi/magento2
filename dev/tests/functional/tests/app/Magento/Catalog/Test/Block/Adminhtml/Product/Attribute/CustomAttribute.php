<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Catalog product custom attribute element.
 */
class CustomAttribute extends Element
{
    /**
     * Attribute input selector;
     *
     * @var string
     */
    protected $inputSelector = '.control [data-ui-id][name]';

    /**
     * Attribute type to element type reference.
     *
     * @var array
     */
    protected $typeReference = [
        'Text Field' => null,
        'Text Area' => null,
        'Date' => 'datepicker',
        'Yes/No' => 'select',
        'Multiple Select' => 'select',
        'Dropdown' => 'select',
        'Price' => null,
    ];

    /**
     * Attribute class to element type reference.
     *
     * @var array
     */
    protected $classReference = [
        'input-text' => 'Text Field',
        'textarea' => 'Text Area',
        'hasDatepicker' => 'Date',
        'select' => 'Dropdown',
    ];

    /**
     * Set custom attribute value.
     *
     * @param array $data
     * @return void
     */
    public function setValue($data)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $value = $data['value'];
        $this->find($this->inputSelector, Locator::SELECTOR_CSS, $this->typeReference[$data['type']])->setValue($value);
    }

    /**
     * Get custom attribute value.
     *
     * @return string|array
     */
    public function getValue()
    {
        $this->_eventManager->dispatchEvent(['get_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        $criteria = new \PHPUnit_Extensions_Selenium2TestCase_ElementCriteria('css selector');
        $criteria->value($this->inputSelector);
        $input = $this->_getWrappedElement()->element($criteria);
        $inputType = $this->getElementByClass($input->attribute('class'));
        return $this->find($this->inputSelector, Locator::SELECTOR_CSS, $inputType)->getValue();
    }

    /**
     * Get element type by class.
     *
     * @param string $class
     * @return string
     */
    protected function getElementByClass($class)
    {
        $element = null;
        foreach ($this->classReference as $reference) {
            if (strpos($class, $reference !== false)) {
                $element = $this->typeReference[$reference];
            }
        }
        return $element;
    }
}
