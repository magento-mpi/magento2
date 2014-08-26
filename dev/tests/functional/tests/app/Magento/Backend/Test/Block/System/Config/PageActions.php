<?php
/**
 * Config actions block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Config;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\FormPageActions as AbstractPageActions;

/**
 * Class PageActions
 * System config page action
 */
class PageActions extends AbstractPageActions
{
    /**
     * Scope CSS selector
     *
     * @var string
     */
    protected $scopeSelector = '.actions.dropdown';

    /**
     * Store dropdown selector
     *
     * @var string
     */
    protected $dropDownselector = '[data-toggle="dropdown"]';

    /**
     * Select store
     *
     * @param array $websiteScope
     * @return $this
     */
    public function selectStore($websiteScope)
    {
        $this->_rootElement->find($this->scopeSelector, Locator::SELECTOR_CSS, 'liselect')->setValue($websiteScope);
        $this->_rootElement->acceptAlert();

        return $this;
    }

    /**
     * Check if store visible in scope dropdown
     *
     * @param string $storeName
     * @return boolean
     */
    public function isStoreVisible($storeName)
    {
        $this->_rootElement->find($this->dropDownselector)->click();
        $storeViews = explode("\n", $this->_rootElement->find($this->scopeSelector)->getText());

        return in_array($storeName, $storeViews);
    }
}
