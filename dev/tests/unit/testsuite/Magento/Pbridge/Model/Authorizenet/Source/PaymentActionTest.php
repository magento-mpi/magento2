<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model\Authorizenet\Source;

class PaymentActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Pbridge\Model\Authorizenet\Source\PaymentAction
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new \Magento\Pbridge\Model\Authorizenet\Source\PaymentAction();
    }

    /**
     * Check if \Magento\Pbridge\Model\Authorizenet\Source\PaymentAction has method toOptionArray
     */
    public function testToOptionArrayExistence()
    {
        $this->assertTrue(method_exists($this->_model, 'toOptionArray'), 'Required method toOptionArray not exists');
    }

    /**
     * Check output format
     * @depends testToOptionArrayExistence
     */
    public function testToOptionArrayFormat()
    {
        $options = $this->_model->toOptionArray();
        $expected = array(
            array(
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize Only')
            ),
            array(
                'value' => \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            )
        );
        $this->assertEquals($options, $expected);
    }
}
