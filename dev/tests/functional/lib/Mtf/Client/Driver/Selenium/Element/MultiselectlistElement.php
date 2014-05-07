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
     * XPath selector for finding option by its position number
     *
     * @var string
     */
    protected $optionElement = './/*[contains(@class,"mselect-list-item")][%d]/label';

    /**
     * XPath selector for checking is option checked
     *
     * @var string
     */
    protected $optionCheckedElement = './/*[contains(@class, "mselect-checked")]/following-sibling::span';

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
        $options = $this->getOptions();
        $values = is_array($values) ? $values : [$values];

        foreach ($options as $option) {
            /** @var Element $option */
            $optionText = $option->getText();
            $isChecked = $option->find($this->optionCheckedElement, Locator::SELECTOR_XPATH)->isVisible();
            $inArray = in_array($optionText, $values);
            if (($isChecked && !$inArray) || (!$isChecked && $inArray)) {
                $option->click();
            }
        }
    }

    /**
     * Method that returns array with checked options in multiple select list
     *
     * @return array|string
     */
    public function getValue()
    {
        $checkedOptions = [];
        $options = $this->getOptions();

        foreach ($options as $option) {
            /** @var Element $option */
            $checkedOption = $option->find($this->optionCheckedElement, Locator::SELECTOR_XPATH);
            if ($checkedOption->isVisible()) {
                $checkedOptions[] = $checkedOption->getText();
            }
        }

        return $checkedOptions;
    }

    /**
     * Getting all options in multi select list
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [];
        $counter = 1;

        $newOption = $this->find(sprintf($this->optionElement, $counter), Locator::SELECTOR_XPATH);
        while ($newOption->isVisible()) {
            $options[] = $newOption;
            $counter++;
            $newOption = $this->find(sprintf($this->optionElement, $counter), Locator::SELECTOR_XPATH);
        }

        return $options;
    }
}
