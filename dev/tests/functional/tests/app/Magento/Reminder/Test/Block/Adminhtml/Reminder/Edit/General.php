<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reminder\Test\Block\Adminhtml\Reminder\Edit;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\SalesRule\Test\Block\Adminhtml\Promo\Grid as SalesRuleGrid;

/**
 * "Rule Information" tab.
 */
class General extends \Magento\Backend\Test\Block\Widget\Tab
{
    /**
     * Locator for "Select Rule" button.
     *
     * @var string
     */
    protected $selectRuleButton = './/button[contains(.,"Select Rule")]';

    /**
     * Locator for loader.
     *
     * @var string
     */
    protected $loader = '#loading-mask';

    /**
     * Locator for popup "Select Rule" grid.
     *
     * @var string
     */
    protected $popupSelectRuleGrid = './/div[contains(@class,"popup-window")]';

    /**
     * Locator for sales rule label.
     *
     * @var string
     */
    protected $salesRuleIdLabel = '#salesrule_id + label';

    /**
     * Fill data to fields on tab.
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $data = $this->dataMapping($fields);
        if (isset($data['salesrule_id'])) {
            $this->selectRule();
            $this->waitForElementNotVisible($this->loader);
            $this->getSalesRuleGrid()->searchAndSelect(['name' => $data['salesrule_id']['value']]);

            $this->setFields['salesrule_id'] = $data['salesrule_id']['value'];
            unset($data['salesrule_id']);
        }
        $this->_fill($data, $element);

        return $this;
    }

    /**
     * Get data of tab.
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $data = $this->dataMapping($fields);
        $salesRuleData = [];

        if (isset($data['salesrule_id'])) {
            $salesRuleId = trim($this->_rootElement->find($this->salesRuleIdLabel)->getText());

            unset($data['salesrule_id']);
            $salesRuleData = ['salesrule_id' => $salesRuleId];
        }

        return array_merge($this->_getData($data, $element), $salesRuleData);
    }


    /**
     * Click "Select Rule" button.
     *
     * @return void
     */
    protected function selectRule()
    {
        $this->_rootElement->find($this->selectRuleButton, Element\Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get sales rule grid.
     *
     * @return SalesRuleGrid
     */
    protected function getSalesRuleGrid()
    {
        return $this->blockFactory->create(
            '\Magento\SalesRule\Test\Block\Adminhtml\Promo\Grid',
            ['element' => $this->browser->find($this->popupSelectRuleGrid, Locator::SELECTOR_XPATH)]
        );
    }
}
