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
     * @var Mage_Core_Model_Config
     */
    protected $_factoryMock;

    /**
     * @var Mage_Core_Model_Helper[]
     */
    protected $_helpers = array();

    public function setUp()
    {
        $aclMock = $this->getMock('Mage_Backend_Model_Auth_Session', array(), array(), '', false);
        $this->_factoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_helpers = array(
            'Mage_Backend_Helper_Data' => $this->getMock('Mage_Backend_Helper_Data')
        );
        $this->_model = new Mage_Backend_Model_Menu_Item_Factory(
            array(
                'acl' => $aclMock,
                'objectFactory' => $this->_factoryMock,
                'helpers' => $this->_helpers
            )
        );
    }

    public function testCreateFromArray()
    {
        $this->_factoryMock->expects($this->once())
            ->method('getModelInstance')
            ->with(
                $this->equalTo('Mage_Backend_Model_Menu_Item'),
                $this->equalTo(array(
                    'module' => $this->_helpers['Mage_Backend_Helper_Data'],
                    'dependsOnModule' => $this->_helpers['Mage_Backend_Helper_Data']
                ))
        );
        $this->_model->createFromArray(array(
            'module' => 'Mage_Backend_Helper_Data',
            'dependsOnModule' => 'Mage_Backend_Helper_Data'
        ));
    }
}
