<?php
/**
 * Saas_Search default fulltext indexer model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Model_Indexer_Fulltext_Default extends Mage_CatalogSearch_Model_Indexer_Fulltext
{
    /**
     * Retrieve Fulltext Search instance
     *
     * @return Saas_Search_Model_Fulltext
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('Saas_Search_Model_Fulltext');
    }
}
