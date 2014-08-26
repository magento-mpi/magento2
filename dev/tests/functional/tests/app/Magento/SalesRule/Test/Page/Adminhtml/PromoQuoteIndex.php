<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class PromoQuoteIndex
 */
class PromoQuoteIndex extends BackendPage
{
    const MCA = 'sales_rule/promo_quote/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'promoQuoteGrid' => [
            'class' => 'Magento\SalesRule\Test\Block\Adminhtml\Promo\Grid',
            'locator' => '#promo_quote_grid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'gridPageActions' => [
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\SalesRule\Test\Block\Adminhtml\Promo\Grid
     */
    public function getPromoQuoteGrid()
    {
        return $this->getBlockInstance('promoQuoteGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }
}
