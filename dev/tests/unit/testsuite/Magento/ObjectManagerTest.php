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
class Magento_ObjectManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Area code
     */
    const AREA_CODE = 'global';

    /**
     * Class name
     */
    const CLASS_NAME = 'TestClassName';

    /**#@+
     * Objects for create and get method
     */
    const OBJECT_CREATE = 'TestObjectCreate';
    const OBJECT_GET = 'TestObjectGet';
    /**#@-*/

    /**
     * Arguments
     *
     * @var array
     */
    protected $_arguments = array(
        'argument_1' => 'value_1',
        'argument_2' => 'value_2',
    );

    /**
     * ObjectManager instance for tests
     *
     * @var Magento_ObjectManager_Zend
     */
    protected $_objectManager;

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

    public function testLoadAreaConfiguration()
    {
        $this->_prepareObjectManagerForLoadAreaConfigurationTests();
        $this->_objectManager->loadAreaConfiguration(self::AREA_CODE);
    }

    public function testCreate()
    {
        $this->_prepareObjectManagerForGetTests(true);
        $actualObject = $this->_objectManager->create(self::CLASS_NAME, $this->_arguments);
        $this->assertEquals(self::OBJECT_CREATE, $actualObject);
    }

    public function testGet()
    {
        $this->_prepareObjectManagerForGetTests();
        $actualObject = $this->_objectManager->get(self::CLASS_NAME, $this->_arguments);
        $this->assertEquals(self::OBJECT_GET, $actualObject);
    }

    /**
     * Create Magento_ObjectManager_Zend instance for testLoadAreaConfiguration
     */
    protected function _prepareObjectManagerForLoadAreaConfigurationTests()
    {
        unset($this->_objectManager);
        /** @var $modelConfigMock Mage_Core_Model_Config */
        $this->_magentoConfig = $this->getMock('Mage_Core_Model_Config', array('getNode', 'loadBase'), array(), '', false);
        $this->_magentoConfig->expects($this->exactly(2))
            ->method('getNode')
            ->will($this->returnCallback(
            array($this, 'getNodeCallback')
        ));

        /** @var $instanceManagerMock Zend\Di\InstanceManager */
        $this->_instanceManager = $this->getMock('Zend\Di\InstanceManager', array('addSharedInstance', 'addAlias'),
            array(), '', false
        );
        $this->_instanceManager->expects($this->exactly(2))
            ->method('addAlias');

        /** @var $diMock Zend\Di\Di */
        $this->_diInstance = $this->getMock('Zend\Di\Di', array('instanceManager', 'get'), array(), '', false);
        $this->_diInstance->expects($this->exactly(3))
            ->method('instanceManager')
            ->will($this->returnValue($this->_instanceManager));
        $this->_diInstance->expects($this->exactly(3))
            ->method('get')
            ->will($this->returnValue($this->_magentoConfig));

        $this->_objectManager = new Magento_ObjectManager_Zend(null, $this->_diInstance);
    }

    /**
     * Create Magento_ObjectManager_Zend instance
     *
     * @param bool $mockNewInstance
     */
    protected function _prepareObjectManagerForGetTests($mockNewInstance = false)
    {
        unset($this->_objectManager);
        $this->_magentoConfig = $this->getMock('Mage_Core_Model_Config', array('loadBase'), array(), '', false);
        $this->_magentoConfig->expects($this->any())
            ->method('loadBase')
            ->will($this->returnSelf());

        $this->_instanceManager = $this->getMock('Zend\Di\InstanceManager', array('addSharedInstance'), array(), '', false);
        $this->_diInstance = $this->getMock('Zend\Di\Di', array('instanceManager', 'newInstance', 'get', 'setDefinitionList'));
        $this->_diInstance->expects($this->any())
            ->method('instanceManager')
            ->will($this->returnValue($this->_instanceManager));
        if ($mockNewInstance) {
            $this->_diInstance->expects($this->once())
                ->method('newInstance')
                ->will($this->returnCallback(array($this, 'verifyCreate')));
            $this->_diInstance->expects($this->exactly(2))
                ->method('get')
                ->will($this->returnCallback(array($this, 'verifyGet')));
        } else {
            $this->_diInstance->expects($this->exactly(3))
                ->method('get')
                ->will($this->returnCallback(array($this, 'verifyGet')));
        }

        $this->_objectManager = new Magento_ObjectManager_Zend(null, $this->_diInstance);
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
            ->will($this->returnCallback(array($this, 'verifySetDefinitionListCallback')));
        $this->_instanceManager->expects($this->exactly(3))
            ->method('addSharedInstance')
            ->will($this->returnCallback(array($this, 'verifyAddSharedInstanceCallback')));

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
    public function verifySetDefinitionListCallback(Zend\Di\DefinitionList $definitions)
    {
        $this->assertInstanceOf('Zend\Di\DefinitionList', $definitions);
    }

    /**
     * Callback to use instead InstanceManager::addSharedInstance
     *
     * @param object $instance
     * @param string $classOrAlias
     */
    public function verifyAddSharedInstanceCallback($instance, $classOrAlias)
    {
        $this->assertInstanceOf('Magento_ObjectManager_Zend', $instance);
        $this->assertEquals('Magento_ObjectManager', $classOrAlias);
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
     * Check passed param and retrieve mock of node object
     *
     * @param string $path
     * @return Varien_Object|PHPUnit_Framework_MockObject_MockObject
     */
    public function getNodeCallback($path)
    {
        $this->assertEquals(self::AREA_CODE . '/' . Magento_ObjectManager_Zend::CONFIGURATION_DI_NODE, $path);

        $nodeMock = $this->getMock('Varien_Object', array('asArray'), array(), '', false);
        $nodeMock->expects($this->once())
            ->method('asArray')
            ->will($this->returnValue(
            array(
                'alias' => array(1)
            )
        ));

        return $nodeMock;
    }

    /**
     * Callback method for Zend\Di\Di::newInstance
     *
     * @param string $className
     * @param array $arguments
     * @return string
     */
    public function verifyCreate($className, array $arguments = array())
    {
        $this->assertEquals(self::CLASS_NAME, $className);
        $this->assertEquals($this->_arguments, $arguments);

        return self::OBJECT_CREATE;
    }

    /**
     * Callback method for Zend\Di\Di::get
     *
     * @param string $className
     * @param array $arguments
     * @return string|Mage_Core_Model_Config
     */
    public function verifyGet($className, array $arguments = array())
    {
        if ($className == 'Mage_Core_Model_Config') {
            return $this->_magentoConfig;
        }

        $this->assertEquals(self::CLASS_NAME, $className);
        $this->assertEquals($this->_arguments, $arguments);

        return self::OBJECT_GET;
    }
}
