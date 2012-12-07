<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API getting orders list method
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api_Catalog_Product_AttributeTest extends Magento_Test_Webservice
{
    /**
     * Tests attribute creation with invalid characters in attribute code (possible SQL injection)
     *
     * @return void
     */
    public function testCreateWithInvalidCode()
    {
        $attributeData = array(
            'attribute_code' => 'mytest1.entity_id = e.entity_id); DROP TABLE aaa_test;',
            'scope'          => 'global',
            'frontend_input' => 'select',
            'frontend_label' => array(
                array('store_id' => 0, 'label' => 'My Attribute With SQL Injection')
            )
        );

        try {
            $this->call('product_attribute.create', array('data' => $attributeData));

            $this->fail('Exception with message like "invalid attribute code" expected but not thrown');
        } catch (Exception $e) {
            if (TESTS_WEBSERVICE_TYPE == Magento_Test_Webservice::TYPE_SOAPV2
                || TESTS_WEBSERVICE_TYPE == Magento_Test_Webservice::TYPE_SOAPV1
                || TESTS_WEBSERVICE_TYPE == Magento_Test_Webservice::TYPE_SOAPV2_WSI) {
                $this->assertEquals(103, $e->faultcode, 'Unexpected fault code');
            }
            $this->assertEquals(
                'Attribute code is invalid. Please use only letters (a-z), numbers (0-9) or'
                . ' underscore(_) in this field, first character should be a letter.',
                $e->getMessage(),
                'Unexpected exception messsage'
            );
        }
    }
}
