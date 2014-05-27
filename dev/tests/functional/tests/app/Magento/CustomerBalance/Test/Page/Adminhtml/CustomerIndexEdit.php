<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerBalance\Test\Page\Adminhtml;

use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit as ParentCustomerIndexEdit;

/**
 * Class CustomerIndexEdit
 * Customer index page
 */
class CustomerIndexEdit extends ParentCustomerIndexEdit
{
    //TODO remove "-balance" after fix in old test generate factory
    const MCA = 'customer-balance/index/edit';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();

        $this->_blocks = [
            'customerForm' => [
                'name' => 'customerForm',
                'class' => 'Magento\CustomerBalance\Test\Block\Adminhtml\Edit\Form',
                'locator' => '[id="page:main-container"]',
                'strategy' => 'css selector',
            ],
            'balanceHistoryGrid' => [
                'name' => 'balanceHistoryGrid',
                'class' => 'Magento\CustomerBalance\Test\Block\Adminhtml\Customer\Edit\Tab\Balance\History\Grid',
                'locator' => '#historyGrid_table',
                'strategy' => 'css selector',
            ]
        ] + $this->_blocks;
    }

    /**
     * @return \Magento\CustomerBalance\Test\Block\Adminhtml\Edit\Form
     */
    public function getCustomerForm()
    {
        return $this->getBlockInstance('customerForm');
    }

    /**
     * @return \Magento\CustomerBalance\Test\Block\Adminhtml\Customer\Edit\Tab\Balance\History\Grid
     */
    public function getBalanceHistoryGrid()
    {
        return $this->getBlockInstance('balanceHistoryGrid');
    }
}
