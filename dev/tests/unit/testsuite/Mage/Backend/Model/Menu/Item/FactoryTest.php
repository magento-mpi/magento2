<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_Item_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Item_Factory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperFactoryMock;

    /**
     * Constructor params
     *
     * @var array
     */
    protected $_params = array();

    public function setUp()
    {
        $this->_objectFactoryMock = $this->getMock('Magento_ObjectManager');
        $this->_helperFactoryMock = $this->getMock('Magento_Core_Model_Factory_Helper', array(), array(), '', false);
        $this->_helperFactoryMock->expects($this->any())->method('get')->will($this->returnValueMap(array(
            array('Mage_Backend_Helper_Data', 'backend_helper'),
            array('Mage_User_Helper_Data', 'user_helper')
        )));

        $this->_model = new Mage_Backend_Model_Menu_Item_Factory($this->_objectFactoryMock, $this->_helperFactoryMock);
    }

    public function testCreate()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu_Item'),
                $this->equalTo(array(
                    'helper' => 'user_helper',
                    'data' => array(
                        'title' => 'item1',
                        'dependsOnModule' => 'Mage_User_Helper_Data',
                    )
                ))
            );
        $this->_model->create(array(
            'module' => 'Mage_User_Helper_Data',
            'title' => 'item1',
            'dependsOnModule' => 'Mage_User_Helper_Data'
        ));
    }

    public function testCreateProvidesDefaultHelper()
    {
        $this->_objectFactoryMock->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu_Item'),
                $this->equalTo(array(
                    'helper' => 'backend_helper',
                    'data' => array()
                ))
        );
        $this->_model->create(array());
    }
}
