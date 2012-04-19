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
 * Test API2 filter ACL attribute resource collection model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Filter_Attribute_CollectionTest extends Magento_TestCase
{
    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixture()
    {
        return require realpath(dirname(__FILE__) . '/../../../..') . '/Acl/_fixture/attributeData.php';
    }

    /**
     * Test collection
     */
    public function testCollection()
    {
        $data = $this->_getFixture();
        $cnt = 3;
        $ids = array();
        for ($i = $cnt; $i > 0; $i--) {
            /** @var $model Mage_Api2_Model_Acl_Filter_Attribute */
            $model = Mage::getModel('api2/acl_filter_attribute');
            $setData = $data['create'];
            $setData['resource_id'] .= $i;
            $this->addModelToDelete($model);
            $model->setData($setData);
            $model->save();
            $ids[] = $model->getId();
        }

        /** @var $model Mage_Api2_Model_Acl_Filter_Attribute */
        $model = Mage::getModel('api2/acl_filter_attribute');
        $collection = $model->getCollection();
        $collection->addFilter('main_table.entity_id', array('in' => $ids), 'public');
        $this->assertEquals($cnt, $collection->count());
        $this->assertInstanceOf(get_class($model), $collection->getFirstItem());
    }
}
