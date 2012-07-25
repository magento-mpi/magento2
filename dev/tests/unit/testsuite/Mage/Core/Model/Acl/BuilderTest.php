<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Acl_BuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Acl_Builder
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var stdClass
     */
    protected $_areaConfigMock;

    public function setUp()
    {
        $this->_aclMock = $this->getMock('Magento_Acl');
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_objectFactoryMock->expects($this->at(0))
            ->method('getModelInstance')
            ->with($this->equalTo('Magento_Acl'))
            ->will($this->returnValue($this->_aclMock));
        $this->_areaConfigMock = new StdClass();
        $this->_model = new Mage_Core_Model_Acl_Builder(array(
            'objectFactory' => $this->_objectFactoryMock,
            'areaConfig' => $this->_areaConfigMock
        ));
    }

    public function testGetAclUsesDefaultLoadersWhenNothingSetInConfiguration()
    {
        $this->_areaConfigMock->resourceLoader = null;
        $this->_areaConfigMock->ruleLoader = null;
        $this->_areaConfigMock->roleLoader = null;
        $defaultLoaderMock = $this->getMock('Magento_Acl_Loader_Default');
        $defaultLoaderMock->expects($this->exactly(3))
            ->method('populateAcl')
            ->with($this->equalTo($this->_aclMock));

        $this->_objectFactoryMock->expects($this->at(1))
            ->method('getModelInstance')
            ->with($this->equalTo('Magento_Acl_Loader_Default'))
            ->will($this->returnValue($defaultLoaderMock));
        $this->_objectFactoryMock->expects($this->at(2))
            ->method('getModelInstance')
            ->with($this->equalTo('Magento_Acl_Loader_Default'))
            ->will($this->returnValue($defaultLoaderMock));
        $this->_objectFactoryMock->expects($this->at(3))
            ->method('getModelInstance')
            ->with($this->equalTo('Magento_Acl_Loader_Default'))
            ->will($this->returnValue($defaultLoaderMock));

        $this->_model->getAcl();
    }

    public function testGetAclUsesLoadersProvidedInconfigurationToPopulateAcl()
    {
        $this->_areaConfigMock->resourceLoader = 'test1';
        $this->_areaConfigMock->roleLoader = 'test2';
        $this->_areaConfigMock->ruleLoader = 'test3';
        $defaultLoaderMock = $this->getMock('Magento_Acl_Loader_Default');
        $defaultLoaderMock->expects($this->exactly(3))
            ->method('populateAcl')
            ->with($this->equalTo($this->_aclMock));

        $this->_objectFactoryMock->expects($this->at(1))
            ->method('getModelInstance')
            ->with($this->equalTo('test1'))
            ->will($this->returnValue($defaultLoaderMock));
        $this->_objectFactoryMock->expects($this->at(2))
            ->method('getModelInstance')
            ->with($this->equalTo('test2'))
            ->will($this->returnValue($defaultLoaderMock));
        $this->_objectFactoryMock->expects($this->at(3))
            ->method('getModelInstance')
            ->with($this->equalTo('test3'))
            ->will($this->returnValue($defaultLoaderMock));

        $this->_model->getAcl();
    }
}
