<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Test\Page;

use Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote;
use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\GridPageActions;

/**
 * Class SalesRule
 *
 */
class SalesRule extends Page
{
    /**
     * URL for sales rule page
     */
    const MCA = 'sales_rule/promo_quote';

    /**
     * @var string
     */
    protected $promoQuoteGridSelector = 'page:main-container';

    /**
     * Grid page actions block
     *
     * @var string
     */
    protected $pageActionsBlock = '.page-main-actions';

    /**
     * {@inheritDoc}
     */
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
            $this->_browser->find($this->promoQuoteGridSelector, Locator::SELECTOR_ID)
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
