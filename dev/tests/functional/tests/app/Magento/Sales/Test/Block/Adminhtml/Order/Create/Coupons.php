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

/**
 * Class Coupons
 * Customer selection grid
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
    protected $applyButton = 'button[title="Apply"]';

    /**
     * Enter discount code and click apply button
     *
     * @param string $code
     * @return void
     */
    public function applyCouponCode($code)
    {
        $this->_rootElement->find($this->couponCode)->setValue($code);
        $this->_rootElement->find($this->applyButton)->click();
    }
}
