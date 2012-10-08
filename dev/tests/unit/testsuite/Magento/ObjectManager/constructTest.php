<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager_Zend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ObjectManager_Zend_ConstructTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for method __construct($definitionsFile, $diInstance)
     *
     * @covers Magento_ObjectManager_Zend::__construct
     *
     * @dataProvider constructDataProvider
     * @param string $definitionsFile
     * @param Zend\Di\Di $diInstance
     */
    public function testConstructWithDiObject($definitionsFile, $diInstance)
    {
        $model = new Magento_ObjectManager_Zend($definitionsFile, $diInstance);
        $this->assertAttributeInstanceOf(get_class($diInstance), '_di', $model);

    }

    /**
     * Test for method  __construct($definitionsFile, $diInstance)
     *
     * @covers Magento_ObjectManager_Zend::__construct
     *
     * @expectedException PHPUnit_Framework_Error_Warning
     * @expectedExceptionMessage The system cannot find the file specified
     */
    public function testConstructWithOutDiObject()
    {
        new Magento_ObjectManager_Zend(null, null);
    }

    /**
     * Data Provider for method __construct($definitionsFile, $diInstance)
     *
     * @covers Magento_ObjectManager_Zend::__construct
     */
    public function constructDataProvider()
    {
        $diInstance = $this->getMock('Zend\Di\Di', array('get', 'setDefinitionList', 'instanceManager'));
        $magentoConfiguration = $this->getMock('Mage_Core_Model_Config', array('loadBase'),
            array(), '', false);
        $instanceManager = $this->getMock('Zend\Di\InstanceManager', array('addSharedInstance'),
            array(), '', false);
        $diInstance->expects($this->atLeastOnce())
            ->method('instanceManager')
            ->will($this->returnValue($instanceManager));
        $diInstance->expects($this->atLeastOnce())
            ->method('get')
            ->with('Mage_Core_Model_Config')
            ->will($this->returnValue($magentoConfiguration));
        $diInstance->expects($this->atLeastOnce())
            ->method('setDefinitionList');

        return array(
            array(null, $diInstance),
            array(__DIR__ . '/_files/test_definition_file', $diInstance),
            array('test_definition_file', $diInstance)
        );
    }
}