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
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test API2 filter ACL attribute resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Filter_AttributeTest extends Magento_TestCase
{
    /**
     * Allowed attributes for ACL attribute
     */
    const ALLOWED_ATTRIBUTES = 'name,description,short_description,price';

    /**
     * Resource resource id
     */
    const ATTRIBUTE_RESOURCE_ID = 'test/resource';

    /**
     * API2 attribute data fixture
     *
     * @var Mage_Api2_Model_Acl_Filter_Attribute
     */
    protected static $_attribute;

    /**
     * Set attiribute data fixture
     *
     * @static
     */
    public static function attributeDataFixture()
    {
        $data = array(
            'user_type'   => 'guest' . mt_rand(),
            'resource_id' => self::ATTRIBUTE_RESOURCE_ID,
            'operation'   => 'read',
            'allowed_attributes' => self::ALLOWED_ATTRIBUTES
        );

        /** @var $role Mage_Api2_Model_Acl_Filter_Attribute */
        $attribute = Mage::getModel('api2/acl_filter_attribute');
        $attribute->setData($data)->save();

        self::$_attribute = $attribute;
    }

    /**
     * Test get allowed attributes
     *
     * @magentoDataFixture attributeDataFixture
     */
    public function testGetAllowedAttributes()
    {
        /** @var $resource Mage_Api2_Model_Resource_Acl_Filter_Attribute */
        $resource = Mage::getResourceModel('api2/acl_filter_attribute');

        $attribute = self::$_attribute;

        // Test method success
        $this->assertEquals(
            self::ALLOWED_ATTRIBUTES,
            $resource->getAllowedAttributes($attribute->getUserType(), self::ATTRIBUTE_RESOURCE_ID, 'read')
        );

        // Test method with wrong user type
        $this->assertFalse($resource->getAllowedAttributes('customer', self::ATTRIBUTE_RESOURCE_ID, 'read'));

        // Test method with wrong resource ID
        $this->assertFalse(
            $resource->getAllowedAttributes($attribute->getUserType(), 'qwerty/integration/test', 'read')
        );

        // Test method with wrong operation
        $this->assertFalse(
            $resource->getAllowedAttributes($attribute->getUserType(), self::ATTRIBUTE_RESOURCE_ID, 'write')
        );
    }

    /**
     * Test check if ALL attributes allowed
     *
     * @magentoDataFixture attributeDataFixture
     */
    public function testIsAllAttributesAllowed()
    {
        $attribute = self::$_attribute;

        $attribute->setResourceId(Mage_Api2_Model_Resource_Acl_Filter_Attribute::FILTER_RESOURCE_ALL)
            ->save();

        /** @var $resource Mage_Api2_Model_Resource_Acl_Filter_Attribute */
        $resource = Mage::getResourceModel('api2/acl_filter_attribute');

        // Test method success
        $this->assertTrue($resource->isAllAttributesAllowed($attribute->getUserType()));

        // Test method fail
        $this->assertFalse($resource->isAllAttributesAllowed('qwerty123123' . mt_rand()));
    }
}
