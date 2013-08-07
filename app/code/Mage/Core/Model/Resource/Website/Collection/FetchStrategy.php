<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Custom fetch strategy for the website collection
 */
class Mage_Core_Model_Resource_Website_Collection_FetchStrategy
    extends Magento_Data_Collection_Db_FetchStrategy_Cache
{
    /**
     * Constructor
     *
     * @param Magento_Cache_FrontendInterface $cache
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     */
    public function __construct(
        Magento_Cache_FrontendInterface $cache,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
    ) {
        parent::__construct($cache, $fetchStrategy, 'app_', array(Mage_Core_Model_Website::CACHE_TAG), false);
    }
}
