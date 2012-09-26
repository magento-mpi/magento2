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
     * Set up before test
     */
    protected function setUp()
    {
        $this->_policyMock = $this->getMock('Magento_Authorization_Policy', array(), array(), '', false);
        $objectFactory = $this->getMock('Varien_Object', array(), array(), '', false);
        $data = array(
            'policy' => $this->_policyMock,
            'objectFactory' => $objectFactory
        );
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
            'resourceIsAllowed' => array(
                'resource' => 'customer',
                'operation' => 'get',
                'aclResource' => 'customer/get',
                'isAllowedResource' => true,
                'isAllowedRoot' => false,
                'expected' => true,
            ),
            'rootIsAllowed' => array(
                'resource' => 'customer',
                'operation' => 'get',
                'aclResource' => 'customer/get',
                'isAllowedResource' => false,
                'isAllowedRoot' => true,
                'expected' => true,
            ),
            'notAllowed' => array(
                'resource' => 'customer',
                'operation' => 'get',
                'aclResource' => 'customer/get',
                'isAllowedResource' => false,
                'isAllowedRoot' => false,
                'expected' => false,
            )
        );
    }

    /**
     * Test for Mage_Webapi_Model_Authorization::isAllowed
     *
     * @param string $resource
     * @param string $operation
     * @param string $aclResource
     * @param bool $isAllowedResource
     * @param bool $isAllowedRoot
     * @param bool $expected
     *
     * @dataProvider isAllowedDataProvider
     */
    public function testIsAllowed($resource, $operation, $aclResource, $isAllowedResource, $isAllowedRoot, $expected)
    {
        $this->_policyMock->expects($this->at(0))->method('isAllowed')
            ->with($this->_roleId, $aclResource)
            ->will($this->returnValue($isAllowedResource));
        if (!$isAllowedResource) {
            $this->_policyMock->expects($this->at(1))->method('isAllowed')
                ->with($this->_roleId, Mage_Webapi_Model_Acl_Rule::API_ACL_RESOURCES_ROOT_ID)
                ->will($this->returnValue($isAllowedRoot));
        }
        $this->assertEquals($expected, $this->_model->isAllowed($this->_roleId, $resource, $operation));
    }
}
