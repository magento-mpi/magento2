<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_ImportExport_Model_Service_Image_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_archiveUploaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_archiveExtractorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_imageValidatorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_importResultMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_fileSystemMock;

    /**
     * @var Saas_ImportExport_Model_Service_Image_Import
     */
    protected $_model;

    public function setUp()
    {
        $this->_archiveUploaderMock = $this->getMock('Saas_ImportExport_Model_Import_Image_Archive_Uploader', array(),
            array(), '', false);
        $this->_archiveExtractorMock = $this->getMock('Saas_ImportExport_Model_Import_Image_Archive_Extractor', array(),
            array(), '', false);
        $this->_imageValidatorMock = $this->getMock('Magento_Validator_ValidatorInterface');
        $validatorFactoryMock = $this->getMock('Saas_ImportExport_Model_Import_Image_Validator_Factory', array(),
            array(), '', false);
        $validatorFactoryMock->expects($this->once())->method('createValidator')
            ->will($this->returnValue($this->_imageValidatorMock));
        $this->_importResultMock = $this->getMock('Saas_ImportExport_Model_Import_Image_Result', array(),
            array(), '', false);
        $this->_fileSystemMock = $this->getMock('Saas_ImportExport_Model_Import_Image_FileSystem', array(),
            array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Saas_ImportExport_Model_Service_Image_Import', array(
            'archiveUploader' => $this->_archiveUploaderMock,
            'archiveExtractor' => $this->_archiveExtractorMock,
            'validatorFactory' => $validatorFactoryMock,
            'importResult' => $this->_importResultMock,
            'fileSystem' => $this->_fileSystemMock,
        ));
    }

    /**
     * @expectedException Saas_ImportExport_Model_Import_Image_Exception
     * @expectedExceptionMessage Some error message
     */
    public function testImportWithFailedUpload()
    {
        $this->_archiveUploaderMock->expects($this->once())->method('upload')
            ->will($this->throwException(new Exception('Some error message')));

        $this->_model->import();
    }

    /**
     * @expectedException Saas_ImportExport_Model_Import_Image_Exception
     * @expectedExceptionMessage Some error message
     */
    public function testImportWithFailedExtract()
    {
        $this->_archiveExtractorMock->expects($this->once())->method('extract')
            ->will($this->throwException(new Exception('Some error message')));

        $this->_model->import();
    }

    /**
     * @expectedException Saas_ImportExport_Model_Import_Image_Exception
     * @expectedExceptionMessage Some error message
     */
    public function testImportWithFailedGetFiles()
    {
        $this->_archiveExtractorMock->expects($this->once())->method('extract')->will($this->returnSelf());
        $this->_archiveExtractorMock->expects($this->once())->method('getFiles')
            ->will($this->throwException(new Exception('Some error message')));

        $this->_model->import();
    }

    public function testImport()
    {
        $archiveFilename = 'archive-filename';
        $extractedFiles = array(
            array('filename' => 'file_invalid'),
            array('filename' => 'file_valid'),
        );
        $messages = array('Some error message');

        $this->_archiveUploaderMock->expects($this->once())->method('upload')
            ->will($this->returnValue($archiveFilename));
        $this->_archiveExtractorMock->expects($this->once())->method('extract')->with($archiveFilename)
            ->will($this->returnSelf());
        $this->_archiveExtractorMock->expects($this->once())->method('getFiles')
            ->will($this->returnValue($extractedFiles));

        $this->_imageValidatorMock->expects($this->at(0))->method('isValid')->with($extractedFiles[0]['filename'])
            ->will($this->returnValue(false));
        $this->_imageValidatorMock->expects($this->once())->method('getMessages')->will($this->returnValue($messages));
        $this->_fileSystemMock->expects($this->once())->method('removeFile')->with($extractedFiles[0]['filename']);
        $this->_importResultMock->expects($this->once())->method('addInvalid')
            ->with($extractedFiles[0]['filename'], $messages);

        $this->_imageValidatorMock->expects($this->at(2))->method('isValid')->with($extractedFiles[1]['filename'])
            ->will($this->returnValue(true));
        $this->_fileSystemMock->expects($this->once())->method('moveFileToMedia')->with($extractedFiles[1]['filename']);
        $this->_importResultMock->expects($this->once())->method('addValid')->with($extractedFiles[1]['filename']);

        $this->assertEquals($this->_importResultMock, $this->_model->import());
    }
}
