<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogRule\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;


/**
 * Class Conditions
 * Catalog price rule conditions
 *
 * @package Magento\Rule\Block
 */
class Conditions extends Block
{
    /**
     * Condition selector
     *
     * @var string
     */
    const CONDITION_TYPE = 'conditions__1__new_child';

    /**
     * Condition value selector
     *
     * @var string
     */
    const CONDITION_VALUE = 'conditions__1--1__value';

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
     * Select Condition type
     * @param  string $type
     */
    public function selectCondition($type)
    {
        $this->_rootElement->find(self::CONDITION_TYPE, Locator::SELECTOR_ID, 'select')->setValue($type);
    }

    /**
     * Select Condition value
     * @param  string $value
     */
    public function selectConditionValue($value)
    {
        $this->_rootElement->find(self::CONDITION_VALUE, Locator::SELECTOR_ID, 'input')->setValue($value);
    }

    /**
     * Click save and continue button on form
     */
    public function clickSaveAndContinue()
    {
        $this->_rootElement->find('#save_and_continue_edit')->click();
    }
}