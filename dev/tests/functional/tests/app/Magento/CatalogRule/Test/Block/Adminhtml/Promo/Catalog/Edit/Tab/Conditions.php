<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;
use Mtf\Factory\Factory;


/**
 * Class Conditions
 * Form Tab for specifying catalog price rule conditions
 *
 * @package Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog\Edit\Tab
 */
class Conditions extends Tab
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

    const RULE_CONDITIONS_FIELDSET = 'rule_conditions_fieldset';

    /**
     * Fill condition options
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $data = $this->dataMapping($fields);

        $conditionsBlock = $this->getConditionsBlock($element);
        $conditionsBlock->clickAddNew();

        $conditionsBlock->selectCondition($this->getConditionType($data));
        $conditionsBlock->clickEllipsis();
        $conditionsBlock->selectConditionValue($this->getConditionValue($data));
    }

    /**
     * @param \Mtf\Client\Element $element
     * @return \Magento\CatalogRule\Test\Block\Conditions block
     */
    public function getConditionsBlock(Element $element)
    {
        return Factory::getBlockFactory()->getMagentoCatalogRuleConditions(
            $element->find('#' . self::RULE_CONDITIONS_FIELDSET)
        );
    }

    /**
     * Get condition type
     * @param array $data
     * @return string
     */
    public function getConditionType($data)
    {
        return $data[self::CONDITION_TYPE]['value'];
    }

    /**
     * Get condition value
     * @param array $data
     * @return string
     */
    public function getConditionValue($data)
    {
        return $data[self::CONDITION_VALUE]['value'];
    }
}
