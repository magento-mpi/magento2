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

namespace Magento\Tax\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\PageActions;
use Magento\Tax\Test\Block\Adminhtml\Rule\Grid;

/**
 * Class TaxRule.
 * Tax rule manage grid.
 *
 * @package Magento\Customer\Test\Page
 */
class TaxRule extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'tax/rule/';

    /**
     * Page actions in backend block
     *
     * @var PageActions
     */
    private $actionsBlock;

    /**
     * Tax rules grid
     *
     * @var Grid
     */
    private $taxRuleGrid;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->actionsBlock = Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
        $this->taxRuleGrid = Factory::getBlockFactory()->getMagentoTaxAdminhtmlRuleGrid(
            $this->_browser->find('taxRuleGrid', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get tax rules grid
     *
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Grid
     */
    public function getRuleGrid()
    {
        return $this->taxRuleGrid;
    }

    /**
     * Get page actions in backend block
     *
     * @return \Magento\Backend\Test\Block\PageActions
     */
    public function getActionsBlock()
    {
        return $this->actionsBlock;
    }
}
