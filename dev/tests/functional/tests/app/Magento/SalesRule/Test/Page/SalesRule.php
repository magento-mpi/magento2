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
namespace Magento\SalesRule\Test\Page;

use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

class SalesRule extends Page
{
    /**
     * URL for sales rule page
     */
    const MCA = 'sales_rule/promo_quote';

    const CLICK_ADD_NEW_SELECTOR = '.page-actions button#add';

    const PROMO_QUOTE_GRID_SELECTOR = 'promo_quote_grid';

    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get tax rules grid
     *
     * @return Quote
     */
    public function getPromoQuoteGrid()
    {
        return Factory::getBlockFactory()->getMagentoSalesRuleAdminhtmlPromoQuote(
            $this->_browser->find(self::PROMO_QUOTE_GRID_SELECTOR, Locator::SELECTOR_ID)
        );
    }

    public function clickAddNew()
    {
        $this->_browser->find(self::CLICK_ADD_NEW_SELECTOR)->click();
    }
}
