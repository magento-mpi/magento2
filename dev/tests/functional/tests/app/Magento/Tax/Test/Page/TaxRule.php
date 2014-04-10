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
use Magento\Backend\Test\Block\GridPageActions;

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
     * Tax rules grid
     *
     * @var string
     */
    protected $taxRuleGrid = '#taxRuleGrid';

    /**
     * Grid page actions block
     *
     * @var string
     */
    protected $pageActionsBlock = '.page-main-actions';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get tax rules grid
     *
     * @return \Magento\Tax\Test\Block\Adminhtml\Rule\Grid
     */
    public function getRuleGrid()
    {
        return Factory::getBlockFactory()->getMagentoTaxAdminhtmlRuleGrid(
            $this->_browser->find($this->taxRuleGrid, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get Grid page actions block
     *
     * @return GridPageActions
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendGridPageActions(
            $this->_browser->find($this->pageActionsBlock)
        );
    }
}
