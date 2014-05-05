<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Typified element class for  Multiple Select List elements
 *
 * @package Mtf\Client\Driver\Selenium\Element
 */
class MultiselectlistElement extends MultiselectElement
{
    /**
     * XPath selector for finding needed option by its value
     *
     * @var string
     */
    protected $optionMaskElement = './/*[contains(@class, "mselect-list-item")]//label/span[text()="%s"]';

    /**
     * XPath selector for finding option by its position number
     *
     * @var string
     */
    protected $optionElement = './/*[contains(@class,"mselect-list-item")][%d]';

    /**
     * XPath selector for checking is option checked by value
     *
     * @var string
     */
    protected $optionCheckedElement = './/*[contains(@class, "mselect-checked")]/following-sibling::span[text()="%s"]';

    /**
     * @param bool $waitForElementPresent
     * @return \PHPUnit_Extensions_Selenium2TestCase_Element|\PHPUnit_Extensions_Selenium2TestCase_Element_Select
     */
    protected function _getWrappedElement($waitForElementPresent = true)
    {
        return Element::_getWrappedElement($waitForElementPresent);
    }

    /**
     * Select options by values in multiple select list
     *
     * @param array|string $values
     * @throws \Exception
     */
    public function setValue($values)
    {
        $this->clearSelectedOptions();
        if (is_array($values)) {
            foreach ($values as $value) {
                $this->selectOptionByValue($value);
            }
        } else {
            $this->selectOptionByValue($values);
        }
    }

    /**
     * Select option by value
     *
     * @param string $value the text appearing in the option
     * @throws \Exception
     */
    protected function selectOptionByValue($value) {
        $option = $this->_context->find(sprintf($this->optionMaskElement, $value), Locator::SELECTOR_XPATH);
        if (!$option->isVisible()) {
            throw new \Exception('[' . $value . '] option is not visible in select list.');
        }
        $option->click();
    }

    /**
     * Method that returns array with checked options in multiple select list
     *
     * @return array|string
     */
    public function getValue()
    {
        $options = [];
        $checkedOptions = [];
        $counter = 1;

        $newOption = $this->find(sprintf($this->optionElement, $counter), Locator::SELECTOR_XPATH);
        while ($newOption->isVisible()) {
            $options[] = $newOption;
            $counter++;
            $newOption = $this->find(sprintf($this->optionElement, $counter), Locator::SELECTOR_XPATH);
        }

        foreach($options as $option) {
            /** @var Element $option */
            $checkedOption =  $option->find('.//*[contains(@class, "mselect-checked")]/following-sibling::span', Locator::SELECTOR_XPATH);
            if ($checkedOption->isVisible()) {
                $checkedOptions[] = $checkedOption->getText();
            }
        }

        return $checkedOptions;
    }

    /**
     * Clear selected options in multiple select list
     */
    public function clearSelectedOptions()
    {
        $selectedOptions = $this->getValue();
        foreach ($selectedOptions as $value) {
            $this->_context->find(sprintf($this->optionMaskElement, $value), Locator::SELECTOR_XPATH)->click();
        }
    }
}
