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
    protected $_appState;

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
     * @var \Magento\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Filesystem\Directory\Read|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryReadMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\Core\Model\File\Storage\Request', array(), array(), '', false);
        $this->_closure = function () {
            return true;
        };
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_appState = $this->getMock('Magento\App\State', array(), array(  ), '', false);
        $this->_configMock = $this->getMock('Magento\Core\Model\File\Storage\Config', array(), array(), '', false);
        $this->_sync= $this->getMock('Magento\Core\Model\File\Storage\Synchronization', array(), array(), '', false);
        $this->_dirVerificationMock = $this->getMock(
            'Magento\Filesystem\DirectoryList\Verification', array(), array(), '', false
        );

        $this->filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->directoryReadMock = $this->getMock('Magento\Filesystem\Directory\Read', array(), array(), '', false);

        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryRead')
            ->with(\Magento\Filesystem::MEDIA)
            ->will($this->returnValue($this->directoryReadMock));

        $this->_responseMock = $this->getMock('Magento\Core\Model\File\Storage\Response', array(), array(), '', false);

        $map = array(
            array('Magento\Filesystem\DirectoryList\Verification', $this->_dirVerificationMock),
            array('Magento\App\State', $this->_appState),
            array('Magento\Core\Model\File\Storage\Request', $this->_requestMock),
            array('Magento\Core\Model\File\Storage\Synchronization', $this->_sync),
        );
        $this->_model = new \Magento\Core\App\Media(
            $this->_appState,
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

    public function testProcessRequestDoesNothingIfApplicationIsNotInstalled()
    {
        $this->_appState->expects($this->once())->method('isInstalled')->will($this->returnValue(false));
        $this->_responseMock->expects($this->once())->method('sendNotFound');
        $this->_requestMock->expects($this->never())->method('getPathInfo');
        $this->assertEquals(-1, $this->_model->execute());
    }

    public function testProcessRequestCreatesConfigFileMediaDirectoryIsNotProvided()
    {
        $this->_model = new \Magento\Core\App\Media(
            $this->_appState,
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
        $this->_appState->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_objectManagerMock->expects($this->once())->method('create')
            ->with('Magento\Core\Model\File\Storage\Config')
            ->will($this->returnValue($this->_configMock));
        $this->_configMock->expects($this->once())->method('save');
        $this->assertEquals(-1, $this->_model->execute());

    }

    public function testProcessRequestReturnsNotFoundResponseIfResourceIsNotAllowed()
    {
        $this->_closure = function () {
            return false;
        };
        $this->_model = new \Magento\Core\App\Media(
            $this->_appState,
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
        $this->_appState->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_responseMock->expects($this->once())->method('sendNotFound');
        $this->_requestMock->expects($this->once())->method('getPathInfo');
        $this->_objectManagerMock->expects($this->once())->method('create')
            ->with('Magento\Core\Model\File\Storage\Config')
            ->will($this->returnValue($this->_configMock));
        $this->_configMock->expects($this->once())->method('getAllowedResources')->will($this->returnValue(false));
        $this->assertEquals(-1, $this->_model->execute());

    }

    public function testProcessRequestReturnsNotFoundIfFileIsNotAllowed()
    {
        $this->_appState->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_configMock->expects($this->never())->method('save');
        $this->_requestMock->expects($this->once())->method('getPathInfo');
        $this->_responseMock->expects($this->once())->method('sendNotFound');
        $this->_requestMock->expects($this->never())->method('getFilePath');
        $this->assertEquals(-1, $this->_model->execute());
    }

    public function testProcessRequestReturnsFileIfItsProperlySynchronized()
    {
        $relativeFilePath = '_files';
        $filePath = str_replace('\\', '/', __DIR__ . '/' . $relativeFilePath);
        $this->_appState->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_requestMock->expects($this->any())->method('getPathInfo')
              ->will($this->returnValue($this->_mediaDirectory . '/'));
        $this->_sync->expects($this->once())->method('synchronize');
        $this->_requestMock->expects($this->any())
             ->method('getFilePath')->will($this->returnValue($filePath));

        $this->directoryReadMock->expects($this->once())
            ->method('getRelativePath')
            ->with($filePath)
            ->will($this->returnValue($relativeFilePath));

        $this->directoryReadMock->expects($this->once())
            ->method('isReadable')
            ->with($relativeFilePath)
            ->will($this->returnValue(true));
        $this->_responseMock
            ->expects($this->once())
            ->method('sendFile')
            ->with($filePath);
        $this->_responseMock
            ->expects($this->never())
            ->method('sendNotFound');
        $this->assertEquals(0, $this->_model->execute());
    }

    public function testProcessRequestReturnsNotFoundIfFileIsNotSynchronized()
    {
        $this->_appState->expects($this->once())->method('isInstalled')->will($this->returnValue(true));
        $this->_requestMock->expects($this->any())
             ->method('getPathInfo')->will($this->returnValue($this->_mediaDirectory . '/'));
        $this->_sync->expects($this->once())->method('synchronize');
        $this->_requestMock->expects($this->any())
            ->method('getFilePath')->will($this->returnValue('non_existing_file_name'));
        $this->_responseMock->expects($this->never())->method('sendFile');
        $this->_responseMock->expects($this->once())->method('sendNotFound');
        $this->assertEquals(-1, $this->_model->execute());
    }
}
