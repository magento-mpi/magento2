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
 * Class PromoQuoteNew
 */
class PromoQuoteNew extends BackendPage
{
    const MCA = 'sales_rule/promo_quote/new';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'salesRuleForm' => [
            'class' => 'Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\PromoQuoteForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'conditionsTab' => [
            'class' => 'Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions',
            'locator' => '#conditions__1__children',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\PromoQuoteForm
     */
    public function getSalesRuleForm()
    {
        return $this->getBlockInstance('salesRuleForm');
    }

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions
     */
    public function getConditionsTab()
    {
        return $this->getBlockInstance('conditionsTab');
    }
}
