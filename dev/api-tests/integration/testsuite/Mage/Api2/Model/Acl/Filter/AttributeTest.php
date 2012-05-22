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
 * Test API2 filter ACL attribute model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Acl_Filter_AttributeTest extends Magento_TestCase
{
    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixture()
    {
        return require dirname(__FILE__) . DS . '..' . DS . '_fixture' .DS . 'attributeData.php';
    }

    /**
     * Test model CRUD
     */
    public function testCrud()
    {
        $data = $this->_getFixture();
        /** @var $model Mage_Api2_Model_Acl_Filter_Attribute */
        $model = Mage::getModel('Mage_Api2_Model_Acl_Filter_Attribute');
        $this->addModelToDelete($model);
        $model->setData($data['create']);
        $testEntity = new Magento_Test_Entity($model, $data['update']);
        $testEntity->testCrud();
    }
}
