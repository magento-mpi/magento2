<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Webapi global ACL role model
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Acl_Global_RuleTest extends Magento_TestCase
{
    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixture()
    {
        return require dirname(__FILE__) . DS . '..' . DS . '_fixture' . DS . '_data' . DS . 'rule_data.php';
    }

    /**
     * Test model CRUD
     */
    public function testCrud()
    {
        $data = $this->_getFixture();
        /** @var $model Mage_Webapi_Model_Acl_Global_Rule */
        $model = Mage::getModel('Mage_Webapi_Model_Acl_Global_Rule');
        $model->setData($data['create']);

        /** @var $role Mage_Webapi_Model_Acl_Global_Role */
        $role = Mage::getModel('Mage_Webapi_Model_Acl_Global_Role');
        $this->addModelToDelete($role->load($model->getRoleId()), true);

        $testEntity = new Magento_Test_Entity($model, $data['update']);
        $testEntity->testCrud();
    }
}
