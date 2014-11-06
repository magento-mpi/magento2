<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\Block\BlockFactory;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Catalog product custom attribute block.
 */
class AttributeBlock extends \Mtf\Block\Block
{
    /**
     * CatalogProductAttribute object.
     *
     * @var CatalogProductAttribute
     */
    protected $attribute;

    /**
     * Common product attribute element selector.
     *
     * @var string
     */
    protected $attributeElement = '.control [data-ui-id][name]';

    /**
     * @constructor
     * @param Element $element
     * @param BlockFactory $blockFactory
     * @param Browser $browser
     * @param array $config
     */
    public function __construct(Element $element, BlockFactory $blockFactory, Browser $browser, array $config = [])
    {
        parent::__construct($element, $blockFactory, $browser, $config);
        $this->attribute = $config['attribute'];
    }

    /**
     * Set attribute value.
     *
     * @param string|null $value
     * @throws \Exception
     * @return void
     */
    public function setValue($value = null)
    {
        $value = $value === null ? $this->getDefaultValue() : $value;
        $value = $this->attribute->getOptions() !== null ? $this->getValueFromOptions() : $value;
        $methodName = 'set' . preg_replace('@\W+@', '', $this->attribute->getFrontendInput());
        if (method_exists($this, $methodName)) {
            call_user_func([$this, $methodName], $value);
        } else {
            throw new \Exception("Couldn't find $methodName method.");
        }
    }

    /**
     * Get default value from attribute fixture.
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        $possibleFields = [
            'default_value_text',
            'default_value_textarea',
            'default_value_date',
            'default_value_yesno',
        ];
        foreach ($possibleFields as $field) {
            if ($this->attribute->hasData($field) !== false) {
                return $this->attribute->getData($field);
            }
        }
    }

    /**
     * Get attribute value.
     *
     * @throws \Exception
     * @return string
     */
    public function getValue()
    {
        $methodName = 'get' . preg_replace('@\W+@', '', $this->attribute->getFrontendInput());
        if (method_exists($this, $methodName)) {
            return call_user_func([$this, $methodName]);
        } else {
            throw new \Exception("Couldn't find $methodName method.");
        }
    }

    /**
     * Get value from attribute options.
     *
     * @return string
     */
    protected function getValueFromOptions()
    {
        $options = $this->attribute->getOptions();
        foreach ($options as $option) {
            if ($option['is_default'] == 'Yes') {
                return $option['admin'];
            }
        }
    }

    /**
     * Set dropdown value.
     *
     * @param string|null $value
     * @return void
     */
    protected function setDropDown($value = null)
    {
        $this->_rootElement->find($this->attributeElement, Locator::SELECTOR_CSS, 'select')->setValue($value);
    }

    /**
     * Get dropdown value.
     *
     * @return string
     */
    protected function getDropDown()
    {
        return $this->_rootElement->find($this->attributeElement, Locator::SELECTOR_CSS, 'select')->getValue();
    }

    /**
     * Set Text Field value.
     *
     * @param string|null $value
     * @return void
     */
    protected function setTextField($value = null)
    {
        $this->_rootElement->find($this->attributeElement)->setValue($value);
    }

    /**
     * Get Text Field value.
     *
     * @return string
     */
    protected function getTextField()
    {
        return $this->_rootElement->find($this->attributeElement)->getValue();
    }

    /**
     * Set Text Area value.
     *
     * @param string|null $value
     * @return void
     */
    protected function setTextArea($value = null)
    {
        $this->_rootElement->find($this->attributeElement)->setValue($value);
    }

    /**
     * Get Text Area value.
     *
     * @return string
     */
    protected function getTextArea()
    {
        return $this->_rootElement->find($this->attributeElement)->getValue();
    }

    /**
     * Set Date value.
     *
     * @param string|null $value
     * @return void
     */
    protected function setDate($value = null)
    {
        $this->_rootElement->find($this->attributeElement, Locator::SELECTOR_CSS, 'datepicker')->setValue($value);
    }

    /**
     * Get Date value.
     *
     * @return string
     */
    protected function getDate()
    {
        return $this->_rootElement->find($this->attributeElement, Locator::SELECTOR_CSS, 'datepicker')->getValue();
    }

    /**
     * Set MultipleSelect value.
     *
     * @param string|null $value
     * @return void
     */
    protected function setMultipleSelect($value = null)
    {
        $this->_rootElement->find($this->attributeElement, Locator::SELECTOR_CSS, 'select')->setValue($value);
    }

    /**
     * Get MultipleSelect value.
     *
     * @return string
     */
    protected function getMultipleSelect()
    {
        return $this->_rootElement->find($this->attributeElement, Locator::SELECTOR_CSS, 'select')->getValue();
    }

    /**
     * Set Price value.
     *
     * @param string|null $value
     * @return void
     */
    protected function setPrice($value = null)
    {
        $this->_rootElement->find($this->attributeElement)->setValue($value);
    }

    /**
     * Get Price value.
     *
     * @return string
     */
    protected function getPrice()
    {
        return $this->_rootElement->find($this->attributeElement)->getValue();
    }
}
