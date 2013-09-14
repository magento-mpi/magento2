<?php
/**
 * Test for \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Block_Adminhtml_Role_Edit_Tab_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Rule|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleResource;

    /**
     * @var \Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource
     */
    protected $_block;

    protected function setUp()
    {
        $this->_ruleResource = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\Rule')
            ->disableOriginalConstructor()
            ->setMethods(array('getResourceIdsByRole'))
            ->getMock();

        $rootResource = new \Magento\Core\Model\Acl\RootResource('Magento_Webapi');

        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_block = $helper->getObject('Magento\Webapi\Block\Adminhtml\Role\Edit\Tab\Resource', array(
            'ruleResource' => $this->_ruleResource,
            'rootResource' => $rootResource
        ));
    }

    /**
     * Test isEverythingAllowed method.
     *
     * @dataProvider isEverythingAllowedDataProvider
     * @param array $selectedResources
     * @param bool $expectedResult
     */
    public function testIsEverythingAllowed($selectedResources, $expectedResult)
    {
        $apiRole = new \Magento\Object(array(
            'role_id' => 1
        ));
        $apiRole->setIdFieldName('role_id');

        $this->_block->setApiRole($apiRole);

        $this->_ruleResource->expects($this->once())
            ->method('getResourceIdsByRole')
            ->with($apiRole->getId())
            ->will($this->returnValue($selectedResources));

        $this->assertEquals($expectedResult, $this->_block->isEverythingAllowed());
    }

    /**
     * @return array
     */
    public function isEverythingAllowedDataProvider()
    {
        return array(
            'Not everything is allowed' => array(
                array('customer', 'customer/get'),
                false
            ),
            'Everything is allowed' => array(
                array('customer', 'customer/get', 'Magento_Webapi'),
                true
            )
        );
    }
}
