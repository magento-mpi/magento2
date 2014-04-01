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
namespace Magento\Core\Model\Resource;

class TransactionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\DB\Transaction
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\DB\Transaction');
    }

    /**
     * @magentoAppArea adminhtml
     */
    public function testSaveDelete()
    {
        $first = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Group');
        $first->setData(
            array('website_id' => 1, 'name' => 'test 1', 'root_category_id' => 1, 'default_store_id' => 1)
        );
        $second = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Store\Group'
        );
        $second = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Group');
        $second->setData(
            array('website_id' => 1, 'name' => 'test 2', 'root_category_id' => 1, 'default_store_id' => 1)
        );


        $first->save();
        $this->_model->addObject($first)->addObject($second, 'second');
        $this->_model->save();
        $this->assertNotEmpty($first->getId());
        $this->assertNotEmpty($second->getId());

        $this->_model->delete();

        $test = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Group');
        $test->load($first->getId());
        $this->assertEmpty($test->getId());
    }
}
