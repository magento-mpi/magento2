<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Model\Checkout\Type\Multishipping;

use Magento\Checkout\Model\Session;

class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $checkoutSessionMock;

    /**
     * @var \Magento\Multishipping\Model\Checkout\Type\Multishipping\Plugin
     */
    protected $model;

    protected function setUp()
    {
        $this->checkoutSessionMock = $this->getMock(
            'Magento\Checkout\Model\Session',
            ['getCheckoutState', 'setCheckoutState'],
            [],
            '',
            false
        );
        $this->model = new Plugin($this->checkoutSessionMock);
    }

    public function testBeforeInitCaseTrue()
    {
        $this->checkoutSessionMock->expects($this->once())->method('getCheckoutState')
            ->willReturn(State::STEP_SELECT_ADDRESSES);
        $this->checkoutSessionMock->expects($this->once())->method('setCheckoutState')
            ->with(Session::CHECKOUT_STATE_BEGIN);
        $this->model->beforeInit();
    }

    public function testBeforeInitCaseFalse()
    {
        $this->checkoutSessionMock->expects($this->once())->method('getCheckoutState')
            ->willReturn('');
        $this->checkoutSessionMock->expects($this->never())->method('setCheckoutState');
        $this->model->beforeInit();
    }
}
