<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Custom fetch strategy for the store collection
 */
class Mage_Core_Model_Resource_Store_Collection_FetchStrategy
    extends Varien_Data_Collection_Db_FetchStrategy_Cache
{
    /**
     * Constructor
     *
     * @param Magento_Cache_FrontendInterface $cache
     * @param Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     */
    public function __construct(
        Magento_Cache_FrontendInterface $cache,
        Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
    ) {
        parent::__construct($cache, $fetchStrategy, 'app_', array(Mage_Core_Model_Store::CACHE_TAG), false);
    }
}
