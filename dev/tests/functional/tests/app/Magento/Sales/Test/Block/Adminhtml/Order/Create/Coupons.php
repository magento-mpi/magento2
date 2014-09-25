<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Magento\Backend\Test\Block\Widget\Form;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Mtf\Client\Element\Locator;

/**
 * Class Coupons
 * Adminhtml sales order create coupons block
 */
class Coupons extends Form
{
    /**
     * Fill discount code input selector
     *
     * @var string
     */
    protected $couponCode = 'input[name="coupon_code"]';

    /**
     * Click apply button selector
     *
     * @var string
     */
    protected $applyButton = '//*[@id="coupons:code"]/following-sibling::button';

    /**
     * Enter discount code and click apply button
     *
     * @param SalesRuleInjectable $code
     * @return void
     */
    public function applyCouponCode(SalesRuleInjectable $code)
    {
        $this->_rootElement->find($this->couponCode)->setValue($code->getCouponCode());
        $this->_rootElement->find($this->applyButton, Locator::SELECTOR_XPATH)->click();
    }
}
