<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog\Edit\Tab;

use Magento\CatalogRule\Test\Repository\CatalogPriceRule;
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
     * Rule conditions block selector
     *
     * @var string
     */
    protected $ruleConditions = '#rule_conditions_fieldset';

    /**
     * Fill condition options
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $data = $this->dataMapping($fields);

        $conditionsBlock = Factory::getBlockFactory()->getMagentoCatalogRuleConditions(
            $element->find($this->ruleConditions)
        );
        $conditionsBlock->clickAddNew();

        $conditionsBlock->selectCondition($data[CatalogPriceRule::CONDITION_TYPE]['value']);
        $conditionsBlock->clickEllipsis();
        $conditionsBlock->selectConditionValue($data[CatalogPriceRule::CONDITION_VALUE]['value']);
    }
}
