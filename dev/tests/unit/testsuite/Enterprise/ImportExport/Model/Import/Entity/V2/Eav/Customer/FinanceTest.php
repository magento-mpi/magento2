<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
 */
class Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Abstract customer finance export model
     *
     * @var Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = array(
        1 => 'website1',
        2 => 'website2',
    );

    /**
     * Customers array
     *
     * @var array
     */
    protected $_customers = array(
        array(
            'id'         => 1,
            'email'      => 'test1@email.com',
            'website_id' => 1
        ),
        array(
            'id'         => 2,
            'email'      => 'test2@email.com',
            'website_id' => 2
        ),
    );

    /**
     * Init entity adapter model
     */
    public function setUp()
    {
        parent::setUp();

        $this->_model = $this->_getModelMock();
    }

    /**
     * Unset entity adapter model
     */
    public function tearDown()
    {
        unset($this->_model);

        parent::tearDown();
    }

    /**
     * Create mock for customer address model class
     *
     * @return Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
     */
    protected function _getModelMock()
    {
        $modelMock = $this->getMock('Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance',
            array('isAttributeValid', '_getCustomerCollection'), array(), '', false, true, true
        );

        $modelMock->expects($this->any())
            ->method('isAttributeValid')
            ->will($this->returnValue(true));

        $customerCollection = new Varien_Data_Collection();
        foreach ($this->_customers as $customer) {
            $customerCollection->addItem(new Varien_Object($customer));
        }

        $modelMock->expects($this->any())
            ->method('_getCustomerCollection')
            ->will($this->returnValue($customerCollection));

        $method = new ReflectionMethod($modelMock, '_initCustomers');
        $method->setAccessible(true);
        $method->invoke($modelMock);

        $property = new ReflectionProperty($modelMock, '_websiteCodeToId');
        $property->setAccessible(true);
        $property->setValue($modelMock, array_flip($this->_websites));

        return $modelMock;
    }

    /**
     * Data provider of row data and errors
     *
     * @return array
     */
    public function validateRowDataProvider()
    {
        return array(
            'valid' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_valid.php',
                '$errors'  => array(),
                '$isValid' => true,
            ),
            'no website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_no_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                        => array(
                            array(1,
                                Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_WEBSITE
                            )
                        )
                ),
            ),
            'empty website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                        => array(
                            array(1,
                                Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_WEBSITE
                            )
                        )
                ),
            ),
            'no email' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_no_email.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_EMAIL)
                    )
                ),
            ),
            'empty email' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_email.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_EMAIL)
                    )
                ),
            ),
            'invalid email' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_email.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_INVALID_EMAIL => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_EMAIL)
                    )
                ),
            ),
            'invalid website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_INVALID_WEBSITE => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_WEBSITE)
                    )
                ),
            ),
            'no customer' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_no_customer.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_CUSTOMER_NOT_FOUND
                        => array(
                            array(1, null)
                        )
                ),
            ),
        );
    }

    /**
     * Test Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::validateRow() with different values
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::validateRow()
     * @dataProvider validateRowDataProvider
     *
     * @param array $rowData
     * @param array $errors
     * @param boolean $isValid
     */
    public function testValidateRow(array $rowData, array $errors, $isValid = false)
    {
        if ($isValid) {
            $this->assertTrue($this->_model->validateRow($rowData, 0));
        } else {
            $this->assertFalse($this->_model->validateRow($rowData, 0));
        }
        $this->assertAttributeEquals($errors, '_errors', $this->_model);
    }

    /**
     * Test entity type code getter
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('customer_finance', $this->_model->getEntityTypeCode());
    }
}
