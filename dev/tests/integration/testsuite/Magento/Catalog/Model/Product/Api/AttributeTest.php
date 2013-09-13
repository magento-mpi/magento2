<?php
/**
 * Test API getting orders list method
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDbIsolation enabled
 */
class Magento_Catalog_Model_Product_Api_AttributeTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->markTestSkipped('Api tests were skipped');
    }

    /**
     * Tests attribute creation with invalid characters in attribute code (possible SQL injection)
     */
    public function testCreateWithInvalidCode()
    {
        $attributeData = array(
            'attribute_code' => 'mytest1.entity_id = e.entity_id); DROP TABLE aaa_test;',
            'scope' => 'global',
            'frontend_input' => 'select',
            'frontend_label' => array(
                array('store_id' => 0, 'label' => 'My Attribute With SQL Injection')
            )
        );

        $expectedMessage = 'Please correct the attribute code. Use only letters (a-z), numbers (0-9)'
            .' or underscores (_) in this field, and begin the code with a letter.';
        $exception = Magento_TestFramework_Helper_Api::callWithException($this,
            'catalogProductAttributeCreate', array('data' => $attributeData), $expectedMessage
        );
        $this->assertEquals(103, $exception->faultcode, 'Unexpected fault code');
    }
}
