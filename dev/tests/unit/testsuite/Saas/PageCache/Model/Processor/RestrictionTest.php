<?php
/**
 * Test class for Saas_PageCache_Model_Processor_Restriction
 *
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PageCache_Model_Processor_RestrictionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_PageCache_Model_Processor_Restriction
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

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
            'Enterprise_PageCache_Model_Environment', array(), array(), '', false
        );
        $this->_cacheMock = $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false);
        $this->_model = new Saas_PageCache_Model_Processor_Restriction(
            $this->_cacheMock, $this->_environmentMock
        );
    }

    public function testIsAllowedWithoutRequestId()
    {
        $this->_environmentMock->expects($this->never())->method('getServer');
        $this->_environmentMock->expects($this->never())->method('hasQuery');
        $this->_cacheMock->expects($this->never())->method('canUse');

        $this->assertFalse($this->_model->isAllowed(''));
    }

    public function testIsAllowedInIsDeniedMode()
    {
        $this->_environmentMock->expects($this->never())->method('getServer');
        $this->_environmentMock->expects($this->never())->method('hasCookie');
        $this->_cacheMock->expects($this->never())->method('canUse');

        $this->_model->setIsDenied();
        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithHTTPS()
    {
        $this->_environmentMock->expects($this->never())->method('hasQuery');
        $this->_cacheMock->expects($this->never())->method('canUse');

        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('on'));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithSIDInGetParam()
    {
        $this->_cacheMock->expects($this->never())->method('canUse');

        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasQuery')
            ->with(Magento_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM)
            ->will($this->returnValue(true));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedWithDisabledCache()
    {
        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasQuery')
            ->will($this->returnValue(false));

        $this->_cacheMock->expects($this->once())->method('canUse')->with('full_page')->will($this->returnValue(false));

        $this->assertFalse($this->_model->isAllowed($this->_requestId));
    }

    public function testIsAllowedSuccess()
    {
        $this->_environmentMock->expects($this->once())
            ->method('getServer')->with('HTTPS')->will($this->returnValue('off'));

        $this->_environmentMock->expects($this->once())
            ->method('hasQuery')
            ->will($this->returnValue(false));

        $this->_cacheMock->expects($this->once())->method('canUse')->with('full_page')->will($this->returnValue(true));

        $this->assertTrue($this->_model->isAllowed($this->_requestId));
    }
}
