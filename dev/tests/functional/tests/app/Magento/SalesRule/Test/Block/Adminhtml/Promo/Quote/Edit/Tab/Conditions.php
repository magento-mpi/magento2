<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\SalesRule\Test\Fixture\SalesRule;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Conditions
 *
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class Conditions extends Tab
{
    /**
     * Field Prefix Constant
     */
    const FIELD_PREFIX = '#conditions__1__';

    /**
     * Customer Segment Constant
     */
    const CUSTOMER_SEGMENT = 'Customer Segment';

    /**
     * Group Name Constant
     */
    const GROUP = 'promo_catalog_edit_tabs_conditions_section_content';

    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

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
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Set the mapping and fill the form
     *
     * @param array $fields
     * @param Element|null $element
     *
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields as $key => $value) {
            $this->mapping[$key] = self::FIELD_PREFIX . $key;
        }
        return parent::fillFormTab($fields, $element);
    }

    /**
     * Add a customer segment condition
     *
     * @param FixtureInterface $fixture
     * @param int $customerSegmentId
     */
    public function addCustomerSegmentCondition(FixtureInterface $fixture, $customerSegmentId)
    {
        if ($fixture instanceof SalesRule) {
            // Add new condition
            $this->clickAddNew();
            // Select Customer Segment
            $this->selectCondition(self::CUSTOMER_SEGMENT);
            // Click ellipsis
            $this->clickEllipsis();
            // Set Customer Segment Id
            $this->selectConditionValue($customerSegmentId);
            // Apply change
            $this->clickApply();
        }
    }

    /**
     * Add New Condition
     */
    public function clickAddNew()
    {
        $this->_rootElement->find('img.rule-param-add.v-middle')->click();
        $this->getTemplateBlock()->waitLoader();
        $this->reinitRootElement();
    }

    /**
     * Select Condition
     */
    public function selectCondition($type)
    {
        $this->_rootElement->find($this->conditionSelector, Locator::SELECTOR_ID, 'select')->setValue($type);
        $this->getTemplateBlock()->waitLoader();
        $this->reinitRootElement();
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
        $this->getTemplateBlock()->waitLoader();
        $this->reinitRootElement();
    }

    /**
     * Click on the apply condition value button
     */
    public function clickApply()
    {
        $this->_rootElement->find('//a[@class="rule-param-apply"]', Locator::SELECTOR_XPATH)->click();
        $this->getTemplateBlock()->waitLoader();
        $this->reinitRootElement();
    }
}
