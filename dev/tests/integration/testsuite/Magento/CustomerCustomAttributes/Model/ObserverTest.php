<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Model;

/**
 * @magentoDataFixture Magento/CustomerCustomAttributes/_files/order_address_with_attribute.php
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * List of block injection classes
     *
     * @var array
     */
    protected $_blockInjections = array(
        'Magento\Core\Model\Context',
        'Magento\Core\Model\Registry',
        null,
        null
    );

    /**
     * @var \Magento\CustomerCustomAttributes\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_observer = $this->_objectManager->create('Magento\CustomerCustomAttributes\Model\Observer');
    }

    public function testSalesOrderAddressCollectionAfterLoad()
    {
        /** @var $address \Magento\Sales\Model\Order\Address */
        $address = $this->_objectManager->create('Magento\Sales\Model\Order\Address');
        $address->load('admin@example.com', 'email');

        $entity = new \Magento\Object(array('id' => $address->getId()));
        $collection = $this->getMock('Magento\Data\Collection\Db', array('getItems'), array(), '', false);
        $collection
            ->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue(array($entity)))
        ;
        $observer = new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array(
                'order_address_collection' => $collection,
            ))
        ));
        $this->assertEmpty($entity->getData('fixture_address_attribute'));
        $this->_observer->salesOrderAddressCollectionAfterLoad($observer);
        $this->assertEquals('fixture_attribute_custom_value', $entity->getData('fixture_address_attribute'));
    }

    public function testSalesOrderAddressAfterLoad()
    {
        $address = $this->_objectManager->create('Magento\Sales\Model\Order\Address');
        $address->load('admin@example.com', 'email');
        $arguments = $this->_prepareConstructorArguments();

        $arguments[] = array('id' => $address->getId());
        $entity = $this->getMockForAbstractClass('Magento\Core\Model\AbstractModel', $arguments);
        $observer = new \Magento\Event\Observer(array(
            'event' => new \Magento\Object(array(
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
                $arguments[] = \Mage::getModel($injectionClass);
            } else {
                $arguments[] = null;
            }
        }
        return $arguments;
    }
}
