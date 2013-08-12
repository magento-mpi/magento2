<?php
/**
 * Test for Magento_Index_Model_Indexer
 *
 * We have to implement it in Magento_Catalog module, because Magento_Index module doesn't implement any index processes
 * and also the original Magento_Index_Model_Indexer is not coverable with unit tests in current implementation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDbIsolation enabled
 */
class Magento_Catalog_IndexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    protected function setUp()
    {
        $this->_indexer = Mage::getModel('Magento_Index_Model_Indexer');
    }

    protected function tearDown()
    {
        $this->_indexer = null;
    }

    public function testReindexAll()
    {
        $process = $this->_getProcessModel('catalog_product_price');
        $process->setStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX)->save();
        $this->assertEquals(
            Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );

        $this->_indexer->reindexAll();

        $this->assertEquals(
            Magento_Index_Model_Process::STATUS_PENDING,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );
    }

    /**
     * @depends testReindexAll
     */
    public function testReindexRequired()
    {
        $process = $this->_getProcessModel('catalog_product_attribute');
        $process->setStatus(Magento_Index_Model_Process::STATUS_RUNNING)->save();
        $process = $this->_getProcessModel('catalog_product_price');
        $process->setStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX)->save();
        $this->assertEquals(
            Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );

        $this->_indexer->reindexRequired();

        $this->assertEquals(
            Magento_Index_Model_Process::STATUS_RUNNING,
            $this->_getProcessModel('catalog_product_attribute')->getStatus()
        );
        $this->assertEquals(
            Magento_Index_Model_Process::STATUS_PENDING,
            $this->_getProcessModel('catalog_product_price')->getStatus()
        );
    }

    /**
     * Load and instantiate index process model
     *
     * We want to load it every time instead of receiving using Magento_Index_Model_Indexer::getProcessByCode()
     * Because that method depends on state of the object, which does not reflect changes in database
     *
     * @param string $typeCode
     * @return Magento_Index_Model_Process
     */
    private function _getProcessModel($typeCode)
    {
        $process = Mage::getModel('Magento_Index_Model_Process');
        $process->load($typeCode, 'indexer_code');
        return $process;
    }
}
