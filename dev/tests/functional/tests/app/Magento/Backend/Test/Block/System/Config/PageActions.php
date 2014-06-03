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
use \Magento\Backend\Test\Block\PageActions as AbstractPageActions;

class PageActions extends AbstractPageActions
{
    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '#save';

    /**
     * @var string
     */
    protected $scopeSelector = '.actions.dropdown';

    /**
     * Click "Save" button
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton)->click();
    }

    /**
     * Select store
     *
     * @param array $websiteScope
     * @return $this
     */
    public function selectStore($websiteScope)
    {
        $scope = $this->_rootElement->find($this->scopeSelector, Locator::SELECTOR_CSS, 'liselect');
        $scope->click();
        $scope->setValue($websiteScope);
        $this->_rootElement->acceptAlert();

        return $this;
    }
}
