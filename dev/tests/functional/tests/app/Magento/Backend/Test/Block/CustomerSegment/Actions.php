<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\CustomerSegment;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Actions
 * Segment actions block
 *
 * @package Magento\Backend\Test\Block\CustomerSegment
 */
class Actions extends Block {
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
     * Add image click
     */
    public function clickAddNew()
    {
        $this->_rootElement->find('img.rule-param-add.v-middle')->click();
    }

    /**
     * Ellipsis image click
     */
    public function clickEllipsis()
    {
        $this->_rootElement->find('//a[contains(text(),"...")]', Locator::SELECTOR_XPATH)->click();
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
        $this->_rootElement->find($this->conditionValueSelector, Locator::SELECTOR_ID, 'select')->setValue($value);
    }

    /**
     * Click save and continue button on form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find('#save_and_continue_edit')->click();
    }
}