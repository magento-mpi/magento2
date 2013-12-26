<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdminGws\Model\Observer
     */
    protected $_model;

    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Object
     */
    protected $_store;

    /**
     * @var \Magento\AdminGws\Model\Role
     */
    protected $_role;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_helper;

    public function setUp()
    {
        $this->_helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_store = new \Magento\Object();

        $this->_observer = $this->getMockBuilder('Magento\Event\Observer')
            ->setMethods(array('getStore'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_observer->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->_store));

        $this->_role = $this->getMockBuilder('Magento\AdminGws\Model\Role')
            ->setMethods(array('getStoreIds'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_role->expects($this->any())
            ->method('getStoreIds')
            ->will(
                $this->returnValue(
                    array(1, 2, 3, 4,5)
                )
            );
        $this->_model = $this->_helper->getObject('Magento\AdminGws\Model\Observer', array('role' => $this->_role));
    }

    public function testUpdateRoleStores()
    {
        $this->_store->setData('store_id', 1000);
        $this->assertInstanceOf('Magento\AdminGws\Model\Observer', $this->_model->updateRoleStores($this->_observer));
    }
}
