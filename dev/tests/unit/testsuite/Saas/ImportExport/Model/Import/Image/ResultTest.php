<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Model_Import_Image_ResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Result
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Saas_ImportExport_Helper_Data', array(), array(), '', false);
        $this->_helperMock->expects($this->any())->method('__')->with($this->isType('string'))
            ->will($this->returnArgument(0));

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManager->getObject('Saas_ImportExport_Model_Import_Image_Result', array(
            'helper' => $this->_helperMock,
        ));
    }

    public function testGetErrorsAsString()
    {
        $this->_model->addInvalid('invalidFile1', 'invalidMessage1');
        $this->_model->addInvalid('invalidFile3', array('invalidMessage31', 'invalidMessage32'));

        $indent = Saas_ImportExport_Model_Import_Image_Result::MESSAGE_INDENT;
        $expected = 'Product images errors (next image files will be ignored):<br />'
            . $indent . 'invalidMessage1<br />' . $indent . $indent . 'invalidFile1'
            . '<br />' . $indent . 'invalidMessage31;<br />' . $indent . 'invalidMessage32<br />'
            . $indent . $indent . 'invalidFile3';

        $this->assertEquals($expected, $this->_model->getErrorsAsString());
    }

    public function testGetUploadSummaryWhenOnlyValidFilesExist()
    {
        $this->_model->addValid('validFile1');
        $this->_model->addValid('validFile2');

        $indent = Saas_ImportExport_Model_Import_Image_Result::MESSAGE_INDENT;
        $expected = array(
            'is_success' => true,
            'message' => 'Image Archive File is valid. All image files successfully uploaded to media storage.<br />'
                . $indent . 'Checked images: 2<br />' . $indent . 'Valid images: 2'
        );

        $this->assertEquals($expected, $this->_model->getUploadSummary());
    }

    public function testGetUploadSummaryWhenValidAndInvalidFilesExist()
    {
        $this->_model->addInvalid('invalidFile1', 'invalidMessage1');
        $this->_model->addInvalid('invalidFile3', array('invalidMessage31', 'invalidMessage32'));
        $this->_model->addValid('validFile1');
        $this->_model->addValid('validFile2');

        $indent = Saas_ImportExport_Model_Import_Image_Result::MESSAGE_INDENT;
        $expected = array(
            'is_success' => true,
            'message' => 'Remainder image files, were successfully uploaded to media storage.<br />' . $indent
                . 'Checked images: 4<br />' . $indent . 'Valid images: 2<br />' . $indent . 'Invalid images: 2'
        );

        $this->assertEquals($expected, $this->_model->getUploadSummary());
    }

    public function testGetUploadSummaryWhenOnlyInvalidFilesExist()
    {
        $this->_model->addInvalid('invalidFile1', 'invalidMessage1');
        $this->_model->addInvalid('invalidFile3', array('invalidMessage31', 'invalidMessage32'));

        $indent = Saas_ImportExport_Model_Import_Image_Result::MESSAGE_INDENT;
        $expected = array(
            'is_success' => false,
            'message' => 'There are no valid images in archive<br />' . $indent . 'Checked images: 2'
        );

        $this->assertEquals($expected, $this->_model->getUploadSummary());
    }
}
