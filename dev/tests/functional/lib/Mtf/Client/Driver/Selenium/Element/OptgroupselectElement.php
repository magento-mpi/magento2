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
 * Class OptgroupselectElement
 * Typified element class for option group selectors
 */
class OptgroupselectElement extends SelectElement
{
    /**
     * Option group selector
     *
     * @var string
     */
    protected $optGroup = 'optgroup[option[contains(.,"%s")]]';

    /**
     * Get the value of form element
     *
     * @return string
     */
    public function getValue()
    {
        $this->_eventManager->dispatchEvent(['get_value'], [(string) $this->_locator]);
        $selectedLabel = trim($this->_getWrappedElement()->selectedLabel());
        $value = trim(
                $this->_getWrappedElement()->byXPath(sprintf($this->optGroup, $selectedLabel))->attribute('label'),
                chr(0xC2) . chr(0xA0)
            );
        $value .= '/' . $selectedLabel;
        return $value;
    }

    /**
     * Select value in dropdown which has option groups
     *
     * @param string $value
     * @throws \Exception
     * @return void
     */
    public function setValue($value)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);
        list($group, $option) = explode('/', $value);
        $optionLocator = './/optgroup[@label="' . $group . '"]/option[contains(text(), "' . $option . '")]';
        $option = $this->_context->find($optionLocator, Locator::SELECTOR_XPATH);
        if (!$option->isVisible()) {
            throw new \Exception('[' . implode('/', $option) . '] option is not visible in optgroup dropdown.');
        }
        $option->click();
    }
}
