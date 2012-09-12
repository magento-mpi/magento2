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
    }

    protected function _createModel($config)
    {
        return new Mage_Core_Model_Acl_Builder(array(
            'objectFactory' => $this->_objectFactoryMock,
            'areaConfig' => $config
        ));
    }

    public function testGetAclUsesDefaultLoadersWhenNothingSetInConfiguration()
    {
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
        $model = $this->_createModel(array(
            'acl' => array(
                'resourceLoader' => null,
                'ruleLoader' => null,
                'roleLoader' => null,
            )
        ));

        $this->assertEquals($this->_aclMock, $model->getAcl());
    }

    public function testGetAclUsesLoadersProvidedInconfigurationToPopulateAcl()
    {
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

        $model = $this->_createModel(array(
            'acl' => array(
                'resourceLoader' => 'test1',
                'roleLoader' => 'test2',
                'ruleLoader' => 'test3',
            )
        ));

        $this->assertEquals($this->_aclMock, $model->getAcl());
    }

    /**
     * @expectedException LogicException
     */
    public function testGetAclRethrowsException()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('getModelInstance')
            ->will($this->throwException(new InvalidArgumentException()));
        $model = $this->_createModel(array(
            'acl' => array(
                'resourceLoader' => 'default',
                'roleLoader' => 'default',
                'ruleLoader' => 'default',
            )
        ));
        $model->getAcl();
    }
}
