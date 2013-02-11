<?php
/**
 * Test class for Enterprise_PageCache_Model_DesignPackage_Info
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_DesignPackage_InfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fpcCacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontendMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_packageRulesMock;

    protected function setUp()
    {
        $this->_frontendMock = $this->getMock('Zend_Cache_Core', array(), array(), '', false);

        $this->_packageRulesMock = $this->getMock('Enterprise_PageCache_Model_DesignPackage_Rules', array(),
            array(), '', false
        );
        $this->_fpcCacheMock = $this->getMock('Enterprise_PageCache_Model_Cache', array(), array(), '', false);

        $this->_fpcCacheMock->expects($this->once())
            ->method('getFrontend')
            ->will($this->returnValue($this->_frontendMock));

        $this->_fpcCacheMock->expects($this->once())
            ->method('load')
            ->with(Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY)
            ->will($this->returnValue('some_cache'));
    }

    public function testGetPackageName()
    {
        $this->_packageRulesMock
            ->expects($this->once())
            ->method('getPackageByUserAgent')
            ->with('some_cache')
            ->will($this->returnValue('test_package'));

        $model = new Enterprise_PageCache_Model_DesignPackage_Info($this->_fpcCacheMock, $this->_packageRulesMock);
        $this->assertEquals('test_package', $model->getPackageName());
    }

    public function testIsDesignExceptionExistsInCache()
    {
        $this->_frontendMock
            ->expects($this->once())
            ->method('test')
            ->with(Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY)
            ->will($this->returnValue('some_value'));

        $model = new Enterprise_PageCache_Model_DesignPackage_Info($this->_fpcCacheMock, $this->_packageRulesMock);
        $this->assertEquals('some_value', $model->isDesignExceptionExistsInCache());
    }
}
