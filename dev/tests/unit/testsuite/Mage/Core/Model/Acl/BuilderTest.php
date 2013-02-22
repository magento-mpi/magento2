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
    protected $_aclFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_aclMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderPoolMock;

    /**
     * @var Mage_Core_Model_Acl_Builder
     */
    protected $_model;

    protected function setUp()
    {
        $this->_aclMock = new Magento_Acl();
        $this->_aclFactoryMock = $this->getMock('Magento_AclFactory', array(), array(), '', false);
        $this->_aclFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->_aclMock));
        $this->_loaderPoolMock = $this->getMock('Mage_Core_Model_Acl_LoaderPool', array(), array(), '', false);
        $this->_model = new Mage_Core_Model_Acl_Builder($this->_aclFactoryMock, $this->_loaderPoolMock);
    }

    protected function tearDown()
    {
        unset($this->_aclMock);
        unset($this->_aclFactoryMock);
        unset($this->_loaderPoolMock);
        unset($this->_model);
    }

    public function testGetAclUsesLoadersProvidedInConfigurationToPopulateAcl()
    {
        $defaultLoaderMock = $this->getMock('Magento_Acl_Loader_Default');
        $defaultLoaderMock->expects($this->exactly(3))
            ->method('populateAcl')
            ->with($this->equalTo($this->_aclMock));
        $this->_loaderPoolMock->expects($this->once())->method('getLoadersByArea')
            ->with('someArea')
            ->will($this->returnValue(
                new ArrayIterator(array(
                    $defaultLoaderMock, $defaultLoaderMock, $defaultLoaderMock
                ))
            ));

        $this->assertEquals($this->_aclMock, $this->_model->getAcl('someArea'));
    }

    /**
     * @expectedException LogicException
     */
    public function testGetAclRethrowsException()
    {
        $this->_loaderPoolMock->expects($this->once())
            ->method('getLoadersByArea')
            ->with('someArea')
            ->will($this->throwException(new InvalidArgumentException()));
        $this->_model->getAcl('someArea');
    }
}
