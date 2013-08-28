<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Pbridge_Model_Authorizenet_Source_PaymentActionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Pbridge_Model_Authorizenet_Source_PaymentAction
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Enterprise_Pbridge_Model_Authorizenet_Source_PaymentAction();
    }

    /**
     * Check if Enterprise_Pbridge_Model_Authorizenet_Source_PaymentAction has method toOptionArray
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
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE,
                'label' => __('Authorize Only')
            ),
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize and Capture')
            ),
        );
        $this->assertEquals($options, $expected);
    }
}
