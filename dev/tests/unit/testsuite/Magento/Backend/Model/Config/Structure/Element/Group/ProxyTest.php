<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Group_ProxyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Config_Structure_Element_Group_Proxy
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = new Magento_Backend_Model_Config_Structure_Element_Group_Proxy($this->_objectManagerMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_objectManagerMock);
    }

    public function testProxyInitializesProxiedObjectOnFirstCall()
    {
        $groupMock = $this->getMock('Magento_Backend_Model_Config_Structure_Element_Group', array(), array(), '',
            false);

        $groupMock->expects($this->once())->method('setData');
        $groupMock->expects($this->once())->method('getId')->will($this->returnValue('group_id'));
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento_Backend_Model_Config_Structure_Element_Group')
            ->will($this->returnValue($groupMock));

        $this->_model->setData(array(), '');
        $this->assertEquals('group_id', $this->_model->getId());
    }
}
