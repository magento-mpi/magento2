<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Page;

use Magento\Customer\Test\Page\CustomerAccountCreate as ParentCustomerAccountCreate;

/**
 * Class CustomerAccountCreate
 */
class CustomerAccountCreate extends ParentCustomerAccountCreate
{
    const MCA = 'reward_customer/account/create';

    /**
     * Initialize page
     *
     * @return void
     */
    protected function _init()
    {
        parent::_init();
        $this->_blocks['tooltipBlock'] = [
            'name' => 'tooltipBlock',
            'class' => 'Magento\Reward\Test\Block\Tooltip',
            'locator' => '.customer-form-before',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\Reward\Test\Block\Tooltip
     */
    public function getTooltipBlock()
    {
        return $this->getBlockInstance('tooltipBlock');
    }
}
