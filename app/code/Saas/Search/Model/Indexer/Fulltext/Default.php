<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Saas_Search default fulltext indexer model
 *
 * @category   Saas
 * @package    Saas_Search
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
