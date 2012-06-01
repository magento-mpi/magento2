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
 * Test API2 global ACL role resource collection model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Global_Role_CollectionTest extends Magento_TestCase
{
    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixture()
    {
        return require realpath(dirname(__FILE__) . '/../../../..') . '/Acl/_fixture/_data/role_data.php';
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
            /** @var $model Mage_Api2_Model_Acl_Global_Role */
            $model = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
            $this->addModelToDelete($model, true);
            $setData = $data['create'];
            $setData['role_name'] .= $i;
            $model->setData($setData);
            $model->save();
            $ids[] = $model->getId();
        }

        /** @var $model Mage_Api2_Model_Acl_Global_Role */
        $model = Mage::getModel('Mage_Api2_Model_Acl_Global_Role');
        $collection = $model->getCollection();
        $collection->addFilter('main_table.entity_id', array('in' => $ids), 'public');
        $this->assertEquals($cnt, $collection->count(), 'Count of collection loaded data');
        $this->assertInstanceOf(get_class($model), $collection->getFirstItem());
    }
}
