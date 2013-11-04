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