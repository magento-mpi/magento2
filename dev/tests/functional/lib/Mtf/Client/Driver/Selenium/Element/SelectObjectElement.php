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
 * Class SelectObjectElement
 * Typified element class for option group selectors
 */
class SelectObjectElement extends Element
{
    /**
     * Select value in dropdown which has option groups
     *
     * @param array $value
     * @throws \Exception
     * @return void
     */
    public function setValue($value)
    {
        if (!isset($value['group']) && !isset($value['store'])) {
            throw new \Exception('Not correct format of values.');
        }
        $optionLocator = './/optgroup[contains(@label,"'
            . $value['group'] . '")]/option[contains(text(), "'
            . $value['store'] . '")]';
        $option = $this->_context->find($optionLocator, Locator::SELECTOR_XPATH);
        if (!$option->isVisible()) {
            throw new \Exception('[' . implode('/', $value) . '] option is not visible in store switcher.');
        }
        $option->click();
    }
}
