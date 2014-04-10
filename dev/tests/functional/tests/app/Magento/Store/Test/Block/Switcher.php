<?php
/**
 * Language switcher
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Store\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

class Switcher extends Block
{
    /**
     * Dropdown button selector
     *
     * @var string
     */
    protected $dropDownButton = 'button';

    /**
     * Select store
     *
     * @param string $name
     */
    public function selectStoreView($name)
    {
        $this->_rootElement->find($this->dropDownButton, Locator::SELECTOR_TAG_NAME)->click();
        $this->_rootElement->find($name, Locator::SELECTOR_LINK_TEXT)->click();
    }
}
