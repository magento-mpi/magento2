<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ImportExport\Model\Import\Entity\AbstractEntity
 */
class Magento_ImportExport_Model_Import_Entity_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Abstract import entity model
     *
     * @var \Magento\ImportExport\Model\Import\Entity\AbstractEntity|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        parent::setUp();

        $this->_model = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Import\Entity\AbstractEntity', array(),
            '', false, true, true, array('_saveValidatedBunches')
        );
    }

    public function tearDown()
    {
        unset($this->_model);

        parent::tearDown();
    }

    /**
     * Create mock for data helper and push it to registry
     *
     * @return \Magento\ImportExport\Helper\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _createDataHelperMock()
    {
        /** @var $helper \Magento\ImportExport\Helper\Data */
        $helper = $this->getMock('Magento\ImportExport\Helper\Data', array(), array(), '', false);
        $registryKey = '_helper/Magento\ImportExport\Helper\Data';
        if (Mage::registry($registryKey)) {
            Mage::unregister($registryKey);
        }
        Mage::register($registryKey, $helper);

        return $helper;
    }

    /**
     * Create source adapter mock and set it into model object which tested in this class
     *
     * @param array $columns value which will be returned by method getColNames()
     * @return \Magento\ImportExport\Model\Import\SourceAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _createSourceAdapterMock(array $columns)
    {
        /** @var $source \Magento\ImportExport\Model\Import\SourceAbstract|PHPUnit_Framework_MockObject_MockObject */
        $source = $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\SourceAbstract', array(), '', false,
            true, true, array('getColNames')
        );
        $source->expects($this->any())
            ->method('getColNames')
            ->will($this->returnValue($columns));
        $this->_model->setSource($source);

        return $source;
    }

    /**
     * Test for method validateData()
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\AbstractEntity::validateData
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Columns number: "1" have empty headers
     */
    public function testValidateDataEmptyColumnName()
    {
        $this->_createDataHelperMock();
        $this->_createSourceAdapterMock(array(''));
        $this->_model->validateData();
    }

    /**
     * Test for method validateData()
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\AbstractEntity::validateData
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Columns number: "1" have empty headers
     */
    public function testValidateDataColumnNameWithWhitespaces()
    {
        $this->_createDataHelperMock();
        $this->_createSourceAdapterMock(array('  '));
        $this->_model->validateData();
    }

    /**
     * Test for method validateData()
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\AbstractEntity::validateData
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Column names: "_test1" are invalid
     */
    public function testValidateDataAttributeNames()
    {
        $this->_createDataHelperMock();
        $this->_createSourceAdapterMock(array('_test1'));
        $this->_model->validateData();
    }

    /**
     * Test for method isAttributeValid()
     *
     * @dataProvider isAttributeValidDataProvider
     * @covers \Magento\ImportExport\Model\Import\Entity\AbstractEntity::isAttributeValid
     *
     * @param string $attrCode
     * @param array $attrParams
     * @param array $rowData
     * @param int $rowNum
     * @param bool $expectedResult
     */
    public function testIsAttributeValid($attrCode, array $attrParams, array $rowData, $rowNum, $expectedResult)
    {
        $this->_createDataHelperMock();
        $this->_createSourceAdapterMock(array('_test1'));
        $this->assertEquals($expectedResult,
            $this->_model->isAttributeValid($attrCode, $attrParams, $rowData, $rowNum));
    }

    /**
     * Data provider for testIsAttributeValid
     *
     * @return array
     */
    public function isAttributeValidDataProvider()
    {
        return array(
            array('created_at', array('type' => 'datetime'), array('created_at' => '2012-02-29'), 1, true),
            array('dob', array('type' => 'datetime'), array('dob' => '29.02.2012'), 1, true),
            array('created_at', array('type' => 'datetime'), array('created_at' => '02/29/2012'), 1, true),
            array('dob', array('type' => 'datetime'), array('dob' => '2012-02-29 21:12:59'), 1, true),
            array('created_at', array('type' => 'datetime'), array('created_at' => '29.02.2012 11:12:59'), 1, true),
            array('dob', array('type' => 'datetime'), array('dob' => '02/29/2012 11:12:59'), 1, true),
            array('created_at', array('type' => 'datetime'), array('created_at' => '2012602-29'), 1, false),
            array('dob', array('type' => 'datetime'), array('dob' => '32.12.2012'), 1, false),
            array('created_at', array('type' => 'datetime'), array('created_at' => '02/30/-2012'), 1, false),
            array('dob', array('type' => 'datetime'), array('dob' => '2012-13-29 21:12:59'), 1, false),
            array('created_at', array('type' => 'datetime'), array('created_at' => '11.02.4 11:12:59'), 1, false),
            array('dob', array('type' => 'datetime'), array('dob' => '02/29/2012 11:12:67'), 1, false)

        );
    }

}
