<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
    protected $_attribute;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $data = array(
            'user_type'   => 'guest' . mt_rand(),
            'resource_id' => self::ATTRIBUTE_RESOURCE_ID,
            'operation'   => 'read',
            'allowed_attributes' => self::ALLOWED_ATTRIBUTES
        );

        $this->_attribute = Mage::getModel('Mage_Api2_Model_Acl_Filter_Attribute');
        $this->_attribute->setData($data)
            ->save();

        $this->addModelToDelete($this->_attribute);
    }

    /**
     * Test get allowed attributes
     */
    public function testGetAllowedAttributes()
    {
        /** @var $resource Mage_Api2_Model_Resource_Acl_Filter_Attribute */
        $resource = Mage::getResourceModel('Mage_Api2_Model_Resource_Acl_Filter_Attribute');

        // Test method success
        $this->assertEquals(
            self::ALLOWED_ATTRIBUTES,
            $resource->getAllowedAttributes($this->_attribute->getUserType(), self::ATTRIBUTE_RESOURCE_ID, 'read')
        );

        // Test method with wrong user type
        $this->assertFalse($resource->getAllowedAttributes('customer', self::ATTRIBUTE_RESOURCE_ID, 'read'));

        // Test method with wrong resource ID
        $this->assertFalse(
            $resource->getAllowedAttributes($this->_attribute->getUserType(), 'qwerty/integration/test', 'read')
        );

        // Test method with wrong operation
        $this->assertFalse(
            $resource->getAllowedAttributes($this->_attribute->getUserType(), self::ATTRIBUTE_RESOURCE_ID, 'write')
        );
    }

    /**
     * Test check if ALL attributes allowed
     */
    public function testIsAllAttributesAllowed()
    {
        $this->_attribute->setResourceId(Mage_Api2_Model_Resource_Acl_Filter_Attribute::FILTER_RESOURCE_ALL)
            ->save();

        /** @var $resource Mage_Api2_Model_Resource_Acl_Filter_Attribute */
        $resource = Mage::getResourceModel('Mage_Api2_Model_Resource_Acl_Filter_Attribute');

        // Test method success
        $this->assertTrue($resource->isAllAttributesAllowed($this->_attribute->getUserType()));

        // Test method fail
        $this->assertFalse($resource->isAllAttributesAllowed('qwerty123123' . mt_rand()));
    }
}
