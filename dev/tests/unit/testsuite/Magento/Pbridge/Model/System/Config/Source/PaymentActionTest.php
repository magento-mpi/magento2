<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\System\Config\Source;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PaymentActionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pbridge\Model\System\Config\Source\PaymentAction */
    protected $paymentAction;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->paymentAction = $this->objectManagerHelper->getObject(
            'Magento\Pbridge\Model\System\Config\Source\PaymentAction'
        );
    }

    public function testToOptionArray()
    {
        $expected = [
            ['value' => AbstractMethod::ACTION_AUTHORIZE, 'label' => __('Authorize Only')],
            ['value' => AbstractMethod::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Authorize and Capture')],
        ];
        $this->assertEquals($expected, $this->paymentAction->toOptionArray());
    }
}
