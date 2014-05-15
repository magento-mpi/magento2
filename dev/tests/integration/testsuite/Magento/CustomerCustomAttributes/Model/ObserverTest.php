<?php
/**
 * {license_notice}
 *
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
    protected $_blockInjections = array('Magento\Framework\Model\Context', 'Magento\Framework\Registry', null, null);

    /**
     * @var \Magento\CustomerCustomAttributes\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Framework\ObjectManager
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

        $entity = new \Magento\Framework\Object(array('id' => $address->getId()));
        $collection = $this->getMock('Magento\Framework\Data\Collection\Db', array('getItems'), array(), '', false);
        $collection->expects($this->any())->method('getItems')->will($this->returnValue(array($entity)));
        $observer = new \Magento\Framework\Event\Observer(
            array('event' => new \Magento\Framework\Object(array('order_address_collection' => $collection)))
        );
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
        $entity = $this->getMockForAbstractClass('Magento\Framework\Model\AbstractModel', $arguments);
        $observer = new \Magento\Framework\Event\Observer(array(
            'event' => new \Magento\Framework\Object(array(
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
                $arguments[] = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($injectionClass);
            } else {
                $arguments[] = null;
            }
        }
        return $arguments;
    }
}
