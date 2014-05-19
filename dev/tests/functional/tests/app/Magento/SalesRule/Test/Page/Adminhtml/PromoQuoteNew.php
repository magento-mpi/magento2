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
 *
 * @package Magento\SalesRule\Test\Page\Adminhtml
 */
class PromoQuoteNew extends BackendPage
{
    const MCA = 'sales_rule/promo_quote/new';

    protected $_blocks = [
        'salesRuleForm' => [
            'name' => 'salesRuleForm',
            'class' => 'Magento\SalesRule\Test\Block\Adminhtml\SalesRuleForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\SalesRule\Test\Block\Adminhtml\SalesRuleForm
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
}
