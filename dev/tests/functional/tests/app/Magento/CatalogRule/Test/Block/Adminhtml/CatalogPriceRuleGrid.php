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

namespace Magento\CatalogRule\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class CatalogPriceRuleGrid
 * Backend catalog price rule grid
 *
 * @package Magento\CatalogRule\Test\Block\Backend
 */
class CatalogPriceRuleGrid extends Grid
{
    /**
     * 'Add New' catalog rule button
     *
     * @var string
     */
    protected $addNewCatalogRule = "../*[@class='page-actions']//*[@id='add']";

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => array(
                'selector' => '#promo_catalog_grid_filter_name'
            ),
        );
    }

    /**
     * Add new catalog rule
     */
    public function addNewCatalogRule()
    {
        $this->_rootElement->find($this->addNewCatalogRule, Locator::SELECTOR_XPATH)->click();
    }
}
