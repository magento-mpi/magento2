<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Magento_Core_Model_StoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Store|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Store');
    }

    /**
     * @magentoConfigFixture limitations/store 1
     * @magentoDbIsolation enabled
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Sorry, you are using all the store views your account allows.
     */
    public function testSaveCreateRestriction()
    {
        $this->_model->setData(array(
            'code'          => 'test',
            'website_id'    => 1,
            'group_id'      => 1,
            'name'          => 'test name',
            'sort_order'    => 0,
            'is_active'     => 1,
        ));
        $this->_model->save();
    }
}
