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

/**
 * Class SalesRule
 *
 * @package Magento\SalesRule\Test\Page
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
    protected $clickAddNewSelector = '.page-actions button#add';

    /**
     * @var string
     */
    protected $promoQuoteGridSelector = 'promo_quote_grid';

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
     * Click the add new button
     */
    public function clickAddNew()
    {
        $this->_browser->find($this->clickAddNewSelector)->click();
        // Wait for the current grid to go away, replaced by the new form
        $this->getPromoQuoteGrid()->waitForElementNotVisible($this->clickAddNewSelector);
    }
}
