<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_TransactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Transaction
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getResourceModel('Mage_Core_Model_Resource_Transaction');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testSaveDelete()
    {
        $first  = Mage::getModel('Mage_Core_Model_Store_Group');
        $first->setData(
            array(
                'website_id'        => 1,
                'name'              => 'test 1',
                'root_category_id'  => 1,
                'default_store_id'  => 1
            )
        );
        $second  = Mage::getModel('Mage_Core_Model_Store_Group');
        $second->setData(
            array(
                'website_id'        => 1,
                'name'              => 'test 2',
                'root_category_id'  => 1,
                'default_store_id'  => 1
            )
        );


        $first->save();
        $this->_model->addObject($first)
            ->addObject($second, 'second');
        $this->_model->save();
        $this->assertNotEmpty($first->getId());
        $this->assertNotEmpty($second->getId());

        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        $this->_model->delete();

        $test  = Mage::getModel('Mage_Core_Model_Store_Group');
        $test->load($first->getId());
        $this->assertEmpty($test->getId());
    }
}
