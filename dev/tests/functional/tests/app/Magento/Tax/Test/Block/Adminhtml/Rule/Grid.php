<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Block\Adminhtml\Rule;

use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as GridInterface;

/**
 * Class Grid
 * Tax rules grid
 *
 */
class Grid extends GridInterface
{
    /**
     * 'Add New' rule button
     *
     * @var string
     */
    protected $addNewRule = "../*[@class='page-actions']//*[@id='add']";

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
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

    /**
     * Check if specific row exists in grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return bool
     */
    public function isRowVisible(array $filter, $isSearchable = false)
    {
        $this->search(array('name' => $filter['name']));
        return parent::isRowVisible($filter, $isSearchable);
    }

    /**
     * Add new rule
     */
    public function addNewRule()
    {
        $this->_rootElement->find($this->addNewRule, Locator::SELECTOR_XPATH)->click();
    }
}