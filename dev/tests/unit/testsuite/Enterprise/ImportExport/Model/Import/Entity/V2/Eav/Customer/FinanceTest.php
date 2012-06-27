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
    /**#@+
     * Mage registry helper prefix
     */
    const MAGE_REGISTRY_HELPER_PREFIX = '_helper/';
    /**#@-*/

    /**
     * Abstract customer finance export model
     *
     * @var Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Array of helpers to mock and put to the registry
     *
     * @var array
     */
    protected $_helpers = array('Mage_Core_Helper_String', 'Mage_ImportExport_Helper_Data');

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = array(
        Mage_Core_Model_App::ADMIN_STORE_ID => 'admin',
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
     * Attributes array
     *
     * @var array
     */
    protected $_attributes = array(
        array(
            'id'   => 1,
            'attribute_code' => 'store_credit',
            'frontend_label' => 'Store Credit',
            'backend_type'   => 'decimal',
            'is_required'    => true,
        ),
        array(
            'id'   => 2,
            'attribute_code' => 'reward_points',
            'frontend_label' => 'Reward Points',
            'backend_type'   => 'int',
            'is_required'    => false,
        ),
    );

    /**
     * Init entity adapter model
     */
    public function setUp()
    {
        parent::setUp();

        $this->_unregisterHelpers();
        $this->_mockHelpersAndRegisterInRegistry();
        $this->_model = $this->_getModelMock();
    }

    /**
     * Unset entity adapter model
     */
    public function tearDown()
    {
        unset($this->_model);
        $this->_unregisterHelpers();

        parent::tearDown();
    }

    /**
     * Mock helpers
     */
    protected function _mockHelpersAndRegisterInRegistry()
    {
        foreach ($this->_helpers as $helperKey) {
            $helper = $this->getMock($helperKey, array('__'));
            $helper->expects($this->any())
                ->method('__')
                ->will($this->returnArgument(0));

            Mage::register(self::MAGE_REGISTRY_HELPER_PREFIX . $helperKey, $helper);
        }
    }

    /**
     * Un-register mocked helpers
     */
    protected function _unregisterHelpers()
    {
        foreach ($this->_helpers as $helperKey) {
            Mage::unregister(self::MAGE_REGISTRY_HELPER_PREFIX . $helperKey);
        }
    }

    /**
     * Create mock for customer finance model class
     *
     * @return Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
     */
    protected function _getModelMock()
    {
        $modelMock = $this->getMock('Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance',
            array('_getCustomerCollection', '_getAttributeCollection'), array(), '', false, true,
            true
        );

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

        $attributeCollection = new Varien_Data_Collection();
        foreach ($this->_attributes as $attribute) {
            $attributeCollection->addItem(new Varien_Object($attribute));
        }

        $modelMock->expects($this->any())
            ->method('_getAttributeCollection')
            ->will($this->returnValue($attributeCollection));

        $method = new ReflectionMethod($modelMock, '_initAttributes');
        $method->setAccessible(true);
        $method->invoke($modelMock);

        return $modelMock;
    }

    /**
     * Create mock for customer finance model class
     *
     * @return Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
     */
    protected function _getModelMockForTestInitAttributes()
    {
        $modelMock = $this->getMock('Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance',
            array('_getAttributeCollection'), array(), '', false, true,
            true
        );

        $attributeCollection = new Varien_Data_Collection();
        foreach ($this->_attributes as $attribute) {
            $attributeCollection->addItem(new Varien_Object($attribute));
        }

        $modelMock->expects($this->any())
            ->method('_getAttributeCollection')
            ->will($this->returnValue($attributeCollection));

        return $modelMock;
    }

    /**
     * Data provider of row data and errors
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return array
     */
    public function validateRowDataProvider()
    {
        return array(
            'valid' => array('$rowData' => include __DIR__ . '/_files/row_data_valid.php',
                '$errors'  => array(), '$isValid' => true,
            ),
            'no website' => array('$rowData' => include __DIR__ . '/_files/row_data_no_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                        => array(array(1,
                            Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_WEBSITE
                        ))
                ),
            ),
            'empty website' => array('$rowData' => include __DIR__ . '/_files/row_data_empty_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                        => array(array(1,
                            Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_WEBSITE
                        ))
                ),
            ),
            'no email' => array('$rowData' => include __DIR__ . '/_files/row_data_no_email.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_EMAIL)
                    )
                ),
            ),
            'empty email' => array('$rowData' => include __DIR__ . '/_files/row_data_empty_email.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_EMAIL)
                    )
                ),
            ),
            'no finance website' => array('$rowData' => include __DIR__ . '/_files/row_data_no_finance_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_FINANCE_WEBSITE_IS_EMPTY
                        => array(array(1,
                            Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE
                        ))
                ),
            ),
            'empty finance website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_finance_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_FINANCE_WEBSITE_IS_EMPTY
                        => array(array(1,
                            Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE
                        ))
                ),
            ),
            'invalid email' => array('$rowData' => include __DIR__ . '/_files/row_data_invalid_email.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_INVALID_EMAIL => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_EMAIL)
                    )
                ),
            ),
            'invalid website' => array('$rowData' => include __DIR__ . '/_files/row_data_invalid_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_INVALID_WEBSITE => array(
                        array(1, Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_WEBSITE)
                    )
                ),
            ),
            'invalid finance website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_finance_website.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_INVALID_FINANCE_WEBSITE
                        => array( array(1,
                            Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE
                        ))
                ),
            ),
            'invalid finance website (admin)' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_finance_website_admin.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_INVALID_FINANCE_WEBSITE
                        => array( array(1,
                            Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE
                        ))
                ),
            ),
            'no customer' => array('$rowData' => include __DIR__ . '/_files/row_data_no_customer.php',
                '$errors' => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_CUSTOMER_NOT_FOUND
                        => array(array(1, null))
                ),
            ),
            'invalid_attribute_value' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_attribute_value.php',
                '$errors' => array(
                    "Invalid value for '%s'" => array(array(1, 'store_credit'), array(1, 'reward_points'))
                ),
            ),
            'empty_optional_attribute_value' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_optional_attribute_value.php',
                '$errors'  => array(), '$isValid' => true,
            ),
            'empty_required_attribute_value' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_required_attribute_value.php',
                '$errors'  => array(
                    Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::ERROR_VALUE_IS_REQUIRED
                        => array(array(1, 'store_credit'))
                ),
            ),
        );
    }

    /**
     * Test filling attribute array
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::_initAttributes()
     */
    public function testInitAttributes()
    {
        $modelMock = $this->_getModelMockForTestInitAttributes();

        $method = new ReflectionMethod($modelMock, '_initAttributes');
        $method->setAccessible(true);
        $method->invoke($modelMock);

        $attributes = array();
        foreach ($this->_attributes as $attribute) {
            $attributes[$attribute['attribute_code']] = array(
                'id'          => $attribute['id'],
                'code'        => $attribute['attribute_code'],
                'is_required' => $attribute['is_required'],
                'type'        => $attribute['backend_type'],
            );
        }

        $this->assertAttributeEquals($attributes, '_attributes', $modelMock);
    }

    /**
     * Test Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::validateRow() with different values
     *
     * @depends testInitAttributes
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
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance::getEntityTypeCode()
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('customer_finance', $this->_model->getEntityTypeCode());
    }
}
