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
    protected $dropDownButton = '#switcher-language-trigger';

    /**
     * StoreView selector
     *
     * @var string
     */
    protected $stroViewSelector = 'li.view-%s';

    /**
     * Select store
     *
     * @param string $name
     */
    public function selectStoreView($name)
    {
        if ($this->_rootElement->find($this->dropDownButton)->isVisible() && ($this->getStoreView() !== $name)) {
            $this->_rootElement->find($this->dropDownButton)->click();
            $this->_rootElement->find($name, Locator::SELECTOR_LINK_TEXT)->click();
        }
    }

    /**
     * Get store view
     *
     * @return string
     */
    public function getStoreView()
    {
        return $this->_rootElement->find($this->dropDownButton)->getText();
    }

    /**
     * Check  is Store View Visible
     *
     * @param string $storeCode
     * @return boolean
     */
    public function isStoreViewVisible($storeCode)
    {
        $this->_rootElement->find($this->dropDownButton)->click();
        return $this->_rootElement->find(sprintf($this->stroViewSelector, $storeCode))->isVisible();
    }
}
