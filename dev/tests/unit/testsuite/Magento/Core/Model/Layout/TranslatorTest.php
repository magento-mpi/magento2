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

class Magento_Core_Model_Layout_TranslatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Translator
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var SimpleXMLElement
     */
    protected $_xmlDocument;

    protected function setUp()
    {
        $string = <<<XML
<?xml version='1.0'?>
<layout>
    <arguments>
        <node_self_translated translate="true">test</node_self_translated>
        <node_no_self_translated>test</node_no_self_translated>
    </arguments>
    <arguments_parent translate="node node_other">
        <node>test</node>
        <node_other> test </node_other>
        <node_no_translated>no translated</node_no_translated>
    </arguments_parent>
    <action_one method="someMethod" />
    <action_two method="someMethod" translate='one two' />
    <action_three method="someMethod" translate='one two.value' />
    <action_four method="someMethod" translate='one two' />
</layout>
XML;

        $this->_xmlDocument = simplexml_load_string($string, '\Magento\Simplexml\Element');

        $this->_object = new Magento_Core_Model_Layout_Translator();
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateActionParameters
     */
    public function testTranslateActionParametersWithNonTranslatedArgument()
    {
        $args = array('one' => 'test');

        $this->_object->translateActionParameters($this->_xmlDocument->action_one, $args);
        $this->assertEquals('test', $args['one']);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateActionParameters
     */
    public function testTranslateActionParametersWithTranslatedArgument()
    {
        $args = array('one' => 'test', 'two' => 'test', 'three' => 'test');
        $expected = array('one' => __('test'), 'two' => __('test'), 'three' => 'test');

        $this->_object->translateActionParameters($this->_xmlDocument->action_two, $args);
        $this->assertEquals($expected, $args);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateActionParameters
     */
    public function testTranslateActionParametersWithHierarchyTranslatedArgumentAndNonStringParam()
    {
        $args = array('one' => array('some', 'data'), 'two' => array('value' => 'test'), 'three' => 'test');
        $expected = array('one' =>  array('some', 'data'), 'two' => array('value' => __('test')), 'three' => 'test');

        $this->_object->translateActionParameters($this->_xmlDocument->action_three, $args);
        $this->assertEquals($expected, $args);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateActionParameters
     */
    public function testTranslateActionParametersWithoutModule()
    {
        $args = array('two' => 'test', 'three' => 'test');
        $expected = array('two' => __('test'), 'three' => __('test'));

        $this->_object->translateActionParameters($this->_xmlDocument->action_four, $args);
        $this->assertEquals($expected, $args);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateArgument
     */
    public function testTranslateArgumentWithDefaultModuleAndSelfTranslatedMode()
    {
        $actual = $this->_object->translateArgument($this->_xmlDocument->arguments->node_self_translated);
        $this->assertEquals(__('test'), $actual);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateArgument
     */
    public function testTranslateArgumentWithoutModuleAndNoSelfTranslatedMode()
    {
        $actual = $this->_object->translateArgument($this->_xmlDocument->arguments->node_no_self_translated);
        $this->assertEquals('test', $actual);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateArgument
     */
    public function testTranslateArgumentViaParentNodeWithParentModule()
    {
        $actual = $this->_object->translateArgument($this->_xmlDocument->arguments_parent->node);
        $this->assertEquals(__('test'), $actual);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateArgument
     */
    public function testTranslateArgumentViaParentNodeWithOwnModule()
    {
        $actual = $this->_object->translateArgument($this->_xmlDocument->arguments_parent->node_other);
        $this->assertEquals(__('test'), $actual);
    }

    /**
     * @covers Magento_Core_Model_Layout_Translator::translateArgument
     */
    public function testTranslateArgumentViaParentWithNodeThatIsNotInTranslateList()
    {
        $actual = $this->_object->translateArgument($this->_xmlDocument->arguments_parent->node_no_translated);
        $this->assertEquals('no translated', $actual);
    }
}
