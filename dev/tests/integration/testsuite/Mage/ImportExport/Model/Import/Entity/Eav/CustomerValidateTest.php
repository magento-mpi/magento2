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
 * Test for class Mage_ImportExport_Model_Import_Entity_Eav_Customer which covers validation logic
 *
 * @magentoDataFixture Mage/ImportExport/_files/customers.php
 */
class Mage_ImportExport_Model_Import_Entity_Eav_CustomerValidateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which used for tests
     *
     * @var Mage_ImportExport_Model_Import_Entity_Eav_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Customer data
     *
     * @var array
     */
    protected $_customerData;

    /**
     * Create all necessary data for tests
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_model = Mage::getModel('Mage_ImportExport_Model_Import_Entity_Eav_Customer');
        $this->_model->setParameters(array(
            'behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE
        ));

        $propertyAccessor = new ReflectionProperty($this->_model, '_messageTemplates');
        $propertyAccessor->setAccessible(true);
        $propertyAccessor->setValue($this->_model, array());

        $this->_customerData = array(
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'group_id' => 1,
            Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL => 'customer@example.com',
            Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_WEBSITE => 'base',
            Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_STORE => 'default',
            'store_id' => 1,
            'website_id' => 1,
            'password' => 'password',
        );
    }

    /**
     * Test which check duplicated data validation
     */
    public function testValidateRowDuplicateEmail()
    {
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertEquals(0, $this->_model->getErrorsCount());

        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL] =
            strtoupper($this->_customerData[Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL]);
        $this->_model->validateRow($this->_customerData, 1);
        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertArrayHasKey(Mage_ImportExport_Model_Import_Entity_Eav_Customer::ERROR_DUPLICATE_EMAIL_SITE,
            $this->_model->getErrorMessages());
    }

    /**
     * Test which check validation of customer email
     */
    public function testValidateRowInvalidEmail()
    {
        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL]
            = 'wrong_email@format';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertArrayHasKey(Mage_ImportExport_Model_Import_Entity_Eav_Customer::ERROR_INVALID_EMAIL,
            $this->_model->getErrorMessages()
        );
    }

    /**
     * Test which check validation of website data
     */
    public function testValidateRowInvalidWebsite()
    {
        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_WEBSITE]
            = 'not_existing_web_site';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertArrayHasKey(Mage_ImportExport_Model_Import_Entity_Eav_Customer::ERROR_INVALID_WEBSITE,
            $this->_model->getErrorMessages()
        );
    }

    /**
     * Test which check validation of store data
     */
    public function testValidateRowInvalidStore()
    {
        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_STORE]
            = 'not_existing_web_store';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertArrayHasKey(Mage_ImportExport_Model_Import_Entity_Eav_Customer::ERROR_INVALID_STORE,
            $this->_model->getErrorMessages()
        );
    }

    /**
     * Test which check validation of password length - incorrect case
     */
    public function testValidateRowPasswordLengthIncorrect()
    {
        $this->_customerData['password'] = '12345';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertArrayHasKey(Mage_ImportExport_Model_Import_Entity_Eav_Customer::ERROR_PASSWORD_LENGTH,
            $this->_model->getErrorMessages()
        );
    }

    /**
     * Test which check validation of password length - correct case
     */
    public function testValidateRowPasswordLengthCorrect()
    {
        $this->_customerData['password'] = '1234567890';
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertEquals(0, $this->_model->getErrorsCount());
    }

    /**
     * Test which check validation of required fields
     */
    public function testValidateRowAttributeRequired()
    {
        unset($this->_customerData['firstname']);
        unset($this->_customerData['lastname']);
        unset($this->_customerData['group_id']);

        $this->_model->validateRow($this->_customerData, 0);
        $this->assertEquals(0, $this->_model->getErrorsCount());

        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL]
            = 'new.customer@example.com';
        $this->_model->validateRow($this->_customerData, 1);
        $this->assertGreaterThan(0, $this->_model->getErrorsCount());
        $this->assertArrayHasKey(Mage_ImportExport_Model_Import_Entity_Eav_Customer::ERROR_VALUE_IS_REQUIRED,
            $this->_model->getErrorMessages()
        );
    }

    /**
     * Check customer email validation for delete behavior
     *
     * @covers Mage_ImportExport_Model_Import_Entity_Eav_Customer::validateRow
     */
    public function testValidateEmailForDeleteBehavior()
    {
        $this->_customerData[Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL]
            = 'new.customer@example.com';

        $this->_model->setParameters(array(
            'behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_DELETE
        ));
        $this->_model->validateRow($this->_customerData, 0);
        $this->assertGreaterThan(0, $this->_model->getErrorsCount());
        $this->assertArrayHasKey(Mage_ImportExport_Model_Import_Entity_Eav_Customer::ERROR_CUSTOMER_NOT_FOUND,
            $this->_model->getErrorMessages()
        );
    }
}
