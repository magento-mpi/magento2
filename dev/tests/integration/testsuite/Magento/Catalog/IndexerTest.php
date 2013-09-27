<?php
/**
 * Test for \Magento\Index\Model\Indexer
 *
 * We have to implement it in \Magento\Catalog module, because \Magento\Index module doesn't implement any index processes
 * and also the original \Magento\Index\Model\Indexer is not coverable with unit tests in current implementation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDbIsolation enabled
 */
namespace Magento\Catalog;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    protected function setUp()
    {
        $this->_indexer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Index\Model\Indexer');
    }

    protected function tearDown()
    {
        $this->_indexer = null;
    }

    public function testReindexAll()
    {
        $process = $this->_getProcessModel('catalog_product_price');
        $process->setStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX)->save();
        $this->assertEquals(
            \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );

        $this->_indexer->reindexAll();

        $this->assertEquals(
            \Magento\Index\Model\Process::STATUS_PENDING,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );
    }

    /**
     * @depends testReindexAll
     */
    public function testReindexRequired()
    {
        $process = $this->_getProcessModel('catalog_product_attribute');
        $process->setStatus(\Magento\Index\Model\Process::STATUS_RUNNING)->save();
        $process = $this->_getProcessModel('catalog_product_price');
        $process->setStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX)->save();
        $this->assertEquals(
            \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );

        $this->_indexer->reindexRequired();

        $this->assertEquals(
            \Magento\Index\Model\Process::STATUS_RUNNING,
            $this->_getProcessModel('catalog_product_attribute')->getStatus()
        );
        $this->assertEquals(
            \Magento\Index\Model\Process::STATUS_PENDING,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );
    }

    /**
     * Load and instantiate index process model
     *
     * We want to load it every time instead of receiving using \Magento\Index\Model\Indexer::getProcessByCode()
     * Because that method depends on state of the object, which does not reflect changes in database
     *
     * @param string $typeCode
     * @return \Magento\Index\Model\Process
     */
    private function _getProcessModel($typeCode)
    {
        $process = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Index\Model\Process');
        $process->load($typeCode, 'indexer_code');
        return $process;
    }
}
