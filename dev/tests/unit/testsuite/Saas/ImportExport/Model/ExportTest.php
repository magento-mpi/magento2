<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_ExportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_ImportExport_Model_Export
     */
    protected $_exportModel;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_entityMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionMock;

    protected function setUp()
    {
        $this->_entityMock = $this->getMock('Saas_ImportExport_Model_Export_Entity_Product',
            array('setStorageAdapter', 'prepareCollection', 'getCollection', 'getHeaderColumns', 'exportCollection'),
            array(), '', false);
        $this->_storageMock = $this->getMock('Saas_ImportExport_Model_Export_Adapter_Csv',
            array('setStorageAdapter', 'cleanupWorkingDir', 'writeHeaderColumns', 'saveHeaderColumns',
                'renameTemporaryFile'), array(), '', false);
        $this->_stateHelperMock = $this->getMock('Saas_ImportExport_Helper_Export_State', array(), array(), '', false);

        $entityFactory = $this->getMock('Saas_ImportExport_Model_Export_EntityFactory', array('create'),
            array(), '', false);
        $entityFactory->expects($this->once())->method('create')->will($this->returnValue($this->_entityMock));

        $storageFactory = $this->getMock('Saas_ImportExport_Model_Export_StorageFactory', array('create'),
            array(), '', false);
        $storageFactory->expects($this->once())->method('create')->will($this->returnValue($this->_storageMock));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_exportModel = $objectManager->getObject('Saas_ImportExport_Model_Export', array(
            'storageFactory' => $storageFactory,
            'entityFactory' => $entityFactory,
            'stateHelper' => $this->_stateHelperMock,
        ));

        // paginate collection
        $this->_collectionMock = $this->getMock('Magento_Data_Collection_Db', array(), array(), '', false);
        $this->_entityMock->expects($this->any())->method('getCollection')
            ->will($this->returnValue($this->_collectionMock));
        $this->_collectionMock->expects($this->once())->method('setCurPage')->will($this->returnSelf());
        $this->_collectionMock->expects($this->once())->method('setPageSize')->will($this->returnSelf());

        // set header columns
        $this->_entityMock->expects($this->once())->method('getHeaderColumns')->will($this->returnValue(array()));
    }

    /**
     * @return array
     */
    public function dataProviderForSetHeaderColumns()
    {
        return array(
            array(1, 'writeHeaderColumns'),
            array(2, 'saveHeaderColumns'),
        );
    }

    protected function _finishExportSuccess()
    {
        $this->_stateHelperMock->expects($this->once())->method('saveTaskAsFinished');
        $this->_storageMock->expects($this->once())->method('renameTemporaryFile')
            ->will($this->returnValue('some-file-name'));
        $this->_stateHelperMock->expects($this->once())->method('saveExportFilename');
    }

    /**
     * @param int $page
     * @param string $setHeaderMethod
     * @dataProvider dataProviderForSetHeaderColumns
     */
    public function testExportIsNotProcessing($page, $setHeaderMethod)
    {
        $isCanExport = 0;
        $this->_storageMock->expects($this->once())->method($setHeaderMethod);
        $this->_collectionMock->expects($this->once())->method('getSize')->will($this->returnValue($isCanExport));
        $this->_entityMock->expects($this->never())->method('exportCollection');
        $this->_finishExportSuccess();

        $this->_exportModel->export(array(
            'file_format' => Saas_ImportExport_Model_Export_Adapter_Csv::EXTENSION_CSV,
            'page' => $page,
        ));
    }

    public function testExportIsProcessingAndFinish()
    {
        $isCanExport = 1;
        $currentPage = 4;
        $lastPage = 4;
        $this->_storageMock->expects($this->once())->method('saveHeaderColumns');
        $this->_collectionMock->expects($this->once())->method('getSize')->will($this->returnValue($isCanExport));
        $this->_entityMock->expects($this->once())->method('exportCollection');
        $this->_collectionMock->expects($this->once())->method('getLastPageNumber')
            ->will($this->returnValue($lastPage));
        $this->_finishExportSuccess();

        $this->_exportModel->export(array(
            'file_format' => Saas_ImportExport_Model_Export_Adapter_Csv::EXTENSION_CSV,
            'page' => $currentPage,
        ));
    }

    public function testExportIsProcessingAndContinue()
    {
        $isCanExport = 1;
        $currentPage = 4;
        $lastPage = 10;
        $this->_storageMock->expects($this->once())->method('saveHeaderColumns');
        $this->_collectionMock->expects($this->once())->method('getSize')->will($this->returnValue($isCanExport));
        $this->_entityMock->expects($this->once())->method('exportCollection');
        $this->_collectionMock->expects($this->exactly(2))->method('getLastPageNumber')
            ->will($this->returnValue($lastPage));
        $this->_stateHelperMock->expects($this->never())->method('saveTaskAsFinished');
        $this->_stateHelperMock->expects($this->once())->method('saveTaskStatusMessage');

        $this->_exportModel->export(array(
            'file_format' => Saas_ImportExport_Model_Export_Adapter_Csv::EXTENSION_CSV,
            'page' => $currentPage,
        ));
    }
}
