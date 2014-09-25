<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Scheduled;

use PHPUnit_Framework_MockObject_MockObject as Mock;

/**
 * Class OperationTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class OperationTest extends \PHPUnit_Framework_TestCase
{
    const DATE = '2014/01/01';

    /**
     * Default date value
     *
     * @var string
     */
    protected $_date = '00-00-00';

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation
     */
    protected $model;

    /**
     * @var \Magento\Framework\Model\Context | Mock
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry | Mock
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\App\Filesystem | Mock
     */
    protected $filesystemMock;

    /**
     * @var \Magento\Store\Model\StoreManager | Mock
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory | Mock
     */
    protected $genericFactoryMock;

    /**
     * @var \Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory | Mock
     */
    protected $dataFactoryMock;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory | Mock
     */
    protected $valueFactoryMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime | Mock
     */
    protected $datetimeMock;

    /**
     * @var \Magento\Framework\App\Config | Mock
     */
    protected $configScopeMock;

    /**
     * @var \Magento\Framework\Stdlib\String | Mock
     */
    protected $stringStdLibMock;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder | Mock
     */
    protected $transportBuilderMock;

    /**
     * @var \Magento\Framework\Io\Ftp | Mock
     */
    protected $ftpMock;

    /**
     * @var \Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation | Mock
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\Db | Mock
     */
    protected $resourceCollectionMock;

    protected function setUp()
    {
        $this->contextMock = $this->getMockBuilder('Magento\Framework\Model\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $this->filesystemMock = $this->getMockBuilder('Magento\Framework\App\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder('Magento\Store\Model\StoreManager')
            ->disableOriginalConstructor()
            ->getMock();
        $genericClass = 'Magento\ScheduledImportExport\Model\Scheduled\Operation\GenericFactory';
        $this->genericFactoryMock = $this->getMockBuilder($genericClass)
            ->disableOriginalConstructor()
            ->getMock();
        $dataClass = 'Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory';
        $this->dataFactoryMock = $this->getMockBuilder($dataClass)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->valueFactoryMock = $this->getMockBuilder('Magento\Framework\App\Config\ValueFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->datetimeMock = $this->getMockBuilder('Magento\Framework\Stdlib\DateTime\DateTime')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configScopeMock = $this->getMockBuilder('Magento\Framework\App\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $this->stringStdLibMock = $this->getMockBuilder('Magento\Framework\Stdlib\String')
            ->disableOriginalConstructor()
            ->getMock();
        $this->transportBuilderMock = $this->getMockBuilder('Magento\Framework\Mail\Template\TransportBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->ftpMock = $this->getMockBuilder('Magento\Framework\Io\Ftp')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Resource\Scheduled\Operation')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resourceCollectionMock = $this->getMockBuilder('Magento\Framework\Data\Collection\Db')
            ->disableOriginalConstructor()
            ->getMock();
        $data = [];

        $this->model = new \Magento\ScheduledImportExport\Model\Scheduled\Operation(
            $this->contextMock,
            $this->registryMock,
            $this->filesystemMock,
            $this->storeManagerMock,
            $this->genericFactoryMock,
            $this->dataFactoryMock,
            $this->valueFactoryMock,
            $this->datetimeMock,
            $this->configScopeMock,
            $this->stringStdLibMock,
            $this->transportBuilderMock,
            $this->ftpMock,
            $this->resourceMock,
            $this->resourceCollectionMock,
            $data
        );
    }

    /**
     * @dataProvider getHistoryFilePathDataProvider
     */
    public function testGetHistoryFilePath($fileInfo, $lastRunDate, $expectedPath)
    {
        $model = $this->_getScheduledOperationModel($fileInfo);

        $model->setLastRunDate($lastRunDate);

        $this->assertEquals($expectedPath, $model->getHistoryFilePath());
    }

    /**
     * @return array
     */
    public function getHistoryFilePathDataProvider()
    {
        $dir = Operation::LOG_DIRECTORY . self::DATE . '/' . Operation::FILE_HISTORY_DIRECTORY;
        return array(
            'empty file name' => array(
                '$fileInfo' => array('file_format' => 'csv'),
                '$lastRunDate' => null,
                '$expectedPath' => $dir . $this->_date . '_export_catalog_product.csv'
            ),
            'filled file name' => array(
                '$fileInfo' => array('file_name' => 'test.xls'),
                '$lastRunDate' => null,
                '$expectedPath' => $dir . $this->_date . '_export_catalog_product.xls'
            ),
            'set last run date' => array(
                '$fileInfo' => array('file_name' => 'test.xls'),
                '$lastRunDate' => '11-11-11',
                '$expectedPath' => $dir . '11-11-11_export_catalog_product.xls'
            )
        );
    }

    /**
     * Get mocked model
     *
     * @param array $fileInfo
     * @return \Magento\ScheduledImportExport\Model\Scheduled\Operation| \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getScheduledOperationModel(array $fileInfo)
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $dateModelMock = $this->getMock(
            'Magento\Framework\Stdlib\DateTime\DateTime',
            array('date'),
            array(),
            '',
            false
        );
        $dateModelMock->expects(
            $this->any()
        )->method(
            'date'
        )->will(
            $this->returnCallback(array($this, 'getDateCallback'))
        );

        //TODO Get rid of mocking methods from testing model when this model will be re-factored

        $operationFactory = $this->getMOck(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation\DataFactory',
            array(),
            array(),
            '',
            false
        );

        $directory = $this->getMockBuilder(
            'Magento\Framework\Filesystem\Directory\Write'
        )->disableOriginalConstructor()->getMock();
        $directory->expects($this->once())->method('getAbsolutePath')->will($this->returnArgument(0));
        $filesystem =
            $this->getMockBuilder('Magento\Framework\App\Filesystem')->disableOriginalConstructor()->getMock();
        $filesystem->expects($this->once())->method('getDirectoryWrite')->will($this->returnValue($directory));

        $params = array('operationFactory' => $operationFactory, 'filesystem' => $filesystem);
        $arguments = $objectManagerHelper->getConstructArguments(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            $params
        );
        $arguments['dateModel'] = $dateModelMock;
        $model = $this->getMock(
            'Magento\ScheduledImportExport\Model\Scheduled\Operation',
            array('getOperationType', 'getEntityType', 'getFileInfo', '_init'),
            $arguments
        );

        $model->expects($this->once())->method('getOperationType')->will($this->returnValue('export'));
        $model->expects($this->once())->method('getEntityType')->will($this->returnValue('catalog_product'));
        $model->expects($this->once())->method('getFileInfo')->will($this->returnValue($fileInfo));

        return $model;
    }

    /**
     * Callback to use instead of \Magento\Framework\Stdlib\DateTime\DateTime::date()
     *
     * @param string $format
     * @param int|string $input
     * @return string
     */
    public function getDateCallback($format, $input = null)
    {
        if (!empty($format) && !is_null($input)) {
            return $input;
        }
        if ($format === 'Y/m/d') {
            return self::DATE;
        }
        return $this->_date;
    }

    /**
     * Test saveFileSource() with all valid parameters
     */
    public function testSaveFileSourceFtp()
    {
        $fileContent = 'data to export';
        $fileInfo = [
            'file_name' => 'somefile.csv',
            'file_format' => 'csv',
            'file_path' => '/test',
            'server_type' => \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FTP_STORAGE
        ];
        $datetime = '1970-01-01';
        $operationType = 'export';
        $entityType = 'product';
        $resultFile = '1970-01-01_export_product.csv';
        $scheduledFileName = 'scheduled_filename';
        $serverOptions = $this->getSourceOptions();
        $openArguments = ['path' => $fileInfo['file_path']];
        $writeFilePath = $fileInfo['file_path'] . '/' . $scheduledFileName . '.' .$fileInfo['file_format'];
        $writeResult = true;

        $this->datetimeMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($datetime));

        $dataMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Scheduled\Operation\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($dataMock));
        $dataMock->expects($this->any())
            ->method('getServerTypesOptionArray')
            ->will($this->returnValue($serverOptions));

        $exportMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Export')
            ->disableOriginalConstructor()
            ->getMock();
        $exportMock->expects($this->at(0))
            ->method('addLogComment');
        $exportMock->expects($this->any())
            ->method('getScheduledFileName')
            ->will($this->returnValue($scheduledFileName));

        $writeDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\Write')
            ->disableOriginalConstructor()
            ->getMock();
        $writeDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnValue($resultFile));
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($writeDirectoryMock));

        $this->ftpMock->expects($this->once())
            ->method('open')
            ->with($this->equalTo($openArguments));
        $this->ftpMock->expects($this->once())
            ->method('write')
            ->with($this->equalTo($writeFilePath), $this->equalTo($fileContent))
            ->will($this->returnValue($writeResult));

        $this->setModelData($fileInfo, $operationType, $entityType);

        $result = $this->model->saveFileSource($exportMock, $fileContent);
        $this->assertTrue($result);
    }

    /**
     * Test saveFileSource() through Filesystem library
     */
    public function testSaveFileSourceFile()
    {
        $fileContent = 'data to export';
        $fileInfo = [
            'file_name' => 'somefile.csv',
            'file_format' => 'csv',
            'file_path' => '/test',
            'server_type' => \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FILE_STORAGE
        ];
        $datetime = '1970-01-01';
        $operationType = 'export';
        $entityType = 'product';
        $resultFile = '1970-01-01_export_product.csv';
        $scheduledFileName = 'scheduled_filename';
        $serverOptions = $this->getSourceOptions();

        $this->datetimeMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($datetime));

        $dataMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Scheduled\Operation\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($dataMock));
        $dataMock->expects($this->any())
            ->method('getServerTypesOptionArray')
            ->will($this->returnValue($serverOptions));

        $exportMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Export')
            ->disableOriginalConstructor()
            ->getMock();
        $exportMock->expects($this->at(0))
            ->method('addLogComment');
        $exportMock->expects($this->any())
            ->method('getScheduledFileName')
            ->will($this->returnValue($scheduledFileName));

        $writeDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\Write')
            ->disableOriginalConstructor()
            ->getMock();
        $writeDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnValue($resultFile));
        $writeDirectoryMock->expects($this->any())
            ->method('writeFile')
            ->will($this->returnValue(true));
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($writeDirectoryMock));

        $this->setModelData($fileInfo, $operationType, $entityType);

        $result = $this->model->saveFileSource($exportMock, $fileContent);
        $this->assertTrue($result);
    }

    /**
     * Test saveFileSource() that throws Exception during opening ftp connection
     *
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage We couldn't write file "scheduled_filename.csv" to "/test" with the "ftp" driver.
     */
    public function testSaveFileSourceException()
    {
        $fileContent = 'data to export';
        $fileInfo = [
            'file_name' => 'somefile.csv',
            'file_format' => 'csv',
            'file_path' => '/test',
            'server_type' => \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FTP_STORAGE
        ];
        $datetime = '1970-01-01';
        $operationType = 'export';
        $entityType = 'product';
        $resultFile = '1970-01-01_export_product.csv';
        $scheduledFileName = 'scheduled_filename';
        $serverOptions = $this->getSourceOptions();

        $this->datetimeMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($datetime));

        $dataMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Scheduled\Operation\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($dataMock));
        $dataMock->expects($this->any())
            ->method('getServerTypesOptionArray')
            ->will($this->returnValue($serverOptions));

        $exportMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Export')
            ->disableOriginalConstructor()
            ->getMock();
        $exportMock->expects($this->at(0))
            ->method('addLogComment');
        $exportMock->expects($this->any())
            ->method('getScheduledFileName')
            ->will($this->returnValue($scheduledFileName));

        $writeDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\Write')
            ->disableOriginalConstructor()
            ->getMock();
        $writeDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnValue($resultFile));
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($writeDirectoryMock));

        $this->ftpMock->expects($this->once())
            ->method('open')
            ->will($this->throwException(new \Exception('Can not open file')));

        $this->setModelData($fileInfo, $operationType, $entityType);

        $result = $this->model->saveFileSource($exportMock, $fileContent);
        $this->assertNull($result);
    }

    /**
     * Test getFileSource() if 'file_name' not exists
     *
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedMessage We couldn't read the file source because the file name is empty.
     */
    public function testGetFileSource()
    {
        $fileInfo = [];
        $importMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Import')
            ->disableOriginalConstructor()
            ->getMock();

        $this->model->setFileInfo($fileInfo);
        $result = $this->model->getFileSource($importMock);
        $this->assertNull($result);
    }

    /**
     * Test getFileSource() import data by using ftp
     */
    public function testGetFileSourceFtp()
    {
        $datetime = '1970-01-01';
        $operationType = 'import';
        $entityType = 'product';
        $fileInfo = [
            'file_name' => 'source.csv',
            'file_path' => '/test',
            'server_type' => \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FTP_STORAGE
        ];
        $serverOptions = $this->getSourceOptions();

        $this->datetimeMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($datetime));

        $dataMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Scheduled\Operation\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($dataMock));
        $dataMock->expects($this->any())
            ->method('getServerTypesOptionArray')
            ->will($this->returnValue($serverOptions));

        $writeDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\Write')
            ->disableOriginalConstructor()
            ->getMock();
        $writeDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnArgument(0));
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($writeDirectoryMock));

        $importMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Import')
            ->disableOriginalConstructor()
            ->getMock();
        $importMock->expects($this->any())
            ->method('addLogComment');

        $this->ftpMock->expects($this->any())
            ->method('open');
        $this->ftpMock->expects($this->any())
            ->method('read')
            ->will($this->returnValue(true));

        $this->setModelData($fileInfo, $operationType, $entityType);

        $result = $this->model->getFileSource($importMock);
        $this->assertEquals('csv', pathinfo($result, PATHINFO_EXTENSION));
    }

    /**
     * Test getFileSource() import data by using Filesystem
     */
    public function testGetFileSourceFile()
    {
        $datetime = '1970-01-01';
        $operationType = 'import';
        $entityType = 'product';
        $fileInfo = [
            'file_name' => 'source.csv',
            'file_path' => '/test',
            'server_type' => \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FILE_STORAGE
        ];
        $source = trim($fileInfo['file_path'] . '/' . $fileInfo['file_name'], '\\/');
        $contents = 'test content';

        $serverOptions = $this->getSourceOptions();

        $this->datetimeMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($datetime));

        $dataMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Scheduled\Operation\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($dataMock));
        $dataMock->expects($this->any())
            ->method('getServerTypesOptionArray')
            ->will($this->returnValue($serverOptions));

        $writeDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\Write')
            ->disableOriginalConstructor()
            ->getMock();
        $readDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\Read')
            ->disableOriginalConstructor()
            ->getMock();
        $readDirectoryMock->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnArgument(0));
        $readDirectoryMock->expects($this->once())
            ->method('isExist')
            ->with($this->equalTo($source))
            ->will($this->returnValue(true));
        $readDirectoryMock->expects($this->once())
            ->method('readFile')
            ->with($this->equalTo($source))
            ->will($this->returnValue($contents));
        $writeDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnArgument(0));
        $writeDirectoryMock->expects($this->any())
            ->method('writeFile')
            ->will($this->returnValue(true));
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($writeDirectoryMock));
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryRead')
            ->will($this->returnValue($readDirectoryMock));

        $importMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Import')
            ->disableOriginalConstructor()
            ->getMock();
        $importMock->expects($this->any())
            ->method('addLogComment');

        $this->setModelData($fileInfo, $operationType, $entityType);

        $result = $this->model->getFileSource($importMock);
        $this->assertEquals('csv', pathinfo($result, PATHINFO_EXTENSION));
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @exceptionMessage We couldn't read the import file.
     */
    public function testGetFileSourceFtpException()
    {
        $datetime = '1970-01-01';
        $operationType = 'import';
        $entityType = 'product';
        $fileInfo = [
            'file_name' => 'source.csv',
            'file_path' => '/test',
            'server_type' => \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FTP_STORAGE
        ];
        $serverOptions = $this->getSourceOptions();

        $this->datetimeMock->expects($this->any())
            ->method('date')
            ->will($this->returnValue($datetime));

        $dataMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Scheduled\Operation\Data')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($dataMock));
        $dataMock->expects($this->any())
            ->method('getServerTypesOptionArray')
            ->will($this->returnValue($serverOptions));

        $writeDirectoryMock = $this->getMockBuilder('Magento\Framework\Filesystem\Directory\Write')
            ->disableOriginalConstructor()
            ->getMock();
        $writeDirectoryMock->expects($this->any())
            ->method('getAbsolutePath')
            ->will($this->returnArgument(0));
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($writeDirectoryMock));

        $importMock = $this->getMockBuilder('Magento\ScheduledImportExport\Model\Import')
            ->disableOriginalConstructor()
            ->getMock();
        $importMock->expects($this->any())
            ->method('addLogComment');

        $this->ftpMock->expects($this->any())
            ->method('open')
            ->will($this->throwException(new \Magento\Framework\Filesystem\FilesystemException('Can not open file')));
        $this->ftpMock->expects($this->any())
            ->method('read')
            ->will($this->returnValue(true));

        $this->setModelData($fileInfo, $operationType, $entityType);

        $result = $this->model->getFileSource($importMock);
        $this->assertNull($result);
    }

    /**
     * @param array $fileInfo
     * @param string $operationType
     * @param string $entityType
     */
    protected function setModelData(array $fileInfo, $operationType, $entityType)
    {
        $this->model->setFileInfo($fileInfo);
        $this->model->setOperationType($operationType);
        $this->model->setEntityType($entityType);
    }

    /**
     * @return array
     */
    protected function getSourceOptions()
    {
        return [
            \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FTP_STORAGE => 'ftp',
            \Magento\ScheduledImportExport\Model\Scheduled\Operation\Data::FILE_STORAGE => 'file',
        ];
    }
}
