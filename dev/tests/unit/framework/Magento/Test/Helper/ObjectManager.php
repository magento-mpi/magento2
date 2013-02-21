<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class for basic object retrieving, such as blocks, models etc...
 */
class Magento_Test_Helper_ObjectManager
{
    /**
     * Special cases configuration
     *
     * @var array
     */
    protected $_specialCases = array(
        'Mage_Core_Model_Resource_Abstract' => '_getResourceModelMock',
        'Mage_Core_Model_Translate' => '_getTranslatorMock',
    );

    /**
     * Test object
     *
     * @var PHPUnit_Framework_TestCase
     */
    protected $_testObject;

    /**
     * Class constructor
     *
     * @param PHPUnit_Framework_TestCase $testObject
     */
    public function __construct(PHPUnit_Framework_TestCase $testObject)
    {
        $this->_testObject = $testObject;
    }

    /**
     * Get mock for each argument
     *
     * @param array $constructArguments
     * @param array $arguments
     * @return array
     */
    protected function _createArgumentsMock($constructArguments, $arguments)
    {
        foreach ($constructArguments as $name => $argument) {
            if (is_array($argument) && isset($argument['isAutoTestValue'])) {
                if ($argument['argumentClassName']) {
                    $object = $this->_processSpecialCases($argument['argumentClassName'], $arguments);
                    if (null !== $object) {
                        $constructArguments[$name] = $object;
                    } else {
                        $constructArguments[$name] = $this->_getMockWithoutConstructorCall(
                            $argument['argumentClassName']
                        );
                    }
                } else {
                    $constructArguments[$name] = $argument['defaultValue'];
                }
            }
        }
        return $constructArguments;
    }

    /**
     * Process special cases
     *
     * @param string $className
     * @param array $arguments
     * @return null|object
     */
    protected function _processSpecialCases($className, $arguments)
    {
        $object = null;
        $interfaces = class_implements($className);

        if (in_array('Magento_ObjectManager_ContextInterface', $interfaces)) {
            $object = $this->getObject($className, $arguments);
        } elseif (isset($this->_specialCases[$className])) {
            $method = $this->_specialCases[$className];
            $object = $this->$method($className);
        }

        return $object;
    }

    /**
     * Retrieve specific mock of core resource model
     *
     * @return Mage_Core_Model_Resource_Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getResourceModelMock()
    {
        $resourceMock = $this->_testObject->getMock('Mage_Core_Model_Resource_Resource', array('getIdFieldName'),
            array(), '', false
        );
        $resourceMock->expects($this->_testObject->any())
            ->method('getIdFieldName')
            ->will($this->_testObject->returnValue('id'));

        return $resourceMock;
    }

    /**
     * Retrieve mock of core translator model
     *
     * @param string $className
     * @return Mage_Core_Model_Translate|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getTranslatorMock($className)
    {
        $translator = $this->_testObject->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->setMethods(array('translate'))
            ->getMock();
        $translateCallback = function ($arguments) {
            $result = '';
            if (is_array($arguments) && current($arguments) instanceof Mage_Core_Model_Translate_Expr) {
                /** @var Mage_Core_Model_Translate_Expr $expression */
                $expression = array_shift($arguments);
                $result = vsprintf($expression->getText(), $arguments);
            }
            return $result;
        };
        $translator->expects($this->_testObject->any())
            ->method('translate')
            ->will($this->_testObject->returnCallback($translateCallback));
        return $translator;
    }

    /**
     * Get mock without call of original constructor
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getMockWithoutConstructorCall($className)
    {
        $class = new ReflectionClass($className);
        $mock = null;
        if ($class->isAbstract()) {
            $mock = $this->_testObject->getMockForAbstractClass($className, array(), '', false, false);
        } else {
            $mock = $this->_testObject->getMock($className, array(), array(), '', false, false);
        }
        return $mock;
    }

    /**
     * Get class instance
     *
     * @param $className
     * @param array $arguments
     * @return object
     */
    public function getObject($className, array $arguments = array())
    {
        $constructArguments = $this->getConstructArguments($className, $arguments);
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->newInstanceArgs($constructArguments);
    }

    /**
     * Retrieve list of arguments that used for new object instance creation
     *
     * @param string $className
     * @param array $arguments
     * @return array
     */
    public function getConstructArguments($className, array $arguments = array())
    {
        $constructArguments = array();
        $method = new ReflectionMethod($className, '__construct');

        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->getName();
            $parameter->getClass();
            $argument = array();
            $argument['argumentClassName'] = $parameter->getClass() ? $parameter->getClass()->getName() : null;
            $argument['defaultValue'] = null;
            $argument['isAutoTestValue'] = true;
            if (isset($arguments[$parameterName])) {
                $argument = $arguments[$parameterName];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $argument['defaultValue'] = $parameter->getDefaultValue();
            }

            $constructArguments[$parameterName] = $argument;
        }

        $constructArguments = $this->_createArgumentsMock($constructArguments, $arguments);

        return $constructArguments;
    }
}
