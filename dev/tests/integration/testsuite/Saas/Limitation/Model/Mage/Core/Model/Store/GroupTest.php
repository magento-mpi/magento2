<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Magento_Core_Model_Store_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Store_Group
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Store_Group');
    }

    /**
     * @magentoConfigFixture limitations/store_group 1
     * @magentoDbIsolation enabled
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage You are using the maximum number of stores allowed.
     */
    public function testSaveCreateRestriction()
    {
        $this->_model->setData(array(
            'website_id'        => 1,
            'root_category_id'  => 1,
            'name'              => 'test name',
        ));
        $this->_model->save();
    }
}
