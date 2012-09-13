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
 * Test Webapi global ACL rule resource collection model
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Model_Resource_Acl_Global_Rule_CollectionTest extends Magento_TestCase
{
    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixture()
    {
        return require realpath(dirname(__FILE__) . '/../../../..') . '/Acl/_fixture/_data/rule_data.php';
    }

    /**
     * Test model CRUD
     */
    public function testCollection()
    {
        $data = $this->_getFixture();
        $cnt = 3;
        $ids = array();
        for ($i = $cnt; $i > 0; $i--) {
            /** @var $model Mage_Webapi_Model_Acl_Global_Rule */
            $model = Mage::getModel('Mage_Webapi_Model_Acl_Global_Rule');
            $setData = $data['create'];
            $setData['resource_id'] .= $i;
            $this->addModelToDelete($model, true);
            $model->setData($setData);
            $model->save();
            $ids[] = $model->getId();

            /** @var $role Mage_Webapi_Model_Acl_Global_Role */
            $role = Mage::getModel('Mage_Webapi_Model_Acl_Global_Role');
            $this->addModelToDelete($role->load($model->getRoleId()), true);
        }

        /** @var $model Mage_Webapi_Model_Acl_Global_Rule */
        $model = Mage::getModel('Mage_Webapi_Model_Acl_Global_Rule');
        $collection = $model->getCollection();
        $collection->addFilter('main_table.entity_id', array('in' => $ids), 'public');
        $this->assertEquals($cnt, $collection->count());
        $this->assertInstanceOf(get_class($model), $collection->getFirstItem());
    }
}
