<?php
/**
 * Test class for \Magento\FullPageCache\Model\DesignPackage\Info
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model\DesignPackage;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontendMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_packageRulesMock;

    protected function setUp()
    {
        $this->_packageRulesMock = $this->getMock('Magento\FullPageCache\Model\DesignPackage\Rules', array(),
            array(), '', false
        );
        $this->_fpcCacheMock = $this->getMock('Magento\FullPageCache\Model\Cache', array(), array(), '', false);
    }

    public function testGetPackageName()
    {
        $this->_packageRulesMock
            ->expects($this->once())
            ->method('getPackageName')
            ->with(1)
            ->will($this->returnValue('test_package'));

        $model = new \Magento\FullPageCache\Model\DesignPackage\Info($this->_fpcCacheMock, $this->_packageRulesMock);

        $this->assertEquals('test_package', $model->getPackageName(1));

        /**
         * Test internal cache
         */
        $this->assertEquals('test_package', $model->getPackageName(1));
    }

    public function testIsDesignExceptionExistsInCache()
    {
        $this->_frontendMock = $this->getMock('Zend_Cache_Core', array(), array(), '', false);

        $this->_fpcCacheMock->expects($this->once())
            ->method('getFrontend')
            ->will($this->returnValue($this->_frontendMock));

        $this->_frontendMock
            ->expects($this->once())
            ->method('test')
            ->with(\Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY)
            ->will($this->returnValue('some_value'));

        $model = new \Magento\FullPageCache\Model\DesignPackage\Info($this->_fpcCacheMock, $this->_packageRulesMock);
        $this->assertEquals('some_value', $model->isDesignExceptionExistsInCache());
    }
}
