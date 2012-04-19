<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test API2 acl global rule tree
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Global_Rule_TreeTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test set role
     */
    public function testSetRole()
    {
        /* @var $role  Mage_Api2_Model_Acl_Global_Role */
        $role = Mage::getModel('api2/acl_global_role');

        /* @var $tree  Mage_Api2_Model_Acl_Global_Rule_Tree */
        $tree = Mage::getModel('api2/acl_global_rule_tree', array(
            'type' => Mage_Api2_Model_Acl_Global_Rule_Tree::TYPE_PRIVILEGE));
        $tree->setRole($role);
        $this->assertEquals($tree->getRole(), $role);
    }
}
