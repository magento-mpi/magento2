<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Enterprise/Customer/_files/order_address_with_attribute.php
 */
class Enterprise_Customer_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Customer_Model_Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_observer = new Enterprise_Customer_Model_Observer;
    }

    public function testSalesOrderAddressCollectionAfterLoad()
    {
        $address = new Mage_Sales_Model_Order_Address();
        $address->load('admin@example.com', 'email');

        $entity = new Varien_Object(array('id' => $address->getId()));
        $collection = $this->getMock('Varien_Data_Collection_Db', array('getItems'));
        $collection
            ->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(array($entity)))
        ;
        $observer = new Varien_Event_Observer(array(
            'event' => new Varien_Object(array(
                'order_address_collection' => $collection,
            ))
        ));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_observer->salesOrderAddressCollectionAfterLoad($observer);
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }

    public function testSalesOrderAddressAfterLoad()
    {
        $address = new Mage_Sales_Model_Order_Address();
        $address->load('admin@example.com', 'email');

        $entity = $this->getMockForAbstractClass('Mage_Core_Model_Abstract', array(array('id' => $address->getId())));
        $observer = new Varien_Event_Observer(array(
            'event' => new Varien_Object(array(
                'address' => $entity,
            ))
        ));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_observer->salesOrderAddressAfterLoad($observer);
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }
}
