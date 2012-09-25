<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Webapi_Model_Authorization.
 */
class Mage_Webapi_Model_AuthorizationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Authorization model
     *
     * @var Mage_Webapi_Model_Authorization
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_policyMock;

    /**
     * @var int
     */
    protected $_roleId = 5;

    /**
     * @var string
     */
    protected $_resource = 'customer';

    /**
     * @var string
     */
    protected $_operation = 'get';

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $this->_policyMock = $this->getMock('Magento_Authorization_Policy', array(), array(), '', false);
        $data = array('policy' => $this->_policyMock);
        $this->_model = new Mage_Webapi_Model_Authorization($data);
    }

    /**
     * Tear down after test
     */
    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Data provider for testIsAllowed()
     *
     * @return array
     */
    public function isAllowedDataProvider()
    {
        return array(
            'returnPositiveValue' => array(
                'isAllowed' => true,
                'expected' => true
            ),
            'returnNegativeValue' => array(
                'isAllowed' => false,
                'expected' => false
            )
        );
    }

    /**
     * Test for Mage_Webapi_Model_Authorization::isAllowed
     *
     * @param $isAllowed
     * @param $expected
     *
     * @dataProvider isAllowedDataProvider
     */
    public function testIsAllowed($isAllowed, $expected)
    {
        $this->_policyMock->expects($this->any())->method('isAllowed')->will($this->returnValue($isAllowed));
        $this->assertEquals($expected, $this->_model->isAllowed($this->_roleId, $this->_resource, $this->_operation));
    }
}
