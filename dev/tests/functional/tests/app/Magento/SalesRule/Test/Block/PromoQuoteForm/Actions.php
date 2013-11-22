<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Block\PromoQuoteForm;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

class Actions extends Block
{
    /**
     * Condition selector
     *
     * @var string
     */
    protected $conditionSelector = 'conditions__1__new_child';

    /**
     * Condition value selector
     *
     * @var string
     */
    protected $conditionValueSelector = 'conditions__1--1__value';

    /**
     * Add New Condition
     */
    public function clickAddNew()
    {
        $this->_rootElement->find('img.rule-param-add.v-middle')->click();
    }

    /**
     * Select Condition
     */
    public function selectCondition($type)
    {
        $this->_rootElement->find($this->conditionSelector, Locator::SELECTOR_ID, 'select')->setValue($type);
    }

    /**
     * Select Condition value
     */
    public function selectConditionValue($value)
    {
        $this->_rootElement->find($this->conditionValueSelector, Locator::SELECTOR_ID, 'input')->setValue($value);
    }

    /**
     * Ellipsis image click
     */
    public function clickEllipsis()
    {
        $this->_rootElement->find('//a[contains(text(),"...")]', Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Click on the apply condition value button
     */
    public function clickApply()
    {
        $this->_rootElement->find('//a[@class="rule-param-apply"]', Locator::SELECTOR_XPATH)->click();
    }
}
