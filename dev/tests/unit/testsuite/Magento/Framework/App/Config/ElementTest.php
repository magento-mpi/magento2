<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Config;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Element
     */
    protected $_model;

    protected function setUp()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <is_test>
        <value_key>value</value_key>
        <value_sensitive_key>vaLue</value_sensitive_key>
        <false_key>false</false_key>
        <off_key>off</off_key>
        <on_key>on</on_key>
        <regular_cdata><![CDATA[value]]></regular_cdata>
        <empty_cdata><![CDATA[]]></empty_cdata>
        <empty_text></empty_text>
    </is_test>
    <class_test>
        <class>Magento\Catalog\Model\Observer</class>
    </class_test>
    <model_test>
        <model>Magento\Catalog\Model\Observer</model>
    </model_test>
    <no_classname_test>
        <none/>
    </no_classname_test>
</root>
XML;
        $this->_model = new Element($xml);
    }

    public function testIs()
    {
        /** @var Element $element */
        $element = $this->_model->is_test;
        $this->assertTrue($element->is('value_key', 'value'));
        $this->assertTrue($element->is('value_sensitive_key', 'value'));
        $this->assertTrue($element->is('regular_cdata', 'value'));
        $this->assertFalse($element->is('false_key'));
        $this->assertFalse($element->is('empty_cdata'));
        $this->assertFalse($element->is('empty_text'));
        $this->assertTrue($element->is('on_key'));
    }

    public function testGetClassName()
    {
        $this->assertEquals('Magento\Catalog\Model\Observer', $this->_model->class_test->getClassName());
        $this->assertEquals('Magento\Catalog\Model\Observer', $this->_model->model_test->getClassName());
        $this->assertFalse($this->_model->no_classname_test->getClassName());
    }
} 