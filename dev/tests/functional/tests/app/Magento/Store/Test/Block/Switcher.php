<?php
/**
 * Language switcher
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Store\Test\Block;

use Magento\Store\Test\Fixture\Store;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Switcher
 * Store switcher block
 */
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
    protected $storeViewSelector = 'li.view-%s';

    /**
     * Select store
     *
     * @param string $name
     * @return void
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
     * Check is Store View Visible
     *
     * @param Store $store
     * @return bool
     */
    public function isStoreViewVisible($store)
    {
        $storeViewDropdown = $this->_rootElement->find($this->dropDownButton);

        $storeViewDropdown->click();
        $isStoreViewVisible = $this->_rootElement->find(sprintf($this->storeViewSelector, $store->getCode()))
            ->isVisible();
        $storeViewDropdown->click();
        return $isStoreViewVisible;
    }

    /**
     * Check if StoreView dropdown is visible
     *
     * @return bool
     */
    public function isStoreViewDropdownVisible()
    {
        return $this->_rootElement->find($this->dropDownButton)->isVisible();
    }
}
