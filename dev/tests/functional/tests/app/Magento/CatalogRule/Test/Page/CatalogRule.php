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

namespace Magento\CatalogRule\Test\Page;

use Magento\Backend\Test\Block\PageActions;
use Magento\CatalogRule\Test\Block\Adminhtml\CatalogPriceRuleGrid;
use Magento\Core\Test\Block\Messages;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CatalogRule
 * CatalogRule grid page.
 *
 * @package Magento\CatalogRule\Test\Page
 */
class CatalogRule extends Page
{
    /**
     * URL for catalog price rules grid
     */
    const MCA = 'catalog_rule/promo_catalog';

    /**
     * Catalog price rule grid block id
     */
    const CATALOG_RULE_GRID_ID = 'promo_catalog_grid';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get catalog price rule grid block
     *
     * @return CatalogPriceRuleGrid
     */
    public function getCatalogPriceRuleGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogRuleAdminhtmlCatalogPriceRuleGrid(
            $this->_browser->find('#' . self::CATALOG_RULE_GRID_ID)
        );
    }

    /**
     * Get page actions block
     *
     * @return PageActions
     */
    public function getPageActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Get messages block
     *
     * @return Messages
     */
    public function getMessagesBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
    }

    /**
     * Click "Apply Rule" button
     */
    public function applyRules()
    {
        $this->_browser->find('#apply_rules')->click();
    }
}
