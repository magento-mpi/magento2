<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
