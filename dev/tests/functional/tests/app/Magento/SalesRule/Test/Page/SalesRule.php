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

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\PageActions;
use Magento\SalesRule\Test\Block\PromoQuoteGrid;

class SalesRule extends Page
{
    /**
     * URL for sales rule page
     */
    const MCA = 'sales_rule/promo_quote';

    /**
     * Promo Quote Grid
     *
     * @var PromoQuoteGrid
     */
    private $promoQuoteGrid;

    /**
     * Page Action Block
     *
     * @var PageActions
     */
    private $pageActionsBlock;

    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->promoQuoteGrid = Factory::getBlockFactory()->getMagentoSalesRulePromoQuoteGrid(
            $this->_browser->find('promo_quote_grid', Locator::SELECTOR_ID)
        );
        $this->pageActionsBlock = Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
    }


    /**
     * Get tax rules grid
     *
     * @return PromoQuoteGrid
     */
    public function getPromoQuoteGrid()
    {
        return $this->promoQuoteGrid;
    }
    /**
     * Getter for page actions block
     *
     * @return PageActions
     */
    public function getPageActionsBlock()
    {
        return $this->pageActionsBlock;
    }
}
