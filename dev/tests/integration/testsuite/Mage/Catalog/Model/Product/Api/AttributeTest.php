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
class Mage_Catalog_Model_Product_Api_AttributeTest extends PHPUnit_Framework_TestCase
{
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

        $expectedMessage = 'Attribute code is invalid. Please use only letters (a-z), numbers (0-9), '
            . 'or underscore(_) in this field. First character should be a letter.';
        $exception = Magento_Test_Helper_Api::callWithException($this,
            'catalogProductAttributeCreate', array('data' => $attributeData), $expectedMessage
        );
        $this->assertEquals(103, $exception->faultcode, 'Unexpected fault code');
    }
}
