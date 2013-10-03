<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Test class for \Magento\Search\Model\Client\RegularFactory */
namespace Magento\Search\Model;

class RegularFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_clientMock;

    /** @var \Magento\Search\Model\RegularFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_factoryObject;

    /** @var \Magento\Search\Model\Adapter\HttpStream */
    protected $_adapterMock;

    /** @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** Set Solr Clients mocks */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')->getMock();
        $this->_clientMock = $this->getMock('Magento\Search\Model\Client\Solr',
            array(), array(), '', false, false);
        $this->_adapterMock = $this->getMock('Magento\Search\Model\Adapter\HttpStream',
            array(), array(), '', false, false);


        $this->_factoryObject = new \Magento\Search\Model\RegularFactory(
            $this->_objectManager
        );
    }

    /**
     * Test if we get search engine regular factory
     */
    public function testGetClient()
    {
        $options = array('attr1' => 'value1', 'attr2' => 'value2');
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Search\Model\Client\Solr')
            ->will($this->returnValue($this->_clientMock));

        $this->_factoryObject->createClient($options);
    }

    /**
     * Test if we get adapter
     */
    public function testCreateAdapter()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Search\Model\Adapter\HttpStream')
            ->will($this->returnValue($this->_adapterMock));
        $this->_factoryObject->createAdapter();
    }
}
