<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\Client\Driver\Selenium\Element;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class SelectgrouplistElement
 * Typified element class for select with group
 */
class SelectgrouplistElement extends MultiselectElement
{
    /**
     * Locator for search optgroup by label
     *
     * @var string
     */
    protected $optgroupByLabel = './/optgroup[@label="%s"]';

    /**
     * Locator for search optgroup by number
     *
     * @var string
     */
    protected $optgroupByNumber = './/optgroup[%d]';

    /**
     * Locator for search next optgroup
     *
     * @var string
     */
    protected $nextOptgroup = './/following-sibling::optgroup[%d]';

    /**
     * Locator for search child optgroup
     *
     * @var string
     */
    protected $childOptgroup = ".//following-sibling::optgroup[%d][@label='%s']";

    /**
     * Locator for search parent optgroup
     *
     * @var string
     */
    protected $parentOptgroup = 'optgroup[option[text()="%s"]]';

    /**
     * Locator for search preceding sibling optgroup
     *
     * @var string
     */
    protected $precedingOptgroup = '/preceding-sibling::optgroup[1][substring(@label,1,%d)="%s"]';

    /**
     * Locator for option
     *
     * @var string
     */
    protected $option = './/option[text()="%s"]';

    /**
     * Locator search for option by number
     *
     * @var string
     */
    protected $optionByNumber = './/optgroup[%d]/option[%d]';

    /**
     * Indent, four symbols non breaking space
     *
     * @var string
     */
    protected $indent = "\xC2\xA0\xC2\xA0\xC2\xA0\xC2\xA0";

    /**
     * Trim symbols
     *
     * @var string
     */
    protected $trim = "\xC2\xA0 ";

    /**
     * Set values
     *
     * @param array|string $values
     * @return void
     */
    public function setValue($values)
    {
        $this->clearSelectedOptions();
        if (is_array($values)) {
            foreach ($values as $value) {
                $this->selectValue($value, $this);
            }
        } else {
            $this->selectValue($values, $this);
        }
    }

    /**
     * Set single value
     *
     * @param $values
     * @param Element $context
     * @return void
     * @throws \Exception
     */
    protected function selectValue($values, Element $context)
    {
        $isOptgroup = false;
        $optgroupIndent = '';
        $values = explode('/', $values);

        foreach ($values as $value) {
            $optionIndent = $isOptgroup ? $this->indent : '';
            $option = $context->find(sprintf($this->option, $optionIndent . $value), Locator::SELECTOR_XPATH);
            if ($option->isVisible()) {
                if (!$option->isSelected()) {
                    $option->click();
                }
                return;
            }

            $value = $optgroupIndent . $value;
            $optgroupIndent .= $this->indent;
            if ($isOptgroup) {
                $context = $this->getChildOptgroup($value, $context);
                continue;
            }
            $optgroup = $context->find(sprintf($this->optgroupByLabel, $value), Locator::SELECTOR_XPATH);
            if ($optgroup->isVisible()) {
                $context = $optgroup;
                $isOptgroup = true;
                continue;
            }

            throw new \Exception("Can't find element");
        }

        throw new \Exception("Can't find element");
    }

    /**
     * Get child optgroup element
     *
     * @param $value
     * @param Element $context
     * @return Element
     * @throws \Exception
     */
    protected function getChildOptgroup($value, Element $context)
    {
        $count = 1;
        while (true) {
            $optgroup = $context->find(sprintf($this->nextOptgroup, $count), Locator::SELECTOR_XPATH);
            if (!$optgroup->isVisible()) {
                throw new \Exception("Can't find element");
            }

            $optgroup = $context->find(
                sprintf($this->childOptgroup, $count, $value),
                Locator::SELECTOR_XPATH
            );
            if ($optgroup->isVisible()) {
                return $optgroup;
            }

            $count += 1;
        }
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getValue()
    {
        $values = [];

        foreach ($this->getSelectedOptions() as $option) {
            $value = [];

            /** @var Element $option */
            $optionText = $option->getText();
            $optionValue = trim($option->getText(), $this->trim);
            $value[] = $optionValue;
            if (0 !== strpos($optionText, '    ')) {
                $values[] = implode('/', $value);
                continue;
            }

            $pathOptgroup = sprintf($this->parentOptgroup, $this->indent . $optionValue);
            $optgroup = $this->_getWrappedElement()->byXPath($pathOptgroup);
            $optgroupText = $optgroup->attribute('label');
            $optgroupValue = trim($optgroupText, $this->trim);
            $amountIndent = strlen($optgroupText) - strlen($optgroupValue);
            $amountIndent = $amountIndent ? ($amountIndent / strlen($this->indent)) : 0;
            $value[] = $optgroupValue;
            if (0 == $amountIndent) {
                $values[] = implode('/', $value);
                continue;
            }

            $amountIndent -= 1;
            $indent = $amountIndent ? str_repeat($this->indent, $amountIndent) : '';
            $pathOptgroup .= sprintf($this->precedingOptgroup, $amountIndent * 4, $indent);
            while (0 <= $amountIndent && $this->find($pathOptgroup, Locator::SELECTOR_XPATH)->isVisible()) {
                $optgroup = $this->_getWrappedElement()->byXPath($pathOptgroup);
                $optgroupText = $optgroup->attribute('label');
                $optgroupValue = trim($optgroupText, $this->trim);
                $value[] = $optgroupValue;

                $amountIndent -= 1;
                $indent = (0 < $amountIndent) ? str_repeat($this->indent, $amountIndent) : '';
                $pathOptgroup .= sprintf($this->precedingOptgroup, $amountIndent * 4, $indent);
            }

            $values[] = implode('/', array_reverse($value));
        }

        return $values;
    }

    /**
     * Get options
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = [];

        $countOptgroup = 1;
        $optgroup = $this->find(sprintf($this->optgroupByNumber, $countOptgroup), Locator::SELECTOR_XPATH);
        while ($optgroup->isVisible()) {
            $countOption = 1;
            $option = $this->find(
                sprintf($this->optionByNumber, $countOptgroup, $countOption),
                Locator::SELECTOR_XPATH
            );
            while ($option->isVisible()) {
                $options[] = $option;
                $countOption += 1;
                $option = $this->find(
                    sprintf($this->optionByNumber, $countOptgroup, $countOption),
                    Locator::SELECTOR_XPATH
                );
            }

            $countOptgroup += 1;
            $optgroup = $this->find(sprintf($this->optgroupByNumber, $countOptgroup), Locator::SELECTOR_XPATH);
        }
        return $options;
    }

    /**
     * Get selected options
     *
     * @return array
     */
    protected function getSelectedOptions()
    {
        $options = [];
        foreach ($this->getOptions() as $option) {
            /** Element $option */
            if ($option->isSelected()) {
                $options[] = $option;
            }
        }
        return $options;
    }

} 