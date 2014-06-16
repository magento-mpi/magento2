<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\System\Config\Source\Payone;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Payment\Model\Method\AbstractMethod;

class PaymentActionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pbridge\Model\System\Config\Source\Payone\PaymentAction */
    protected $paymentAction;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->paymentAction = $this->objectManagerHelper->getObject(
            'Magento\Pbridge\Model\System\Config\Source\Payone\PaymentAction'
        );
    }

    public function testToOptionArray()
    {
        $expected = [
            ['value' => AbstractMethod::ACTION_AUTHORIZE, 'label' => __('Preauthorization')],
            ['value' => AbstractMethod::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Authorization')],
        ];
        $this->assertEquals($expected, $this->paymentAction->toOptionArray());
    }
}
