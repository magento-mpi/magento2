<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Webapi acl global rule tree
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Acl_Global_Rule_TreeTest extends Mage_PHPUnit_TestCase
{
    /**
     * Test set role
     */
    public function testSetRole()
    {
        /* @var $role  Mage_Webapi_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Global_Role');

        /* @var $tree  Mage_Webapi_Model_Acl_Global_Rule_Tree */
        $tree = Mage::getModel('Mage_Webapi_Model_Acl_Global_Rule_Tree', array(
            'type' => Mage_Webapi_Model_Acl_Global_Rule_Tree::TYPE_PRIVILEGE));
        $tree->setRole($role);
        $this->assertEquals($tree->getRole(), $role);
    }
}
