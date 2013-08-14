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
        $mockHelper = $this->getMock('Enterprise_Pbridge_Helper_Data', array(), array(), '', false, false);
        $mockHelper->expects($this->any())->method('__')->will($this->returnValue('Test Label'));
        $this->_model= new Enterprise_Pbridge_Model_Authorizenet_Source_PaymentAction($mockHelper);
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
                'label' => 'Test Label'
            ),
            array(
                'value' => Magento_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE,
                'label' => 'Test Label'
            ),
        );
        $this->assertEquals($options, $expected);
    }
}
