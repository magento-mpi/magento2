<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\File\Storage\Directory;

/**
 * Class DatabaseTest
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\File\Storage\Directory\Database |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryDatabase;

    /**
     * @var \Magento\Model\Context |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Registry |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Core\Helper\File\Storage\Database |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helperStorageDatabase;

    /**
     * @var \Magento\Stdlib\DateTime\DateTime |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateModelMock;

    /**
     * @var \Magento\Core\Model\File\Storage\Directory\Database |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryMock;

    /**
     * @var \Magento\Core\Model\File\Storage\Directory\DatabaseFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryFactoryMock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Core\Model\Resource\File\Storage\Directory\Database |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceDirectoryDatabaseMock;

    /**
     * @var \Magento\Logger
     */
    protected $loggerMock;

    /**
     * @var string
     */
    protected $customConnectionName = 'custom-connection-name';

    /**
     * Setup preconditions
     */
    protected function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Model\Context', array(), array(), '', false);
        $this->registryMock = $this->getMock('Magento\Registry', array(), array(), '', false);
        $this->helperStorageDatabase = $this->getMock(
            'Magento\Core\Helper\File\Storage\Database',
            array(),
            array(),
            '',
            false
        );
        $this->dateModelMock = $this->getMock('Magento\Stdlib\DateTime\DateTime', array(), array(), '', false);
        $this->directoryMock = $this->getMock(
            'Magento\Core\Model\File\Storage\Directory\Database',
            array('setPath', 'setName', '__wakeup', 'save', 'getParentId'),
            array(),
            '',
            false
        );
        $this->directoryFactoryMock = $this->getMock(
            'Magento\Core\Model\File\Storage\Directory\DatabaseFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->resourceDirectoryDatabaseMock = $this->getMock(
            'Magento\Core\Model\Resource\File\Storage\Directory\Database',
            array(),
            array(),
            '',
            false
        );
        $this->loggerMock = $this->getMock('Magento\Logger', array(), array(), '', false);

        $this->directoryFactoryMock->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->directoryMock)
        );

        $this->configMock = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->configMock->expects(
            $this->any()
        )->method(
            'getValue'
        )->with(
            \Magento\Core\Model\File\Storage::XML_PATH_STORAGE_MEDIA_DATABASE,
            'default'
        )->will(
            $this->returnValue($this->customConnectionName)
        );

        $this->contextMock->expects($this->once())->method('getLogger')->will($this->returnValue($this->loggerMock));

        $this->directoryDatabase = new \Magento\Core\Model\File\Storage\Directory\Database(
            $this->contextMock,
            $this->registryMock,
            $this->helperStorageDatabase,
            $this->dateModelMock,
            $this->configMock,
            $this->directoryFactoryMock,
            $this->resourceDirectoryDatabaseMock,
            null,
            $this->customConnectionName,
            array()
        );
    }

    /**
     * test import directories
     */
    public function testImportDirectories()
    {
        $this->directoryMock->expects($this->any())->method('getParentId')->will($this->returnValue(1));
        $this->directoryMock->expects($this->any())->method('save');

        $this->directoryMock->expects(
            $this->exactly(2)
        )->method(
            'setPath'
        )->with(
            $this->logicalOr($this->equalTo('/path/number/one'), $this->equalTo('/path/number/two'))
        );

        $this->directoryDatabase->importDirectories(
            array(
                array('name' => 'first', 'path' => './path/number/one'),
                array('name' => 'second', 'path' => './path/number/two')
            )
        );
    }

    /**
     * test import directories without parent
     */
    public function testImportDirectoriesFailureWithoutParent()
    {
        $this->directoryMock->expects($this->any())->method('getParentId')->will($this->returnValue(null));

        $this->loggerMock->expects($this->any())->method('logException');

        $this->directoryDatabase->importDirectories(array());
    }

    /**
     * test import directories not an array
     */
    public function testImportDirectoriesFailureNotArray()
    {
        $this->directoryMock->expects($this->never())->method('getParentId')->will($this->returnValue(null));

        $this->directoryDatabase->importDirectories('not an array');
    }
}
