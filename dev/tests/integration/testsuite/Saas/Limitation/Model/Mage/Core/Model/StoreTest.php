<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Mage_Core_Model_StoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Store|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_Store');
    }

    /**
     * @magentoConfigFixture limitations/store 1
     * @magentoDbIsolation enabled
     * @expectedException Mage_Core_Exception
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
