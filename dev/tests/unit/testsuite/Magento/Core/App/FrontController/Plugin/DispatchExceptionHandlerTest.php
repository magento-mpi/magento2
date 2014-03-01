<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

class DispatchExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\FrontController\Plugin\DispatchExceptionHandler
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filesystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    protected function setUp()
    {
        $this->_storeManagerMock = $this->getMock('\Magento\Core\Model\StoreManager', array(), array(), '', false);
        $this->_filesystemMock = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->subjectMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->_model = new DispatchExceptionHandler(
            $this->_storeManagerMock,
            $this->_filesystemMock
        );
    }

    public function testAroundDispatch()
    {
        $requestMock = $this->getMock('Magento\App\RequestInterface');
        $this->assertEquals('Expected',
            $this->_model->aroundDispatch($this->subjectMock, $this->closureMock, $requestMock));
    }
}
