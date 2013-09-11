<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout
     */
    protected $_layout;

    public function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_layout = $objectManagerHelper->getObject('\Magento\Core\Model\Layout');
    }

    /**
     * @dataProvider translateArgumentDataProvider
     * @param string $argument
     * @param boolean $isTranslatable
     */
    public function testTranslateArgument($argument, $isTranslatable)
    {
        $reflectionObject = new ReflectionObject($this->_layout);
        $reflectionMethod = $reflectionObject->getMethod('_translateArgument');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invoke($this->_layout, new \Magento\Simplexml\Element($argument));
        if ($isTranslatable) {
            $this->assertInstanceOf('\Magento\Phrase', $result);
        } else {
            $this->assertInternalType('string', $result);
        }
    }

    /**
     * @see self::testTranslateArgument();
     * @return array
     */
    public function translateArgumentDataProvider()
    {
        return array(
            array('<argument name="argumentName">phrase</argument>', false),
            array('<argument name="argumentName" translate="true">phrase</argument>', true),
        );
    }

    /**
     * @dataProvider translateArgumentsDataProvider
     * @param string $method
     * @param string $layoutElement
     * @param boolean $isTranslatable
     */
    public function testTranslateArguments($method, $layoutElement, $isTranslatable)
    {
        $reflectionObject = new ReflectionObject($this->_layout);
        $reflectionMethod = $reflectionObject->getMethod($method);
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invoke($this->_layout, new \Magento\Core\Model\Layout\Element($layoutElement));
        $argument = $method == '_readArguments' ? $result['argumentName']['value'] : $result['argumentName'];


        if ($isTranslatable) {
            $this->assertInstanceOf('\Magento\Phrase', $argument);
        } else {
            $this->assertInternalType('string', $argument);
        }
    }

    /**
     * @see self::testsFillArgumentsArray();
     * @return array
     */
    public function translateArgumentsDataProvider()
    {
        $result = array();
        $methods = array('_extractArgs', '_fillArgumentsArray', '_readArguments');

        $inputData = array(
            '<arguments xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <argument xsi:type="string" name="argumentName">phrase</argument>
            </arguments>',
            '<arguments xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <argument xsi:type="string" name="argumentName" translate="true"><value>phrase</value></argument>
            </arguments>',
            '<arguments xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <argument xsi:type="string" name="argumentName" translate="true"><value>phrase</value></argument>
            </arguments>'
        );

        foreach ($methods as $method) {
            $result[] = array($method, $inputData[0], false);
            $result[] = array($method, $inputData[1], true);
            $result[] = array($method, $inputData[2], true);
        }
        return $result;
    }

}
