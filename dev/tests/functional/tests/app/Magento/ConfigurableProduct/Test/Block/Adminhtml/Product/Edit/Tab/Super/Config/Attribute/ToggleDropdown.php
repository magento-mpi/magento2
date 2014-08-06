<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Attribute;

use Mtf\Client\Element\Locator;
use Mtf\Client\Driver\Selenium\Element;

/**
 * Class ToggleDropdown
 * General class for toggle dropdown elements.
 */
class ToggleDropdown extends Element
{
    /**
     * Selector for list options
     *
     * @var string
     */
    protected $listOptions = './../ul[@data-role="dropdown-menu"]';

    /**
     * Selector for search option by text
     *
     * @var string
     */
    protected $optionByText = './../ul[@data-role="dropdown-menu"]/li/a[.="%s"]';

    /**
     * Set value
     *
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->_eventManager->dispatchEvent(['set_value'], [__METHOD__, $this->getAbsoluteSelector()]);

        if ($value != $this->getValue()) {
            $this->click();
            $this->waitListOptionsVisible();
            $this->find(sprintf($this->optionByText, $value), Locator::SELECTOR_XPATH)->click();
        }
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getValue()
    {
        $this->_eventManager->dispatchEvent(['get_value'], [(string)$this->_locator]);

        return $this->find('span')->getText();
    }

    /**
     * Wait visible list options
     *
     * @return void
     */
    protected function waitListOptionsVisible()
    {
        $browser = $this;
        $selector = $this->listOptions;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                return $browser->find($selector, Locator::SELECTOR_XPATH)->isVisible() ? true : null;
            }
        );
    }
}

