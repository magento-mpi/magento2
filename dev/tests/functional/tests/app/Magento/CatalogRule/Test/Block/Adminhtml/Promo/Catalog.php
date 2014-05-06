<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml\Promo;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Catalog
 * Backend catalog price rule grid
 *
 * @package Magento\CatalogRule\Test\Block\Backend
 */
class Catalog extends Grid
{
    /**
     * 'Add New' catalog rule button
     *
     * @var string
     */
    protected $addNewCatalogRule = "//*[@class='page-actions']//*[@id='add']";

    /**
     * 'Apply Rules' button
     *
     * @var string
     */
    protected $applyCatalogRules = "//*[@class='page-actions']//*[@id='apply_rules']";

    /**
     * An element locator which allows to select first entity in grid
     *
     * @var string
     */
    protected $editLink = '#promo_catalog_grid_table tbody tr:first-child td';

    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#promo_catalog_grid_filter_name',
        ],
        'is_active' => [
            'selector' => '#promo_catalog_grid_filter_is_active',
            'input' => 'select',
        ],
        'rule_website' => [
            'selector' => '#promo_catalog_grid_filter_rule_website',
            'input' => 'select',
        ],
    ];

    /**
     * Add new catalog rule
     */
    public function addNewCatalogRule()
    {
        $this->_rootElement->find($this->addNewCatalogRule, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Click "Apply Rule" button
     */
    public function applyRules()
    {
        $this->_rootElement->find($this->applyCatalogRules, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Return row with given catalog price rule name
     *
     * @param string $ruleName
     * @return Element
     */
    public function getGridRow($ruleName)
    {
        return $this->getRow(array('name' => $ruleName));
    }

    /**
     * Return id of catalog price rule with given name
     *
     * @param string $ruleName
     * @return string
     */
    public function getCatalogPriceId($ruleName)
    {
        return $this->getGridRow($ruleName)->find('//td[@data-column="rule_id"]', Locator::SELECTOR_XPATH)->getText();
    }

    /**
     * Check if row exists in grid with given name
     *
     * @param string $ruleName
     * @return bool
     */
    public function isRuleVisible($ruleName)
    {
        return parent::isRowVisible(array('name' => $ruleName));
    }
}
