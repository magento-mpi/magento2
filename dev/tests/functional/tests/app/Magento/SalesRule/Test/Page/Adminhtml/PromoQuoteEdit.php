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
 * Class PromoQuoteEdit
 */
class PromoQuoteEdit extends BackendPage
{
    const MCA = 'sales_rule/promo_quote/edit';

    protected $_blocks = [
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'salesRuleForm' => [
            'name' => 'salesRuleForm',
            'class' => 'Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\PromoQuoteForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\PromoQuoteForm
     */
    public function getSalesRuleForm()
    {
        return $this->getBlockInstance('salesRuleForm');
    }
}
