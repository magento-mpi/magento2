<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_ImportExport_Model_Scheduled_OperationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_ImportExport_Model_Scheduled_Operation
     */
    protected $_model;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $this->_model = Mage::getModel('Enterprise_ImportExport_Model_Scheduled_Operation');
    }


    /**
     * Get possible operation types
     *
     * @return array
     */
    public function getOperationTypesDataProvider()
    {
        return array(
            'import' => array('$operationType' => 'import'),
            'export' => array('$operationType' => 'export')
        );
    }

    /**
     * Test getInstance() method
     *
     * @dataProvider getOperationTypesDataProvider
     * @param $operationType
     */
    public function testGetInstance($operationType)
    {
        $this->_model->setOperationType($operationType);

        $this->assertInstanceOf(
            'Enterprise_ImportExport_Model_' . uc_words($operationType),
            $this->_model->getInstance()
        );
    }

    /**
     * Test getHistoryFilePath() method in case when file info is not set
     *
     * @expectedException Magento_Core_Exception
     */
    public function testGetHistoryFilePathException()
    {
        $this->_model->getHistoryFilePath();
    }

    /**
     * @magentoDataFixture Enterprise/ImportExport/_files/operation.php
     * @magentoDataFixture Magento/Catalog/_files/products_new.php
     */
    public function testRunAction()
    {
        $this->_model->load('export', 'operation_type');

        $fileInfo = $this->_model->getFileInfo();

        // Create export directory if not exist
        $varDir = Mage::getBaseDir('var');
        $exportDir = $varDir . DS . $fileInfo['file_path'];
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777);
        }

        // Change current working directory to allow save export results
        $cwd = getcwd();
        chdir($varDir);

        $this->_model->run();

        $scheduledExport = Mage::getModel('Enterprise_ImportExport_Model_Export');
        $scheduledExport->setEntity($this->_model->getEntityType());
        $scheduledExport->setOperationType($this->_model->getOperationType());
        $scheduledExport->setRunDate($this->_model->getLastRunDate());

        $filePath = $exportDir . DS . $scheduledExport->getScheduledFileName() . '.' . $fileInfo['file_format'];
        $this->assertFileExists($filePath);

        // Restore current working directory
        chdir($cwd);
    }
}
