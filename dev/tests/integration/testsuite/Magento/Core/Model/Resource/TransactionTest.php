<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_TransactionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Transaction
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Resource_Transaction');
    }

    public function testSaveDelete()
    {
        $first  = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Store_Group');
        $first->setData(
            array(
                'website_id'        => 1,
                'name'              => 'test 1',
                'root_category_id'  => 1,
                'default_store_id'  => 1
            )
        );
        $second  = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Store_Group');
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

        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->setId(Magento_Core_Model_AppInterface::ADMIN_STORE_ID);
        $this->_model->delete();

        $test  = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Store_Group');
        $test->load($first->getId());
        $this->assertEmpty($test->getId());
    }
}
