<?php
/**
 * Test for Mage_Index_Model_Indexer
 *
 * We have to implement it in Mage_Catalog module, because Mage_Index module doesn't implement any index processes
 * and also the original Mage_Index_Model_Indexer is not coverable with unit tests in current implementation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDbIsolation enabled
  */
class Mage_Catalog_IndexerTest extends PHPUnit_Framework_TestCase
{
    public function testReindexAll()
    {
        /** @var $indexer Mage_Index_Model_Indexer */
        $indexer = Mage::getModel('Mage_Index_Model_Indexer');
        $process = $indexer->getProcessByCode('catalog_product_price');
        $process->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

        $indexer->reindexAll();

        /**
         * The indexer model must be created again, in order to re-create collection. Once the collection is loaded,
         * it will contain old object of processes. This is a design flaw of the Mage_Index_Model_Indexer
         */
        $indexer = Mage::getModel('Mage_Index_Model_Indexer');
        $process = $indexer->getProcessByCode('catalog_product_price');
        $this->assertEquals(Mage_Index_Model_Process::STATUS_PENDING, $process->getStatus());
    }

    /**
     * @depends testReindexAll
     */
    public function testReindexRequired()
    {
        /** @var $indexer Mage_Index_Model_Indexer */
        $indexer = Mage::getModel('Mage_Index_Model_Indexer');
        $process = $indexer->getProcessByCode('catalog_product_attribute');
        $this->assertEquals(Mage_Index_Model_Process::STATUS_PENDING, $process->getStatus());
        $process = $indexer->getProcessByCode('catalog_product_price');
        $process->setStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);

        $indexer->reindexRequired();

        $indexer = Mage::getModel('Mage_Index_Model_Indexer');
        $process = $indexer->getProcessByCode('catalog_product_attribute');
        $this->assertEquals(Mage_Index_Model_Process::STATUS_PENDING, $process->getStatus());
        $process = $indexer->getProcessByCode('catalog_product_price');
        $this->assertEquals(Mage_Index_Model_Process::STATUS_PENDING, $process->getStatus());
    }
}
