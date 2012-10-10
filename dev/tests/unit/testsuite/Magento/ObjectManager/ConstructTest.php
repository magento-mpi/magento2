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

/**
 * Test class for Magento_ObjectManager_Zend
 */
class Magento_ObjectManager_Zend_ConstructTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_magentoConfig;

    /**
     * @var Zend\Di\InstanceManager
     */
    protected $_instanceManager;

    /**
     * @var Zend\Di\Di
     */
    protected $_diInstance;

    /**
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
     * Data Provider for method __construct($definitionsFile, $diInstance)
     */
    public function constructDataProvider()
    {
        $this->_diInstance = $this->getMock('Zend\Di\Di', array('get', 'setDefinitionList', 'instanceManager'));
        $this->_magentoConfig = $this->getMock('Mage_Core_Model_Config', array('loadBase'),
            array(), '', false
        );
        $this->_instanceManager = $this->getMock('Zend\Di\InstanceManager', array('addSharedInstance'),
            array(), '', false
        );
        $this->_diInstance->expects($this->exactly(3))
            ->method('instanceManager')
            ->will($this->returnValue($this->_instanceManager));
        $this->_diInstance->expects($this->exactly(6))
            ->method('get')
            ->with('Mage_Core_Model_Config')
            ->will($this->returnCallback(array($this, 'getCallback')));
        $this->_diInstance->expects($this->exactly(4))
            ->method('setDefinitionList')
            ->will($this->returnCallback(array($this, 'setDefinitionListCallback')));
        $this->_instanceManager->expects($this->exactly(3))
            ->method('addSharedInstance')
            ->will($this->returnCallback(array($this, 'addSharedInstanceCallback')));

        return array(
            'without definition file and with specific Di instance' => array(
                null, $this->_diInstance
            ),
            'with definition file and with specific Di instance' => array(
                __DIR__ . '/_files/test_definition_file', $this->_diInstance
            ),
            'with missing definition file and with specific Di instance' => array(
                'test_definition_file', $this->_diInstance
            )
        );
    }

    /**
     * Callback to use instead Di::setDefinitionList
     *
     * @param Zend\Di\DefinitionList $definitions
     */
    public function setDefinitionListCallback(Zend\Di\DefinitionList $definitions)
    {
        $this->assertInstanceOf('Zend\Di\DefinitionList', $definitions);
    }

    /**
     * Callback to use instead Di::get
     *
     * @param string $className
     * @param array $arguments
     * @return Mage_Core_Model_Config
     */
    public function getCallback($className, array $arguments = array())
    {
        $this->assertEquals('Mage_Core_Model_Config', $className);
        $this->assertEmpty($arguments);
        return $this->_magentoConfig;
    }

    /**
     * Callback to use instead InstanceManager::addSharedInstance
     *
     * @param object $instance
     * @param string $classOrAlias
     */
    public function addSharedInstanceCallback($instance, $classOrAlias)
    {
        $this->assertInstanceOf('Magento_ObjectManager_Zend', $instance);
        $this->assertEquals('Magento_ObjectManager', $classOrAlias);
    }
}