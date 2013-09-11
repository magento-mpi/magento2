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
     * @var \Magento\Core\Model\Resource\Transaction
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getResourceModel('\Magento\Core\Model\Resource\Transaction');
    }

    public function testSaveDelete()
    {
        $first  = Mage::getModel('\Magento\Core\Model\Store\Group');
        $first->setData(
            array(
                'website_id'        => 1,
                'name'              => 'test 1',
                'root_category_id'  => 1,
                'default_store_id'  => 1
            )
        );
        $second  = Mage::getModel('\Magento\Core\Model\Store\Group');
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

        Mage::app()->getStore()->setId(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID);
        $this->_model->delete();

        $test  = Mage::getModel('\Magento\Core\Model\Store\Group');
        $test->load($first->getId());
        $this->assertEmpty($test->getId());
    }
}
