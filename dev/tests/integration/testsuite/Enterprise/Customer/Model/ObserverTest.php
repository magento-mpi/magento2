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
     * List of block injection classes
     *
     * @var array
     */
    protected $_blockInjections = array(
        'Magento_Core_Model_Context',
        null,
        null
    );

    /**
     * @var Enterprise_Customer_Model_Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_observer = Mage::getModel('Enterprise_Customer_Model_Observer');
    }

    public function testSalesOrderAddressCollectionAfterLoad()
    {
        $address = Mage::getModel('Magento_Sales_Model_Order_Address');
        $address->load('admin@example.com', 'email');

        $entity = new Magento_Object(array('id' => $address->getId()));
        $collection = $this->getMock('Magento_Data_Collection_Db', array('getItems'), array(), '', false);
        $collection
            ->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(array($entity)))
        ;
        $observer = new Magento_Event_Observer(array(
            'event' => new Magento_Object(array(
                'order_address_collection' => $collection,
            ))
        ));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_observer->salesOrderAddressCollectionAfterLoad($observer);
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }

    public function testSalesOrderAddressAfterLoad()
    {
        $address = Mage::getModel('Magento_Sales_Model_Order_Address');
        $address->load('admin@example.com', 'email');
        $arguments = $this->_prepareConstructorArguments();
        $arguments[] = array('id' => $address->getId());
        $entity = $this->getMockForAbstractClass('Magento_Core_Model_Abstract', $arguments);
        $observer = new Magento_Event_Observer(array(
            'event' => new Magento_Object(array(
                'address' => $entity,
            ))
        ));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_observer->salesOrderAddressAfterLoad($observer);
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }

    /**
     * List of block constructor arguments
     *
     * @return array
     */
    protected function _prepareConstructorArguments()
    {
        $arguments = array();
        foreach ($this->_blockInjections as $injectionClass) {
            if ($injectionClass) {
                $arguments[] = Mage::getModel($injectionClass);
            } else {
                $arguments[] = null;
            }
        }
        return $arguments;
    }
}
