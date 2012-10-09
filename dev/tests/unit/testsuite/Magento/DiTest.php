<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

use \Zend\Di;

/**
 * Test case for Magento_Di
 */
class Magento_DiTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Test classes for different instantiation types
     */
    const TEST_CLASS_MODEL = 'Mage_Test_Model_Stub';
    const TEST_CLASS_BLOCK = 'Mage_Test_Block_Stub';
    const TEST_CLASS_OTHER = 'Varien_Object';
    /**#@-*/

    /**
     * Model under test
     *
     * @var Magento_Di
     */
    protected $_model;

    /**
     * Expected property types
     *
     * @var array
     */
    protected $_propertyTypes = array(
        self::TEST_CLASS_MODEL => array(
            '_eventDispatcher' => 'Mage_Core_Model_Event_Manager',
            '_cacheManager'    => 'Mage_Core_Model_Cache',
        ),
        self::TEST_CLASS_BLOCK => array(
            '_request'         => 'Mage_Core_Controller_Request_Http',
            '_layout'          => 'Mage_Core_Model_Layout',
            '_eventManager'    => 'Mage_Core_Model_Event_Manager',
            '_translator'      => 'Mage_Core_Model_Translate',
            '_cache'           => 'Mage_Core_Model_Cache',
            '_designPackage'   => 'Mage_Core_Model_Design_Package',
            '_session'         => 'Mage_Core_Model_Session',
            '_storeConfig'     => 'Mage_Core_Model_Store_Config',
            '_frontController' => 'Mage_Core_Controller_Varien_Front',
        ),
    );

    /**
     * List of expected cached classes
     *
     * @var array
     */
    protected $_cachedInstances = array(
        self::TEST_CLASS_MODEL => array(
            'eventManager' => 'Mage_Core_Model_Event_Manager',
            'cache'        => 'Mage_Core_Model_Cache',
        ),
        self::TEST_CLASS_BLOCK => array(
            'eventManager'    => 'Mage_Core_Model_Event_Manager',
            'cache'           => 'Mage_Core_Model_Cache',
            'request'         => 'Mage_Core_Controller_Request_Http',
            'layout'          => 'Mage_Core_Model_Layout',
            'translate'       => 'Mage_Core_Model_Translate',
            'design'          => 'Mage_Core_Model_Design_Package',
            'session'         => 'Mage_Core_Model_Session',
            'storeConfig'     => 'Mage_Core_Model_Store_Config',
            'frontController' =>'Mage_Core_Controller_Varien_Front',
        ),
    );

    /**
     * List of shared instances
     *
     * @var array
     */
    protected $_sharedInstances = array();

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Data provider for testNewInstanceWithoutDefinitions
     *
     * @return array
     */
    public function newInstanceWithoutDefinitionsDataProvider()
    {
        return array(
            'shared model instance with arguments' => array(
                '$className' => self::TEST_CLASS_MODEL,
                '$arguments' => array(1 => 'value_1'),
                '$isShared'  => true,
            ),
            'shared model instance without arguments' => array(
                '$className' => self::TEST_CLASS_MODEL,
                '$arguments' => array(),
                '$isShared'  => true,
            ),
            'not shared model instance' => array(
                '$className' => self::TEST_CLASS_MODEL,
                '$arguments' => array(),
                '$isShared'  => false,
            ),
            'not shared block instance' => array(
                '$className' => self::TEST_CLASS_BLOCK,
                '$arguments' => array(),
                '$isShared'  => false,
            ),
            'not shared other class instance' => array(
                '$className' => self::TEST_CLASS_OTHER,
                '$arguments' => array(),
                '$isShared'  => false,
            ),
        );
    }

    /**
     * @param string $className
     * @param array $arguments
     * @param bool $isShared
     *
     * @dataProvider newInstanceWithoutDefinitionsDataProvider
     */
    public function testNewInstanceWithoutDefinitions($className, array $arguments = array(), $isShared = true)
    {
        // assert object instantiation
        $this->_prepareMockForNewInstanceWithoutDefinitions($className, $arguments, $isShared);
        $testObject = $this->_model->newInstance($className, $arguments, $isShared);
        switch ($className) {
            case self::TEST_CLASS_MODEL:
                $this->_assertTestModel($testObject, $arguments);
                break;

            case self::TEST_CLASS_BLOCK:
                $this->_assertTestBlock($testObject, $arguments);
                break;

            case self::TEST_CLASS_OTHER:
            default:
                $this->assertInstanceOf($className, $testObject);
                break;
        }
        $this->assertAttributeEmpty('instanceContext', $this->_model);

        // assert cache
        if (isset($this->_cachedInstances[$className])) {
            $expectedCache = array();
            foreach ($this->_cachedInstances[$className] as $key => $class) {
                $this->assertArrayHasKey($class, $this->_sharedInstances);
                $expectedCache[$key] = $this->_sharedInstances[$class];
            }
            $this->assertAttributeEquals($expectedCache, '_cachedInstances', $this->_model);
        }
    }

    /**
     * Prepares all mocks for testNewInstanceWithoutDefinitions
     *
     * @param string $className
     * @param bool $isShared
     * @param array $arguments
     */
    protected function _prepareMockForNewInstanceWithoutDefinitions(
        $className, array $arguments = array(), $isShared = true
    ) {
        $definitions = $this->getMock('Zend\Di\DefinitionList', array('hasClass'), array(), '', false);
        $definitions->expects($this->once())
            ->method('hasClass')
            ->will($this->returnValue(false));

        $instanceManager = $this->getMock(
            'Zend\Di\InstanceManager',
            array('hasSharedInstance', 'getSharedInstance', 'addSharedInstanceWithParameters', 'addSharedInstance')
        );
        $instanceManager->expects($this->any())
            ->method('hasSharedInstance')
            ->will($this->returnValue(true));
        $instanceManager->expects($this->any())
            ->method('getSharedInstance')
            ->will($this->returnCallback(array($this, 'callbackGetSharedInstance')));

        if ($isShared) {
            if ($arguments) {
                $instanceManager->expects($this->once())
                    ->method('addSharedInstanceWithParameters')
                    ->with($this->isInstanceOf($className), $className, $arguments);
                $instanceManager->expects($this->never())
                    ->method('addSharedInstance');
            } else {
                $instanceManager->expects($this->never())
                    ->method('addSharedInstanceWithParameters');
                $instanceManager->expects($this->once())
                    ->method('addSharedInstance')
                    ->with($this->isInstanceOf($className), $className);
            }
        } else {
            $instanceManager->expects($this->never())
                ->method('addSharedInstanceWithParameters');
            $instanceManager->expects($this->never())
                ->method('addSharedInstance');
        }

        $this->_model = new Magento_Di($definitions, $instanceManager);
    }

    /**
     * Invokes when DI class calls $this->get('<class_name>')
     *
     * @param $classOrAlias
     * @return PHPUnit_Framework_MockObject_MockObject|object
     */
    public function callbackGetSharedInstance($classOrAlias)
    {
        $this->_sharedInstances[$classOrAlias] = $mock = $this->getMock($classOrAlias, array(), array(), '', false);
        return $this->_sharedInstances[$classOrAlias];
    }

    /**
     * Assert test model object
     *
     * @param object $modelInstance
     * @param array $arguments
     */
    protected function _assertTestModel($modelInstance, array $arguments = array())
    {
        $this->assertInstanceOf(self::TEST_CLASS_MODEL, $modelInstance);

        foreach ($this->_propertyTypes[self::TEST_CLASS_MODEL] as $propertyName => $propertyClass) {
            $this->assertAttributeInstanceOf($propertyClass, $propertyName, $modelInstance);
        }
        $this->assertAttributeSame(null, '_resource', $modelInstance);
        $this->assertAttributeSame(null, '_resourceCollection', $modelInstance);
        $this->assertAttributeSame($arguments, '_data', $modelInstance);
    }

    /**
     * Assert test block object
     *
     * @param object $blockInstance
     * @param array $arguments
     */
    protected function _assertTestBlock($blockInstance, array $arguments = array())
    {
        $this->assertInstanceOf(self::TEST_CLASS_BLOCK, $blockInstance);

        foreach ($this->_propertyTypes[self::TEST_CLASS_BLOCK] as $propertyName => $propertyClass) {
            $this->assertAttributeInstanceOf($propertyClass, $propertyName, $blockInstance);
        }
        $this->assertAttributeSame($arguments, '_data', $blockInstance);
    }

    // @codingStandardsIgnoreStart
    /**
     * @expectedException Zend\Di\Exception\ClassNotFoundException
     * @expectedExceptionMessage Class (specified by alias Mage_Test_Model_Stub) Mage_Test_Model_Stub_Other could not be located in provided definitions.
     */
    // @codingStandardsIgnoreEnd
    public function testNewInstanceNoDefinitionException()
    {
        $this->_prepareMockForNewInstanceExceptions(true);
        $this->_model->newInstance(self::TEST_CLASS_MODEL);
    }

    /**
     * @expectedException Zend\Di\Exception\RuntimeException
     */
    public function testNewInstanceInvalidInstantiatorArray()
    {
        $this->_prepareMockForNewInstanceExceptions(false, array(self::TEST_CLASS_MODEL, 'testMethod'));
        $this->_model->newInstance(self::TEST_CLASS_MODEL);
    }

    /**
     * @expectedException Zend\Di\Exception\RuntimeException
     * @expectedExceptionMessage Invalid instantiator of type "string" for "Mage_Test_Model_Stub".
     */
    public function testNewInstanceInvalidInstantiatorNotArray()
    {
        $this->_prepareMockForNewInstanceExceptions(false, 'test string');
        $this->_model->newInstance(self::TEST_CLASS_MODEL);
    }

    /**
     * Prepares all mocks for tests with exceptions
     */
    protected function _prepareMockForNewInstanceExceptions($noDefinition = false, $invalidInstantiator = null)
    {
        $definitions = $this->getMock(
            'Zend\Di\DefinitionList', array('hasClass', 'getInstantiator'), array(), '', false
        );
        if ($noDefinition) {
            $definitions->expects($this->exactly(2))
                ->method('hasClass')
                ->will($this->returnCallback(array($this, 'callbackHasClassForExceptions')));
        } elseif ($invalidInstantiator) {
            $definitions->expects($this->exactly(2))
                ->method('hasClass')
                ->will($this->returnValue(true));
            $definitions->expects($this->once())
                ->method('getInstantiator')
                ->will($this->returnValue($invalidInstantiator));
        }

        $instanceManager = $this->getMock('Zend\Di\InstanceManager', array('hasAlias', 'getClassFromAlias'));
        $instanceManager->expects($this->once())
            ->method('hasAlias')
            ->will($this->returnValue($noDefinition));
        if ($noDefinition) {
            $instanceManager->expects($this->once())
                ->method('getClassFromAlias')
                ->will($this->returnValue(self::TEST_CLASS_MODEL . '_Other'));
        }

        $this->_model = new Magento_Di($definitions, $instanceManager);
    }

    /**
     * @param string $className
     * @return bool
     */
    public function callbackHasClassForExceptions($className)
    {
        return $className == self::TEST_CLASS_MODEL;
    }

    /**
     * Data provider for testNewInstanceWithDefinitionsWithoutResolve
     *
     * @return array
     */
    public function newInstanceWithDefinitionsWithoutResolveDataProvider()
    {
        $testClassName = self::TEST_CLASS_OTHER;

        return array(
            'shared with arguments' => array(
                '$instantiator' => '__construct',
                '$className'    => $testClassName,
                '$arguments'    => array(2 => 'test string'),
                '$isShared'     => true,
            ),
            'shared without arguments' => array(
                '$instantiator' => '__construct',
                '$className'    => $testClassName,
                '$arguments'    => array(),
                '$isShared'     => true,
            ),
            'not shared' => array(
                '$instantiator' => '__construct',
                '$className'    => $testClassName,
                '$arguments'    => array(),
                '$isShared'     => false,
            ),
            'not shared callback' => array(
                '$instantiator' => array(new $testClassName(), 'setOrigData'), // setOrigData returns object itself
                '$className'    => $testClassName,
                '$arguments'    => array(),
                '$isShared'     => false,
            ),
        );
    }

    /**
     * @param string|array $instantiator
     * @param string $className
     * @param array $arguments
     * @param bool $isShared
     *
     * @dataProvider newInstanceWithDefinitionsWithoutResolveDataProvider
     */
    public function testNewInstanceWithDefinitionsWithoutResolve(
        $instantiator, $className, array $arguments = array(), $isShared = true
    ) {
        $this->_prepareMockForNewInstanceWithDefinitionsWithoutResolve(
            $instantiator, $className, $arguments, $isShared
        );

        $testObject = $this->_model->newInstance($className, $arguments, $isShared);
        $this->assertInstanceOf($className, $testObject);
    }

    /**
     * Prepares all mocks for testNewInstanceWithDefinitionsWithoutResolve
     *
     * @param string|array $instantiator
     * @param string $className
     * @param array $arguments
     * @param bool $isShared
     */
    protected function _prepareMockForNewInstanceWithDefinitionsWithoutResolve(
        $instantiator, $className, array $arguments = array(), $isShared = true
    ) {
        $definitions = $this->getMock(
            'Zend\Di\DefinitionList', array('hasClass', 'getInstantiator', 'hasMethodParameters', 'hasMethod'),
            array(), '', false
        );
        $definitions->expects($this->exactly(2))
            ->method('hasClass')
            ->will($this->returnValue(true));
        $definitions->expects($this->once())
            ->method('getInstantiator')
            ->will($this->returnValue($instantiator));

        if (is_array($instantiator)) {
            $definitions->expects($this->never())
                ->method('hasMethodParameters');
            $definitions->expects($this->once())
                ->method('hasMethod')
                ->with(get_class($instantiator[0]), $instantiator[1])
                ->will($this->returnValue(false));
        } else {
            $definitions->expects($this->once())
                ->method('hasMethodParameters')
                ->will($this->returnValue(false));
        }

        $instanceManager = $this->getMock(
            'Zend\Di\InstanceManager', array('hasAlias', 'addSharedInstanceWithParameters', 'addSharedInstance')
        );
        $instanceManager->expects($this->any())
            ->method('hasAlias')
            ->will($this->returnValue(false));

        if ($isShared) {
            if ($arguments) {
                $instanceManager->expects($this->once())
                    ->method('addSharedInstanceWithParameters')
                    ->with($this->isInstanceOf($className), $className, $arguments);
                $instanceManager->expects($this->never())
                    ->method('addSharedInstance');
            } else {
                $instanceManager->expects($this->never())
                    ->method('addSharedInstanceWithParameters');
                $instanceManager->expects($this->once())
                    ->method('addSharedInstance')
                    ->with($this->isInstanceOf($className), $className);
            }
        } else {
            $instanceManager->expects($this->never())
                ->method('addSharedInstanceWithParameters');
            $instanceManager->expects($this->never())
                ->method('addSharedInstance');
        }

        $this->_model = new Magento_Di($definitions, $instanceManager);
    }
}

/**
 * Stub model class to test Magento_Di::newInstance
 */
class Mage_Test_Model_Stub extends Mage_Core_Model_Abstract
{
    /**
     * Constructor $data property value
     *
     * @var array
     */
    protected $_data;

    /**
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Mage_Core_Model_Cache $cacheManager
     * @param array $data
     * @param Mage_Core_Model_Resource_Abstract $resource
     * @param Varien_Data_Collection_Db $resourceCollection
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Mage_Core_Model_Cache $cacheManager,
        Mage_Core_Model_Resource_Abstract $resource = null,
        Varien_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($eventDispatcher, $cacheManager, $resource, $resourceCollection);
        $this->_data = $data;
    }
}

/**
 * Stub block class to test Magento_Di::newInstance
 */
class Mage_Test_Block_Stub extends Mage_Core_Block_Abstract
{
    /**
     * Constructor $data property value
     *
     * @var array
     */
    protected $_data;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        array $data = array()
    ) {
        parent::__construct(
            $request,
            $layout,
            $eventManager,
            $translator,
            $cache,
            $designPackage,
            $session,
            $storeConfig,
            $frontController
        );
        $this->_data = $data;
    }
}
