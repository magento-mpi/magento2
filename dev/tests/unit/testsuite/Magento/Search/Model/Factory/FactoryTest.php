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

/**
 * Test Class for Magento_Search_Model_Factory_Factory
 */
class Magento_Search_Model_Factory_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Search_Model_Solr_State |PHPUnit_Framework_MockObject_MockObject */
    protected $_solrStateMock;

    /** @var Magento_Search_Model_Factory_Factory|PHPUnit_Framework_MockObject_MockObject*/
    protected $_factoryObject;

    /** @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject */
    protected $_objectManager;

    /** @var Magento_Search_Model_SolrFactory|PHPUnit_Framework_MockObject_MockObject */
    protected $_solrFactoryMock;

    /** @var Magento_Search_Model_RegularFactory|PHPUnit_Framework_MockObject_MockObject */
    protected $_regularFactoryMock;

    /**
     * Set adapter mocks
     */
    public function setUp()
    {
        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')->getMock();
        $this->_solrStateMock = $this->getMock('Magento_Search_Model_Solr_State',
            array(), array(), '', false, false);
        $this->_solrFactoryMock = $this->getMock('Magento_Search_Model_SolrFactory',
            array(), array(), '', false, false);
        $this->_regularFactoryMock = $this->getMock('Magento_Search_Model_RegularFactory',
            array(), array(), '', false, false);

        $this->_factoryObject = new Magento_Search_Model_Factory_Factory($this->_objectManager, $this->_solrStateMock);
    }

    /**
     * Test if we get Solr factory on solr ext loaded
     */
    public function testGetFactorySolr()
    {
        $this->_solrStateMock->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(true));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Search_Model_SolrFactory')
            ->will($this->returnValue($this->_solrFactoryMock));
        $this->_factoryObject->getFactory();
    }

    /**
     * Test if we get regular factory on solr ext not loaded
     */
    public function testGetFactoryRegular()
    {
        $this->_solrStateMock->expects($this->once())
            ->method('isActive')
            ->will($this->returnValue(false));
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_Search_Model_RegularFactory')
            ->will($this->returnValue($this->_regularFactoryMock));
        $this->_factoryObject->getFactory();
    }
}

