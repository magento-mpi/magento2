<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_ImportExport
 * @magentoDataFixture Mage/ImportExport/_files/customers.php
 */
class Mage_ImportExport_Model_Import_Entity_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Import_Entity_Customer Mock
     */
    protected $_model;
    protected $_modelDelete;

    /**
     * @var boolean Is error occured
     */
    protected $_errorWas;

    /**
     * @var array Array of errors
     */
    protected $_errors;
    protected $_customerData;

    protected function setUp()
    {
        $this->_model = $this->getMock('Mage_ImportExport_Model_Import_Entity_Customer',
            array('addRowError', 'getBehavior')
        );

        $errorWas = false;
        $errors = array();
        $this->_errorWas = &$errorWas;
        $this->_errors = &$errors;

        $checkException = function ($errorCode, $errorRowNum, $colName = null) use (&$errorWas, &$errors) {
            $errorWas = true;
            $errors[] = array($errorCode, $errorRowNum, $colName);
        };

        $this->_model->expects($this->any())
            ->method('addRowError')
            ->will($this->returnCallback($checkException));

        $this->_modelDelete = clone $this->_model;

        $this->_model->expects($this->any())
            ->method('getBehavior')
            ->will($this->returnValue(Mage_ImportExport_Model_Import::BEHAVIOR_APPEND));

        $this->_customerData = array(
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'group_id' => 1,
            Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL => 'customer@example.com',
            Mage_ImportExport_Model_Import_Entity_Customer::COL_WEBSITE => 'base',
            Mage_ImportExport_Model_Import_Entity_Customer::COL_STORE => 'default',
            'store_id' => 1,
            'website_id' => 1,
            'password' => 'password',
        );
    }

    public function testValidateRowDuplicateEmail()
    {
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertFalse($this->_errorWas);

        $this->_errorWas = false;
        $this->_errors = array();

        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL] =
            strtoupper($this->_customerData[Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL]);
        $this->_model->validateRow($this->_customerData, 1);
        $this->assertTrue($this->_errorWas);
        $this->assertEquals(Mage_ImportExport_Model_Import_Entity_Customer::ERROR_DUPLICATE_EMAIL_SITE,
            $this->_errors[0][0]
        );
    }

    public function testValidateRowInvalidEmail()
    {
        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL] = 'wrong_email@format';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertTrue($this->_errorWas);
        $this->assertEquals(Mage_ImportExport_Model_Import_Entity_Customer::ERROR_INVALID_EMAIL,
            $this->_errors[0][0]
        );
    }

    public function testValidateRowInvalidWebsite()
    {
        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Customer::COL_WEBSITE] = 'not_existing_web_site';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertTrue($this->_errorWas);
        $this->assertEquals(Mage_ImportExport_Model_Import_Entity_Customer::ERROR_INVALID_WEBSITE,
            $this->_errors[0][0]
        );
    }

    public function testValidateRowInvalidStore()
    {
        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Customer::COL_STORE] = 'not_existing_web_store';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertTrue($this->_errorWas);
        $this->assertEquals(Mage_ImportExport_Model_Import_Entity_Customer::ERROR_INVALID_STORE,
            $this->_errors[0][0]
        );
    }

    public function testValidateRowPasswordLength()
    {
        $this->_customerData['password'] = '12345';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertTrue($this->_errorWas);
        $this->assertEquals(Mage_ImportExport_Model_Import_Entity_Customer::ERROR_PASSWORD_LENGTH,
            $this->_errors[0][0]
        );
    }

    public function testValidateRowAttributeRequired()
    {
        unset($this->_customerData['firstname']);
        unset($this->_customerData['lastname']);
        unset($this->_customerData['group_id']);

        $this->_model->validateRow($this->_customerData, 0);
        $this->assertFalse($this->_errorWas);

        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL] = 'new.customer@example.com';
        $this->_model->validateRow($this->_customerData, 1);
        $this->assertTrue($this->_errorWas);
        foreach ($this->_errors as $error) {
            $this->assertEquals(Mage_ImportExport_Model_Import_Entity_Customer::ERROR_VALUE_IS_REQUIRED,
                $error[0]
            );
        }
    }

    public function testValidateRowDelete()
    {
        $this->_modelDelete->expects($this->any())
            ->method('getBehavior')
            ->will($this->returnValue(Mage_ImportExport_Model_Import::BEHAVIOR_DELETE));

        $this->_modelDelete->validateRow($this->_customerData, 0);
        $this->assertFalse($this->_errorWas);

        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Customer::COL_EMAIL] = 'new.customer@example.com';
        $this->_modelDelete->validateRow($this->_customerData, 1);
        $this->assertTrue($this->_errorWas);
        $this->assertEquals(Mage_ImportExport_Model_Import_Entity_Customer::ERROR_EMAIL_SITE_NOT_FOUND,
            $this->_errors[0][0]
        );
    }
}
