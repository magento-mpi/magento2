<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Mage_Core_Model_Store_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Store_Group
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_Store_Group');
    }

    /**
     * @magentoConfigFixture limitations/store_group 1
     * @magentoDbIsolation enabled
     * @expectedException Mage_Core_Exception
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
