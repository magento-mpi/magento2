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
 * Test class for Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance
 */
class Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_FinanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer financial data export model
     *
     * @var Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance
     */
    protected $_model;

    /**
     * Bunch counter for getNextBunch() stub method
     *
     * @var int
     */
    protected $_bunchNumber;

    /**
     * Websites array (website id => code)
     *
     * @var array
     */
    protected $_websites = array(
        Magento_Core_Model_AppInterface::ADMIN_STORE_ID => 'admin',
        1                                            => 'website1',
        2                                            => 'website2',
    );

    /**
     * Customers array
     *
     * @var array
     */
    protected $_customers = array(
        array(
            'entity_id'  => 1,
            'email'      => 'test1@email.com',
            'website_id' => 1
        ),
        array(
            'entity_id'  => 2,
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
            'attribute_code' =>
                Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_CUSTOMER_BALANCE,
            'frontend_label' => 'Store Credit',
            'backend_type'   => 'decimal',
            'is_required'    => true,
        ),
        array(
            'id'   => 2,
            'attribute_code' =>
                Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS,
            'frontend_label' => 'Reward Points',
            'backend_type'   => 'int',
            'is_required'    => false,
        ),
    );

    /**
     * Input data
     *
     * @var array
     */
    protected $_inputData = array(
        array(
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL => 'test1@email.com',
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_WEBSITE => 'website1',
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE
                => 'website1',
            Magento_ImportExport_Model_Import_EntityAbstract::COLUMN_ACTION => null,
            Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => 1,
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_CUSTOMER_BALANCE
                => 100,
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS
                => 200
        ),
        array(
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL => 'test2@email.com',
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_WEBSITE => 'website2',
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE
                => 'website1',
            Magento_ImportExport_Model_Import_EntityAbstract::COLUMN_ACTION
                => Magento_ImportExport_Model_Import_EntityAbstract::COLUMN_ACTION_VALUE_DELETE,
            Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => 2,
        ),
        array(
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL => 'test2@email.com',
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_WEBSITE => 'website2',
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_FINANCE_WEBSITE
                => 'website1',
            Magento_ImportExport_Model_Import_EntityAbstract::COLUMN_ACTION => 'update',
            Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => 2,
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_CUSTOMER_BALANCE
                => 100,
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS
                => 200
        )
    );

    /**
     * Init entity adapter model
     */
    public function setUp()
    {
        $this->_bunchNumber = 0;
        if ($this->getName() == 'testImportDataCustomBehavior') {
            $dependencies = $this->_getModelDependencies(true);
        } else {
            $dependencies = $this->_getModelDependencies();
        }

        $moduleHelper = $this->getMock('Enterprise_ImportExport_Helper_Data',
            array('isRewardPointsEnabled', 'isCustomerBalanceEnabled'), array(), '', false);
        $moduleHelper->expects($this->any())->method('isRewardPointsEnabled')->will($this->returnValue(true));
        $moduleHelper->expects($this->any())->method('isCustomerBalanceEnabled')->will($this->returnValue(true));

        $coreData = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);

        $coreString = $this->getMock('Magento_Core_Helper_String', array(), array(), '', false);

        $this->_model = new Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance(
            $coreData,
            $coreString,
            $moduleHelper,
            $dependencies
        );
    }

    /**
     * Unset entity adapter model
     */
    public function tearDown()
    {
        unset($this->_model);
        unset($this->_bunchNumber);
    }

    /**
     * Create mocks for all $this->_model dependencies
     *
     * @param bool $addData
     * @return array
     */
    protected function _getModelDependencies($addData = false)
    {
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        $dataSourceModel = $this->getMock('stdClass', array('getNextBunch'));
        if ($addData) {
            $dataSourceModel->expects($this->exactly(2))->method('getNextBunch')
                ->will($this->returnCallback(array($this, 'getNextBunch')));
        }

        $connection = $this->getMock('stdClass');

        $websiteManager = $this->getMock('stdClass', array('getWebsites'));
        $websiteManager->expects($this->once())->method('getWebsites')
            ->will($this->returnCallback(array($this, 'getWebsites')));

        /** @var $customerStorage Magento_ImportExport_Model_Resource_Customer_Storage */
        $customerStorage = $this->getMock('Magento_ImportExport_Model_Resource_Customer_Storage', array('load'),
            array(), '', false);
        $customerResource = $this->getMock('Magento_Customer_Model_Resource_Customer', array('getIdFieldName'),
            array(), '', false);
        $customerResource->expects($this->any())
            ->method('getIdFieldName')
            ->will($this->returnValue('entity_id'));
        foreach ($this->_customers as $customerData) {
            /** @var $customer Magento_Customer_Model_Customer */
            $arguments = $objectManagerHelper->getConstructArguments('Magento_Customer_Model_Customer');
            $arguments['resource'] = $customerResource;
            $arguments['data'] = $customerData;
            $customer = $this->getMock('Magento_Customer_Model_Customer', array('_construct'), $arguments);
            $customerStorage->addCustomer($customer);
        }

        $objectFactory = $this->getMock('stdClass', array('getModelInstance'));
        $objectFactory->expects($this->any())->method('getModelInstance')
            ->will($this->returnCallback(array($this, 'getModelInstance')));

        /** @var $attributeCollection Magento_Data_Collection */
        $attributeCollection = $this->getMock('Magento_Data_Collection', array('getEntityTypeCode'));
        foreach ($this->_attributes as $attributeData) {
            /** @var $attribute Magento_Eav_Model_Entity_Attribute_Abstract */
            $arguments = $objectManagerHelper->getConstructArguments('Magento_Eav_Model_Entity_Attribute_Abstract');
            $arguments['data'] = $attributeData;
            $attribute = $this->getMockForAbstractClass('Magento_Eav_Model_Entity_Attribute_Abstract',
                $arguments, '', true, true, true, array('_construct')
            );
            $attributeCollection->addItem($attribute);
        }

        $adminUser = $this->getMock('stdClass', array('getUsername'));
        $adminUser->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('admin'));

        $data = array(
            'data_source_model'            => $dataSourceModel,
            'connection'                   => $connection,
            'json_helper'                  => 'not_used',
            'string_helper'                => $this->getMock('Magento_Core_Helper_String',
                array(), array(), '', false, false
            ),
            'page_size'                    => 1,
            'max_data_size'                => 1,
            'bunch_size'                   => 1,
            'website_manager'              => $websiteManager,
            'store_manager'                => 'not_used',
            'entity_type_id'               => 1,
            'customer_storage'             => $customerStorage,
            'object_factory'               => $objectFactory,
            'attribute_collection'         => $attributeCollection,
            'admin_user'                   => $adminUser
        );

        return $data;
    }

    /**
     * Stub for next bunch of validated rows getter. It is callback function which is used to emulate work of data
     * source model. It should return data on first call and null on next call to emulate end of bunch.
     *
     * @return array|null
     */
    public function getNextBunch()
    {
        if ($this->_bunchNumber == 0) {
            $data = $this->_inputData;
        } else {
            $data = null;
        }
        $this->_bunchNumber++;

        return $data;
    }

    /**
     * Iterate stub
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param Magento_Data_Collection $collection
     * @param int $pageSize
     * @param array $callbacks
     */
    public function iterate(Magento_Data_Collection $collection, $pageSize, array $callbacks)
    {
        foreach ($collection as $customer) {
            foreach ($callbacks as $callback) {
                call_user_func($callback, $customer);
            }
        }
    }

    /**
     * Get websites stub
     *
     * @param bool $withDefault
     * @return array
     */
    public function getWebsites($withDefault = false)
    {
        $websites = array();
        if (!$withDefault) {
            unset($websites[0]);
        }
        foreach ($this->_websites as $id => $code) {
            if (!$withDefault && $id == Magento_Core_Model_AppInterface::ADMIN_STORE_ID) {
                continue;
            }
            $websiteData = array(
                'id'   => $id,
                'code' => $code,
            );
            $websites[$id] = new Magento_Object($websiteData);
        }

        return $websites;
    }

    /**
     * Callback method for mock object Magento_Core_Model_Config object
     *
     * @param string $modelClass
     * @param array|object $constructArguments
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getModelInstance($modelClass = '', $constructArguments = array())
    {
        switch ($modelClass) {
            case 'Enterprise_CustomerBalance_Model_Balance':
                $instance = $this->getMock($modelClass, array('setCustomer', 'setWebsiteId', 'loadByCustomer',
                        'getAmount', 'setAmountDelta', 'setComment', 'save'
                    ), $constructArguments, '', false
                );
                $instance->expects($this->any())
                    ->method('setCustomer')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('setWebsiteId')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('loadByCustomer')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('getAmount')
                    ->will($this->returnValue(0));
                $instance->expects($this->any())
                    ->method('setAmountDelta')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('setComment')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('save')
                    ->will($this->returnSelf());
                break;
            case 'Enterprise_Reward_Model_Reward':
                $instance = $this->getMock($modelClass, array('setCustomer', 'setWebsiteId', 'loadByCustomer',
                        'getPointsBalance', 'setPointsDelta', 'setAction', 'setComment', 'updateRewardPoints'
                    ), $constructArguments, '', false
                );
                $instance->expects($this->any())
                    ->method('setCustomer')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('setWebsiteId')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('loadByCustomer')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('getPointsBalance')
                    ->will($this->returnValue(0));
                $instance->expects($this->any())
                    ->method('setPointsDelta')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('setAction')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('setComment')
                    ->will($this->returnSelf());
                $instance->expects($this->any())
                    ->method('updateRewardPoints')
                    ->will($this->returnSelf());
                break;
            default:
                $instance = $this->getMock($modelClass, array(), $constructArguments, '', false);
                break;
        }
        return $instance;
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
            'valid' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_valid.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array()
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array()
                    ),
                )
            ),
            'no website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_no_website.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                                => array(array(1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_WEBSITE
                                ))
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                                => array(array(1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_WEBSITE
                                ))
                        ),
                    )
                )
            ),
            'empty website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_website.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                                => array(array(1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_WEBSITE
                                ))
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_WEBSITE_IS_EMPTY
                                => array(array(1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_WEBSITE
                                ))
                        ),
                    )
                )
            ),
            'no email' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_no_email.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL
                                )
                            )
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL
                                )
                            )
                        ),
                    )
                )
            ),
            'empty email' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_email.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL
                                )
                            )
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_EMAIL_IS_EMPTY =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL
                                )
                            )
                        ),
                    )
                )
            ),
            'empty finance website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_finance_website.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_FINANCE_WEBSITE_IS_EMPTY =>
                                array(
                                    array(
                                        1,
                                        Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                        COLUMN_FINANCE_WEBSITE
                                    )
                                )
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_FINANCE_WEBSITE_IS_EMPTY =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                    COLUMN_FINANCE_WEBSITE
                                )
                            )
                        ),
                    )
                )
            ),
            'invalid email' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_email.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_INVALID_EMAIL =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL
                                )
                            )
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_INVALID_EMAIL =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::COLUMN_EMAIL
                                )
                            )
                        ),
                    )
                )
            ),
            'invalid website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_website.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_INVALID_WEBSITE =>
                                array(
                                    array(
                                        1,
                                        Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                        COLUMN_WEBSITE
                                    )
                                )
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_INVALID_WEBSITE =>
                                array(
                                    array(
                                        1,
                                        Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                        COLUMN_WEBSITE
                                    )
                                )
                        ),
                    )
                )
            ),
            'invalid finance website' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_finance_website.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_INVALID_FINANCE_WEBSITE =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                    COLUMN_FINANCE_WEBSITE
                                )
                            )
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_INVALID_FINANCE_WEBSITE =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                    COLUMN_FINANCE_WEBSITE
                                )
                            )
                        ),
                    )
                )
            ),
            'invalid finance website (admin)' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_finance_website_admin.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_INVALID_FINANCE_WEBSITE =>
                                array(
                                    array(
                                        1,
                                        Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                        COLUMN_FINANCE_WEBSITE
                                    )
                                )
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_INVALID_FINANCE_WEBSITE =>
                            array(
                                array(
                                    1,
                                    Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                                    COLUMN_FINANCE_WEBSITE
                                )
                            )
                        ),
                    )
                )
            ),
            'no customer' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_no_customer.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_CUSTOMER_NOT_FOUND =>
                                array(array(1, null))
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::
                            ERROR_CUSTOMER_NOT_FOUND =>
                                array(array(1, null))
                        ),
                    )
                )
            ),
            'invalid_attribute_value' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_invalid_attribute_value.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors' => array(
                            "Please correct the value for '%s'." => array(
                                array(1, 'store_credit'), array(1, 'reward_points'))
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(),
                    )
                )
            ),
            'empty_optional_attribute_value' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_optional_attribute_value.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors'  => array()
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(),
                    )
                )
            ),
            'empty_required_attribute_value' => array(
                '$rowData' => include __DIR__ . '/_files/row_data_empty_required_attribute_value.php',
                '$behaviors' => array(
                    Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE => array(
                        'errors'  => array(
                            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_VALUE_IS_REQUIRED
                                => array(array(1, 'store_credit'))
                        ),
                    ),
                    Magento_ImportExport_Model_Import::BEHAVIOR_DELETE => array(
                        'errors' => array(),
                    )
                )
            ),
        );
    }

    /**
     * Test Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::validateRow() with different values
     * in case when add/update behavior is performed
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::_validateRowForUpdate
     * @dataProvider validateRowDataProvider
     *
     * @param array $rowData
     * @param array $behaviors
     */
    public function testValidateRowForUpdate(array $rowData, array $behaviors)
    {
        $behavior = Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE;

        $this->_model->setParameters(
            array('behavior' => $behavior)
        );

        if (!count($behaviors[$behavior]['errors'])) {
            $this->assertTrue($this->_model->validateRow($rowData, 0));
        } else {
            $this->assertFalse($this->_model->validateRow($rowData, 0));
        }

        $this->assertAttributeEquals($behaviors[$behavior]['errors'], '_errors', $this->_model);
    }

    /**
     * Test Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::validateRow()
     * with 2 rows with identical PKs in case when add/update behavior is performed
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::_validateRowForUpdate
     */
    public function testValidateRowForUpdateDuplicateRows()
    {
        $behavior = Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE;

        $this->_model->setParameters(
            array('behavior' => $behavior)
        );

        $secondRow = $firstRow = array(
            '_website'         => 'website1',
            '_email'           => 'test1@email.com',
            '_finance_website' => 'website2',
            'store_credit'     => 10.5,
            'reward_points'    => 5,
        );
        $secondRow['store_credit']  = 20;
        $secondRow['reward_points'] = 30;

        $errors = array(
            Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::ERROR_DUPLICATE_PK
                => array(array(2, null))
        );

        $this->assertTrue($this->_model->validateRow($firstRow, 0));
        $this->assertFalse($this->_model->validateRow($secondRow, 1));

        $this->assertAttributeEquals($errors, '_errors', $this->_model);
    }

    /**
     * Test Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::validateRow() with different values
     * in case when delete behavior is performed
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::_validateRowForDelete
     * @dataProvider validateRowDataProvider
     *
     * @param array $rowData
     * @param array $behaviors
     */
    public function testValidateRowForDelete(array $rowData, array $behaviors)
    {
        $behavior = Magento_ImportExport_Model_Import::BEHAVIOR_DELETE;

        $this->_model->setParameters(
            array('behavior' => $behavior)
        );

        if (!count($behaviors[$behavior]['errors'])) {
            $this->assertTrue($this->_model->validateRow($rowData, 0));
        } else {
            $this->assertFalse($this->_model->validateRow($rowData, 0));
        }

        $this->assertAttributeEquals($behaviors[$behavior]['errors'], '_errors', $this->_model);
    }

    /**
     * Test entity type code getter
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::getEntityTypeCode
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('customer_finance', $this->_model->getEntityTypeCode());
    }

    /**
     * Test data import
     *
     * @covers Enterprise_ImportExport_Model_Import_Entity_Eav_Customer_Finance::importData
     */
    public function testImportDataCustomBehavior()
    {
        $this->assertTrue($this->_model->importData());
    }
}
