<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\App;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\App\Media
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirVerificationMock;

    /**
     * @var callable
     */
    protected $_closure;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sync;

    /**
     * @var string
     */
    protected $_mediaDirectory = 'mediaDirectory';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var \Magento\Framework\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryReadMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\Core\Model\File\Storage\Request', array(), array(), '', false);
        $this->_closure = function () {
            return true;
        };
        $this->_objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');
        $this->_configMock = $this->getMock('Magento\Core\Model\File\Storage\Config', array(), array(), '', false);
        $this->_sync = $this->getMock('Magento\Core\Model\File\Storage\Synchronization', array(), array(), '', false);
        $this->_dirVerificationMock = $this->getMock(
            'Magento\Framework\App\Filesystem\DirectoryList\Verification',
            array(),
            array(),
            '',
            false
        );

        $this->filesystemMock = $this->getMock('Magento\Framework\App\Filesystem', array(), array(), '', false);
        $this->directoryReadMock = $this->getMock(
            'Magento\Framework\Filesystem\Directory\Read',
            array(),
            array(),
            '',
            false
        );

        $this->filesystemMock->expects(
            $this->any()
        )->method(
            'getDirectoryRead'
        )->with(
            \Magento\Framework\App\Filesystem::MEDIA_DIR
        )->will(
            $this->returnValue($this->directoryReadMock)
        );

        $this->_responseMock = $this->getMock('Magento\Core\Model\File\Storage\Response', array(), array(), '', false);

        $map = array(
            array('Magento\Framework\App\Filesystem\DirectoryList\Verification', $this->_dirVerificationMock),
            array('Magento\Core\Model\File\Storage\Request', $this->_requestMock),
            array('Magento\Core\Model\File\Storage\Synchronization', $this->_sync)
        );
        $this->_model = new \Magento\Core\App\Media(
            $this->_objectManagerMock,
            $this->_requestMock,
            $this->_responseMock,
            $this->_closure,
            'baseDir',
            'mediaDirectory',
            'var',
            'params',
            $this->filesystemMock
        );
        $this->_objectManagerMock->expects($this->any())->method('get')->will($this->returnValueMap($map));
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testProcessRequestCreatesConfigFileMediaDirectoryIsNotProvided()
    {
        $this->_model = new \Magento\Core\App\Media(
            $this->_objectManagerMock,
            $this->_requestMock,
            $this->_responseMock,
            $this->_closure,
            'baseDir',
            false,
            'var',
            'params',
            $this->filesystemMock
        );
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Core\Model\File\Storage\Config'
        )->will(
            $this->returnValue($this->_configMock)
        );
        $this->_configMock->expects($this->once())->method('save');
        $this->assertEquals($this->_responseMock, $this->_model->launch());
    }

    public function testProcessRequestReturnsNotFoundResponseIfResourceIsNotAllowed()
    {
        $this->_closure = function () {
            return false;
        };
        $this->_model = new \Magento\Core\App\Media(
            $this->_objectManagerMock,
            $this->_requestMock,
            $this->_responseMock,
            $this->_closure,
            'baseDir',
            false,
            'var',
            'params',
            $this->filesystemMock
        );
        $this->_responseMock->expects($this->once())->method('setHttpResponseCode')->with(404);
        $this->_requestMock->expects($this->once())->method('getPathInfo');
        $this->_objectManagerMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Core\Model\File\Storage\Config'
        )->will(
            $this->returnValue($this->_configMock)
        );
        $this->_configMock->expects($this->once())->method('getAllowedResources')->will($this->returnValue(false));
        $this->assertEquals($this->_responseMock, $this->_model->launch());
    }

    public function testProcessRequestReturnsNotFoundIfFileIsNotAllowed()
    {
        $this->_configMock->expects($this->never())->method('save');
        $this->_requestMock->expects($this->once())->method('getPathInfo');
        $this->_responseMock->expects($this->once())->method('setHttpResponseCode')->with(404);
        $this->_requestMock->expects($this->never())->method('getFilePath');
        $this->assertEquals($this->_responseMock, $this->_model->launch());
    }

    public function testProcessRequestReturnsFileIfItsProperlySynchronized()
    {
        $relativeFilePath = '_files';
        $filePath = str_replace('\\', '/', __DIR__ . '/' . $relativeFilePath);
        $this->_requestMock->expects(
            $this->any()
        )->method(
            'getPathInfo'
        )->will(
            $this->returnValue($this->_mediaDirectory . '/')
        );
        $this->_sync->expects($this->once())->method('synchronize');
        $this->_requestMock->expects($this->any())->method('getFilePath')->will($this->returnValue($filePath));

        $this->directoryReadMock->expects(
            $this->once()
        )->method(
            'getRelativePath'
        )->with(
            $filePath
        )->will(
            $this->returnValue($relativeFilePath)
        );

        $this->directoryReadMock->expects(
            $this->once()
        )->method(
            'isReadable'
        )->with(
            $relativeFilePath
        )->will(
            $this->returnValue(true)
        );
        $this->_responseMock->expects($this->once())->method('setFilePath')->with($filePath);
        $this->assertEquals($this->_responseMock, $this->_model->launch());
    }

    public function testProcessRequestReturnsNotFoundIfFileIsNotSynchronized()
    {
        $this->_requestMock->expects(
            $this->any()
        )->method(
            'getPathInfo'
        )->will(
            $this->returnValue($this->_mediaDirectory . '/')
        );
        $this->_sync->expects($this->once())->method('synchronize');
        $this->_requestMock->expects(
            $this->any()
        )->method(
            'getFilePath'
        )->will(
            $this->returnValue('non_existing_file_name')
        );
        $this->_responseMock->expects($this->once())->method('setHttpResponseCode')->with(404);
        $this->assertEquals($this->_responseMock, $this->_model->launch());
    }
}
