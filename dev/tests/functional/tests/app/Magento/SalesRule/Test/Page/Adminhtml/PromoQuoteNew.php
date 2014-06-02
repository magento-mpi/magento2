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
            'class' => 'Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Form',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageActions',
            'class' => 'Magento\Backend\Test\Block\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'conditionsTab' => [
            'name' => 'conditionsTab',
            'class' => 'Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Tab\Conditions',
            'locator' => '#conditions__1__children',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\SalesRule\Test\Block\Adminhtml\Promo\Quote\Edit\Form
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
