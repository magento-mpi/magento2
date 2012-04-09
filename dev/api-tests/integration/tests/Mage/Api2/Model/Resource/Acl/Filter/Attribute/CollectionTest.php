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
