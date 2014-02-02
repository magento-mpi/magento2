<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Core\Model\Config\Scope;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var \Magento\Core\Model\Config\Scope\Resolver
     */
    protected $_object;

    public function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_storeManager = $this->getMock('\Magento\Core\Model\StoreManagerInterface');
        $this->_object = $helper->getObject('\Magento\Core\Model\Config\Scope\Resolver', array(
            'storeManager' => $this->_storeManager,
        ));
    }

    public function testGetScopeCode()
    {
        /** @var \Magento\BaseScopeInterface|PHPUnit_Framework_MockObject_MockObject */
        $scope = $this->getMock('\Magento\BaseScopeInterface');
        $scope->expects($this->once())
            ->method('getCode')
            ->will($this->returnValue('scopeCode'));
        $this->_storeManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($scope));
        $this->assertEquals('scopeCode', $this->_object->getScopeCode());
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Invalid scope object
     */
    public function testGetScopeCodeSxception()
    {
        $this->_object->getScopeCode();
    }
}
