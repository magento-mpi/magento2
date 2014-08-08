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
     * Select value in dropdown which has option groups
     *
     * @param string $value
     * @throws \Exception
     * @return void
     */
    public function setValue($value)
    {
        $optionLocator = './/option[contains(text(), "'. ucfirst($value) . '")]';
        $option = $this->_context->find($optionLocator, Locator::SELECTOR_XPATH);
        if (!$option->isVisible()) {
            throw new \Exception('[' . implode('/', $value) . '] option is not visible in store switcher.');
        }
        $option->click();
    }
}
