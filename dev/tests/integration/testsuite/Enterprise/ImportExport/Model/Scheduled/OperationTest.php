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
        $this->_model= new Enterprise_ImportExport_Model_Scheduled_Operation();
    }

    /**
     * Tear down before test
     */
    protected function tearDown()
    {
        unset($this->_model);
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
     * @expectedException Mage_Core_Exception
     */
    public function testGetHistoryFilePathException()
    {
        $this->_model->getHistoryFilePath();
    }
}
