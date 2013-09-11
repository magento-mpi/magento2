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
 * Test class for \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract
 *
 * @todo fix in the scope of https://wiki.magento.com/display/MAGE2/Technical+Debt+%28Team-Donetsk-B%29
 */
class Magento_ImportExport_Model_Import_Entity_Eav_CustomerAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Abstract customer export model
     *
     * @var \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract|PHPUnit_Framework_MockObject_MockObject
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
     * Available behaviours
     *
     * @var array
     */
    protected $_availableBehaviors = array(
        \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE,
        \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE,
    );

    public function setUp()
    {
        parent::setUp();

        $this->_model = $this->_getModelMock();
    }

    public function tearDown()
    {
        unset($this->_model);

        parent::tearDown();
    }

    /**
     * Create mock for abstract customer model class
     *
     * @return \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getModelMock()
    {
        $customerCollection = new \Magento\Data\Collection();
        foreach ($this->_customers as $customer) {
            $customerCollection->addItem(new \Magento\Object($customer));
        }

        $modelMock = $this->getMockForAbstractClass('\Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract',
            array(),
            '',
            false,
            true,
            true,
            array('_getCustomerCollection', '_validateRowForUpdate', '_validateRowForDelete')
        );
        $property = new ReflectionProperty($modelMock, '_websiteCodeToId');
        $property->setAccessible(true);
        $property->setValue($modelMock, array_flip($this->_websites));

        $property = new ReflectionProperty($modelMock, '_availableBehaviors');
        $property->setAccessible(true);
        $property->setValue($modelMock, $this->_availableBehaviors);

        $modelMock->expects($this->any())
            ->method('_getCustomerCollection')
            ->will($this->returnValue($customerCollection));

        return $modelMock;
    }

    /**
     * Data provider of row data and errors for _checkUniqueKey
     *
     * @return array
     */
    public function checkUniqueKeyDataProvider()
    {
        return array(
            'valid' => array(
                '$rowData' => include __DIR__ . '/Customer/_files/row_data_abstract_valid.php',
                '$errors'  => array(),
                '$isValid' => true,
            ),
            'no website' => array(
                '$rowData' => include __DIR__ . '/Customer/_files/row_data_abstract_no_website.php',
                '$errors' => array(
                    \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::ERROR_WEBSITE_IS_EMPTY => array(
                        array(1, \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::COLUMN_WEBSITE)
                    )
                ),
            ),
            'empty website' => array(
                '$rowData' => include __DIR__ . '/Customer/_files/row_data_abstract_empty_website.php',
                '$errors' => array(
                    \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::ERROR_WEBSITE_IS_EMPTY => array(
                        array(1, \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::COLUMN_WEBSITE)
                    )
                ),
            ),
            'no email' => array(
                '$rowData' => include __DIR__ . '/Customer/_files/row_data_abstract_no_email.php',
                '$errors' => array(
                    \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::ERROR_EMAIL_IS_EMPTY => array(
                        array(1, \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::COLUMN_EMAIL)
                    )
                ),
            ),
            'empty email' => array(
                '$rowData' => include __DIR__ . '/Customer/_files/row_data_abstract_empty_email.php',
                '$errors' => array(
                    \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::ERROR_EMAIL_IS_EMPTY => array(
                        array(1, \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::COLUMN_EMAIL)
                    )
                ),
            ),
            'invalid email' => array(
                '$rowData' => include __DIR__ . '/Customer/_files/row_data_abstract_invalid_email.php',
                '$errors' => array(
                    \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::ERROR_INVALID_EMAIL => array(
                        array(1, \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::COLUMN_EMAIL)
                    )
                ),
            ),
            'invalid website' => array(
                '$rowData' => include __DIR__ . '/Customer/_files/row_data_abstract_invalid_website.php',
                '$errors' => array(
                    \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::ERROR_INVALID_WEBSITE => array(
                        array(1, \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::COLUMN_WEBSITE)
                    )
                ),
            ),
        );
    }

    /**
     * Test \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::_checkUniqueKey() with different values
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::_checkUniqueKey
     * @dataProvider checkUniqueKeyDataProvider
     *
     * @param array $rowData
     * @param array $errors
     * @param boolean $isValid
     */
    public function testCheckUniqueKey(array $rowData, array $errors, $isValid = false)
    {
        $checkUniqueKey = new ReflectionMethod(
            '\Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract',
            '_checkUniqueKey'
        );
        $checkUniqueKey->setAccessible(true);

        if ($isValid) {
            $this->assertTrue($checkUniqueKey->invoke($this->_model, $rowData, 0));
        } else {
            $this->assertFalse($checkUniqueKey->invoke($this->_model, $rowData, 0));
        }
        $this->assertAttributeEquals($errors, '_errors', $this->_model);
    }

    /**
     * Test for \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::validateRow for add/update action
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::validateRow
     */
    public function testValidateRowForUpdate()
    {
        // _validateRowForUpdate should be called only once
        $this->_model->expects($this->once())
            ->method('_validateRowForUpdate');

        $this->assertAttributeEquals(0, '_processedEntitiesCount', $this->_model);

        // update action
        $this->_model->setParameters(array('behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_ADD_UPDATE));
        $this->_clearValidatedRows();

        $this->assertAttributeEquals(array(), '_validatedRows', $this->_model);
        $this->assertTrue($this->_model->validateRow(array(), 1));
        $this->assertAttributeEquals(array(1 => true), '_validatedRows', $this->_model);
        $this->assertAttributeEquals(1, '_processedEntitiesCount', $this->_model);
        $this->assertTrue($this->_model->validateRow(array(), 1)); // _validateRowForUpdate should be called once
    }

    /**
     * Test for \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::validateRow for delete action
     *
     * @covers \Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract::validateRow
     */
    public function testValidateRowForDelete()
    {
        // _validateRowForDelete should be called only once
        $this->_model->expects($this->once())
            ->method('_validateRowForDelete');

        // delete action
        $this->_model->setParameters(array('behavior' => \Magento\ImportExport\Model\Import::BEHAVIOR_DELETE));
        $this->_clearValidatedRows();

        $this->assertAttributeEquals(array(), '_validatedRows', $this->_model);
        $this->assertTrue($this->_model->validateRow(array(), 2));
        $this->assertAttributeEquals(array(2 => true), '_validatedRows', $this->_model);
        $this->assertAttributeEquals(1, '_processedEntitiesCount', $this->_model);
        $this->assertTrue($this->_model->validateRow(array(), 2)); // _validateRowForDelete should be called once
    }

    /**
     * Clear validated rows array
     *
     * @return null
     */
    protected function _clearValidatedRows()
    {
        // clear array
        $validatedRows = new ReflectionProperty(
            '\Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract',
            '_validatedRows'
        );
        $validatedRows->setAccessible(true);
        $validatedRows->setValue($this->_model, array());
        $validatedRows->setAccessible(false);

        // reset counter
        $entitiesCount = new ReflectionProperty(
            '\Magento\ImportExport\Model\Import\Entity\Eav\CustomerAbstract',
            '_processedEntitiesCount'
        );
        $entitiesCount->setAccessible(true);
        $entitiesCount->setValue($this->_model, 0);
        $entitiesCount->setAccessible(false);
    }
}
