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

/**
 * Test class for Mage_Core_Model_Layout_Factory
 */
class Mage_Core_Model_Layout_FactoryTest extends PHPUnit_Framework_TestCase
{
    /*
     * Test class name
     */
    const CLASS_NAME  = 'Mage_Core_Model_Layout';

    /**
     * Test arguments
     *
     * @var array
     */
    protected $_arguments = array();

    /**
     * ObjectManager mock for tests
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * Test class instance
     *
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_model = new Mage_Core_Model_Layout_Factory($this->_objectManager);
    }

    public function testConstruct()
    {
        $this->assertAttributeInstanceOf('Magento_ObjectManager', '_objectManager', $this->_model);
    }

    public function testCreateLayoutNew()
    {
        $modelLayout = $this->getMock(self::CLASS_NAME, array(), array(), '', false);

        $this->_objectManager->expects($this->once())
            ->method('configure')
            ->with(array(self::CLASS_NAME => array('parameters' => array('someParam' => 'someVal'))));

        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with(Mage_Core_Model_Layout_Factory::CLASS_NAME)
            ->will($this->returnValue($modelLayout));

        $this->assertEquals($modelLayout, $this->_model->createLayout(array('someParam' => 'someVal')));
    }
}
