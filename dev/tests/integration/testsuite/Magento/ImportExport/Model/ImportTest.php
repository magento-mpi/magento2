<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Magento/ImportExport/_files/import_data.php
 */
class Magento_ImportExport_Model_ImportTest extends PHPUnit_Framework_TestCase
{
    /**
     * Model object which is used for tests
     *
     * @var \Magento\ImportExport\Model\Import
     */
    protected $_model;

    /**
     * Expected entity behaviors
     *
     * @var array
     */
    protected $_entityBehaviors = array(
        'catalog_product' => array(
            'token' => 'Magento\ImportExport\Model\Source\Import\Behavior\Basic',
            'code'  => 'basic_behavior',
        ),
        'customer_composite' => array(
            'token' => 'Magento\ImportExport\Model\Source\Import\Behavior\Basic',
            'code'  => 'basic_behavior',
        ),
        'customer' => array(
            'token' => 'Magento\ImportExport\Model\Source\Import\Behavior\Custom',
            'code'  => 'custom_behavior',
        ),
        'customer_address' => array(
            'token' => 'Magento\ImportExport\Model\Source\Import\Behavior\Custom',
            'code'  => 'custom_behavior',
        ),
    );

    /**
     * Expected unique behaviors
     *
     * @var array
     */
    protected $_uniqueBehaviors = array(
        'basic_behavior'  => 'Magento\ImportExport\Model\Source\Import\Behavior\Basic',
        'custom_behavior' => 'Magento\ImportExport\Model\Source\Import\Behavior\Custom',
    );

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento\ImportExport\Model\Import');
    }

    /**
     * @covers \Magento\ImportExport\Model\Import::_getEntityAdapter
     */
    public function testImportSource()
    {
        /** @var $customersCollection \Magento\Customer\Model\Resource\Customer\Collection */
        $customersCollection = Mage::getResourceModel('Magento\Customer\Model\Resource\Customer\Collection');

        $existCustomersCount = count($customersCollection->load());

        $customersCollection->resetData();
        $customersCollection->clear();

        $this->_model->importSource();

        $customers = $customersCollection->getItems();

        $addedCustomers = count($customers) - $existCustomersCount;

        $this->assertGreaterThan($existCustomersCount, $addedCustomers);
    }

    public function testValidateSource()
    {
        $this->_model->setEntity('catalog_product');
        /** @var \Magento\ImportExport\Model\Import\SourceAbstract|PHPUnit_Framework_MockObject_MockObject $source */
        $source = $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\SourceAbstract', array(
            array('sku', 'name')
        ));
        $source->expects($this->any())->method('_getNextRow')->will($this->returnValue(false));
        $this->assertTrue($this->_model->validateSource($source));
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Entity is unknown
     */
    public function testValidateSourceException()
    {
        $source = $this->getMockForAbstractClass('Magento\ImportExport\Model\Import\SourceAbstract',
            array(), '', false);
        $this->_model->validateSource($source);
    }

    public function testGetEntity()
    {
        $entityName = 'entity_name';
        $this->_model->setEntity($entityName);
        $this->assertSame($entityName, $this->_model->getEntity());
    }

    /**
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Entity is unknown
     */
    public function testGetEntityEntityIsNotSet()
    {
        $this->_model->getEntity();
    }

    /**
     * Test getEntityBehaviors with all required data
     * Can't check array on equality because this test should be useful for CE
     *
     * @covers \Magento\ImportExport\Model\Import::getEntityBehaviors
     */
    public function testGetEntityBehaviors()
    {
        $importModel = $this->_model;
        $actualBehaviors = $importModel::getEntityBehaviors();

        foreach ($this->_entityBehaviors as $entityKey => $behaviorData) {
            $this->assertArrayHasKey($entityKey, $actualBehaviors);
            $this->assertEquals($behaviorData, $actualBehaviors[$entityKey]);
        }
    }

    /**
     * Test getEntityBehaviors with not existing behavior class
     *
     * @magentoConfigFixture global/importexport/import_entities/customer/behavior_token Unknown_Behavior_Class
     *
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Invalid behavior token for customer
     */
    public function testGetEntityBehaviorsWithUnknownBehavior()
    {
        $importModel = $this->_model;
        $actualBehaviors = $importModel::getEntityBehaviors();
        $this->assertArrayNotHasKey('customer', $actualBehaviors);
    }

    /**
     * Test getUniqueEntityBehaviors with all required data
     * Can't check array on equality because this test should be useful for CE
     *
     * @covers \Magento\ImportExport\Model\Import::getUniqueEntityBehaviors
     */
    public function testGetUniqueEntityBehaviors()
    {
        $importModel = $this->_model;
        $actualBehaviors = $importModel::getUniqueEntityBehaviors();

        foreach ($this->_uniqueBehaviors as $behaviorCode => $behaviorClass) {
            $this->assertArrayHasKey($behaviorCode, $actualBehaviors);
            $this->assertEquals($behaviorClass, $actualBehaviors[$behaviorCode]);
        }
    }
}
