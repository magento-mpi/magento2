<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Mage_Core_Model_Resource_Website_Collection_FetchStrategy
    extends Varien_Data_Collection_Db_FetchStrategy_Cache
{
    public function __construct(
        Magento_Cache_FrontendInterface $cache,
        Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
    ) {
        parent::__construct($cache, $fetchStrategy, 'app_', array(Mage_Core_Model_Website::CACHE_TAG), false);
    }
}
