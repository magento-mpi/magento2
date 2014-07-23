<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit\Tab\MatchedCustomers;

/**
 * Class CustomerSegmentForm
 * Backend CustomerSegment form
 */
class CustomerSegmentForm extends FormTabs
{
    /**
     * Add button
     *
     * @var string
     */
    protected $addButton = '.rule-param-new-child a';

    /**
     * Locator for Customer Segment Conditions
     *
     * @var string
     */
    protected $conditionFormat = '//*[@id="conditions__1__new_child"]//option[contains(.,"%s")]';

    /**
     * Get number of customer on navigation tabs
     *
     * @return int
     */
    public function getNumberOfCustomersOnTabs()
    {
        $customerLink = $this->_rootElement->find($this->tabs['matched_customers']['selector'], Locator::SELECTOR_CSS)
            ->getText();
        preg_match('`\((\d*?)\)`', $customerLink, $customersCount);
        return (int) $customersCount[1];
    }

    /**
     * Get Matched Customers tab
     *
     * @return MatchedCustomers
     */
    public function getMatchedCustomers()
    {
        return $this->getTabElement('matched_customers');
    }

    /**
     * Fill form with tabs
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @param array|null $replace
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null, array $replace = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        if ($replace) {
            $tabs = $this->prepareData($tabs, $replace);
        }
        return $this->fillTabs($tabs, $element);
    }

    /**
     * Replace placeholders in each values of data
     *
     * @param array $tabs
     * @param array $replace
     * @return array
     */
    protected function prepareData(array $tabs, array $replace)
    {
        foreach ($tabs as $tabName => $fields) {
            foreach ($fields as $key => $pairs) {
                if (isset($replace[$tabName])) {
                    $tabs[$tabName][$key]['value'] = str_replace(
                        array_keys($replace[$tabName]),
                        array_values($replace[$tabName]),
                        $tabs[$tabName][$key]['value']
                    );
                }
            }
        }
        return $tabs;
    }

    /**
     * Check if customer attribute is available in conditions of customer segment
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @return bool
     */
    public function isAttributeInConditions(CustomerCustomAttribute $customerAttribute)
    {
        $this->_rootElement->find($this->addButton, Locator::SELECTOR_CSS)->click();
        $frontendLabel = $customerAttribute->getFrontendLabel();
        $condition = $this->_rootElement->find(
            sprintf($this->conditionFormat, $frontendLabel),
            Locator::SELECTOR_XPATH
        )->getValue();
        $pieces = explode("|", $condition);
        $formAttributeCode = end($pieces);
        $fixtureAttributeCode = $customerAttribute->getAttributeCode();
        return ($formAttributeCode == $fixtureAttributeCode);
    }
}
