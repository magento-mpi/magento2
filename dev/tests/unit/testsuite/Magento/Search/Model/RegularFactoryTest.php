<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** Test class for \Magento\Solr\Model\Client\RegularFactory */
namespace Magento\Solr\Model;

class RegularFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_clientMock;

    /** @var \Magento\Solr\Model\RegularFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $_factoryObject;

    /** @var \Magento\Solr\Model\Adapter\HttpStream */
    protected $_adapterMock;

    /** @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** Set Solr Clients mocks */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')->getMock();
        $this->_clientMock = $this->getMock('Magento\Solr\Model\Client\Solr', array(), array(), '', false, false);
        $this->_adapterMock = $this->getMock(
            'Magento\Solr\Model\Adapter\HttpStream',
            array(),
            array(),
            '',
            false,
            false
        );


        $this->_factoryObject = new \Magento\Solr\Model\RegularFactory($this->_objectManager);
    }

    /**
     * Test if we get search engine regular factory
     */
    public function testGetClient()
    {
        $options = array('attr1' => 'value1', 'attr2' => 'value2');
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Solr\Model\Client\Solr'
        )->will(
            $this->returnValue($this->_clientMock)
        );

        $this->_factoryObject->createClient($options);
    }

    /**
     * Test if we get adapter
     */
    public function testCreateAdapter()
    {
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Solr\Model\Adapter\HttpStream'
        )->will(
            $this->returnValue($this->_adapterMock)
        );
        $this->_factoryObject->createAdapter();
    }
}
