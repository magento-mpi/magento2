<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Varien_Cache_Backend_Decorator_DecoratorAbstract test case
 */
class Varien_Cache_Backend_Decorator_DecoratorAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Cache_Backend_File
     */
    protected $_mockBackend;

    protected function setUp()
    {
        $this->_mockBackend = $this->getMock('Zend_Cache_Backend_File');
    }

    protected function tearDown()
    {
        unset($this->_mockBackend);
    }

    public function testConstructor()
    {
        $options = array('concrete_backend' => $this->_mockBackend, 'testOption' => 'testOption');

        $decorator = $this->getMockForAbstractClass(
            'Varien_Cache_Backend_Decorator_DecoratorAbstract',
            array($options)
        );

        $backendProperty = new ReflectionProperty('Varien_Cache_Backend_Decorator_DecoratorAbstract', '_backend');
        $backendProperty->setAccessible(true);

        $optionsProperty =
            new ReflectionProperty('Varien_Cache_Backend_Decorator_DecoratorAbstract', '_decoratorOptions');
        $optionsProperty->setAccessible(true);

        $this->assertEquals($backendProperty->getValue($decorator), $this->_mockBackend);

        $this->assertArrayNotHasKey('concrete_backend', $optionsProperty->getValue($decorator));
        $this->assertArrayNotHasKey('testOption', $optionsProperty->getValue($decorator));
    }

    /**
     * @expectedException Varien_Cache_Exception
     * @dataProvider constructorExceptionProvider
     */
    public function testConstructorException($options)
    {
        $this->getMockForAbstractClass(
            'Varien_Cache_Backend_Decorator_DecoratorAbstract',
            array($options)
        );
    }

    public function constructorExceptionProvider()
    {
        return array(
            'empty' => array(array()),
            'wrong_class' => array(array('concrete_backend' => $this->getMock('Test_Class')))
        );
    }

    /**
     * @dataProvider allMethodsProvider
     */
    public function testAllMethods($methodName)
    {
        $this->_mockBackend->expects($this->once())->method($methodName);

        $decorator = $this->getMockForAbstractClass(
            'Varien_Cache_Backend_Decorator_DecoratorAbstract',
            array(array('concrete_backend' => $this->_mockBackend))
        );

        call_user_func(array($decorator, $methodName), null, null);
    }

    public function allMethodsProvider()
    {
        $return = array();
        $allMethods = array('setDirectives', 'load', 'test', 'save', 'remove', 'clean', 'getIds', 'getTags',
            'getIdsMatchingTags', 'getIdsNotMatchingTags', 'getIdsMatchingAnyTags', 'getFillingPercentage',
            'getMetadatas', 'touch', 'getCapabilities', 'setOption', 'getLifetime', 'isAutomaticCleaningAvailable',
            'getTmpDir');
        foreach ($allMethods as $method) {
            $return[$method] = array($method);
        }
        return $return;
    }
}
