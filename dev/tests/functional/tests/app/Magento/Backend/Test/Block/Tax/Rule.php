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

namespace Magento\Backend\Test\Block\Tax;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;
/**
 * Class Grid
 * Tax rules grid
 *
 * @package Magento\Backend\Test\Block\Tax
 */
class Rule extends Grid
{

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => array(
                'selector' => '#taxRuleGrid_filter_code'
            ),
            'customer_tax_class' => array(
                'selector' => '#taxRuleGrid_filter_customer_tax_classes',
                'input' => 'select'
            ),
            'product_tax_class' => array(
                'selector' => '#taxRuleGrid_filter_product_tax_classes',
                'input' => 'select'
            ),
            'tax_rate' => array(
                'selector' => '#taxRuleGrid_filter_tax_rates',
                'input' => 'select'
            )
        );
    }

    /**
     * Press Reset Filter Button
     */
    public function clickResetFilter()
    {
        parent::resetFilter();
    }

    public function prepareForSearch(array $filters)
    {
        foreach ($filters as $key => $value) {
            if (isset($this->filters[$key])) {
                $selector = $this->filters[$key]['selector'];
                $strategy = isset($this->filters[$key]['strategy'])
                    ? $this->filters[$key]['strategy']
                    : Locator::SELECTOR_CSS;
                $typifiedElement = isset($this->filters[$key]['input'])
                    ? $this->filters[$key]['input']
                    : null;
                $this->_rootElement->find($selector, $strategy, $typifiedElement)->setValue($value);
            } else {
                throw new \Exception('Such column is absent in the grid or not described yet.');
            }
        }
    }

    public function clickSearchButton()
    {
        $this->_rootElement->find($this->searchButton, Locator::SELECTOR_CSS)->click();
    }

    /**
     * @param array|string $filter
     * @return bool
     */
    public function isRowVisible(array $filter)
    {
        if (count($filter) == 1) {
            $filter = implode('', $filter);
            $location = '//div[@class="grid"]//tr[td[text()[normalize-space()="' . $filter . '"]]]';
            return $this->_rootElement->find($location, Locator::SELECTOR_XPATH)->isVisible();
        }
    }
}

//div[@class="grid"]//tr[td[text()[normalize-space()="Tax Rule 1961477950"]] and td[text()[normalize-space()="Retail Customer"]] and td[text()[normalize-space()="Taxable Goods"]] and td[text()[normalize-space()="US-CA-*-Rate 1"]]]
