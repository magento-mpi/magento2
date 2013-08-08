<?php
/**
 * Test class for Magento_FullPageCache_Model_Processor_Restriction
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_FullPageCache_Model_Processor_RestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_FullPageCache_Model_Processor_Restriction
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheStateMock;

    /**
     * Test request id
     *
     * @var string
     */
    protected $_requestId = 'test_id';

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_environmentMock;

    protected function setUp()
    {
        $this->_environmentMock = $this->getMock(
            'Magento_FullPageCache_Model_Environment', array(), array(), '', false
        );
        $this->_cacheStateMock = $this->getMock('Magento_Core_Model_Cache_StateInterface');
        $this->_model = new Magento_FullPageCache_Model_Processor_Restriction(
            $this->_cacheStateMock, $this->_environmentMock
        );
    }

    public function testIsAllowedWithoutRequestId()
    {
        $this->_environmentMock->expects($this->never())->method('getServer');
        $this->_environmentMock->expects($this->never())->method('hasCookie');
        $this->_environmentMock->expects($this->never())->method('hasQuery');
        $this->_cacheStateMock->expects($this->never())->method('isEnabled');

        $this->assertFalse($this->_model->isAllowed(''));
    }

    public function testIsAllowedInIsDeniedMode()
    {
        $this->_environmentMock->expects($this->never())->method('getServer');
        $this->_environmentMock->expects($this->never())->method('hasCookie');
        $this->_environmentMock->expects($this->never())->method('hasQuery');
        $this->_cacheStateMock->expects($this->never())->method('isEnabled');

        $this->_model->setIsDenied();
        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithHTTPS()
    {
        $this->_environmentMock->expects($this->never())->method('hasCookie');
        $this->_environmentMock->expects($this->never())->method('hasQuery');
        $this->_cacheStateMock->expects($this->never())->method('isEnabled');

        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('on'));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithCookie()
    {
        $this->_environmentMock->expects($this->never())->method('hasQuery');
        $this->_cacheStateMock->expects($this->never())->method('isEnabled');

        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithNoCacheInGetParam()
    {
        $this->_cacheStateMock->expects($this->never())->method('isEnabled');

        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
            ->will($this->returnValue(false));

        $this->_environmentMock->expects($this->once())
            ->method('hasQuery')
            ->with('no_cache')
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithSIDInGetParam()
    {
        $this->_cacheStateMock->expects($this->never())->method('isEnabled');

        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
            ->will($this->returnValue(false));


        $valueMap = array(
            array('no_cache', false),
            array(Magento_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM, true),
        );

        $this->_environmentMock->expects($this->exactly(2))
            ->method('hasQuery')
            ->will($this->returnValueMap($valueMap));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithDisabledCache()
    {
        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
            ->will($this->returnValue(false));

        $this->_environmentMock->expects($this->exactly(2))
            ->method('hasQuery')
            ->will($this->returnValue(false));

        $this->_cacheStateMock->expects($this->once())
            ->method('isEnabled')->with('full_page')->will($this->returnValue(false));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedSuccess()
    {
        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasCookie')
            ->with(Magento_FullPageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE)
            ->will($this->returnValue(false));

        $this->_environmentMock->expects($this->exactly(2))
            ->method('hasQuery')
            ->will($this->returnValue(false));

        $this->_cacheStateMock->expects($this->once())
            ->method('isEnabled')->with('full_page')->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed($this->_requestId));
    }
}
