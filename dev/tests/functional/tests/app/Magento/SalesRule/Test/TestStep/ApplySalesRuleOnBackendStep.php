<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesRule\Test\TestStep;

use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Mtf\TestStep\TestStepInterface;

/**
 * Class ApplySalesRuleOnBackendStep
 * Apply Sales Rule on backend
 */
class ApplySalesRuleOnBackendStep implements TestStepInterface
{
    /**
     * Order Create Index
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * SalesRule fixture
     *
     * @var SalesRuleInjectable
     */
    protected $salesRule;

    /**
     * @constructor
     * @param OrderCreateIndex $orderCreateIndex
     * @param SalesRuleInjectable $salesRule
     */
    public function __construct(OrderCreateIndex $orderCreateIndex, SalesRuleInjectable $salesRule = null)
    {
        $this->orderCreateIndex = $orderCreateIndex;
        $this->salesRule = $salesRule;
    }

    /**
     * Apply gift card before one page checkout
     *
     * @return void
     */
    public function run()
    {
        if ($this->salesRule !== null) {
            $this->orderCreateIndex->getCouponsBlock()->applyCouponCode($this->salesRule);
        }
    }
}
