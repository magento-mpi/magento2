<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Search_Model_Resource_Search_Grid_Collection
    extends Mage_CatalogSearch_Model_Resource_Query_Collection
{
    /**
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Mage_Core_Model_Registry $registry
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_Core_Model_Registry $registry,
        $resource = null
    ) {
        $this->_registryManager = $registry;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * @return Enterprise_Search_Model_Resource_Search_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $queryId = $this->getQuery()->getId();
        if ($queryId) {
            $this->addFieldToFilter('query_id', array('nin' => $queryId));
        }
        return $this;
    }

    /**
     *  Retrieve a value from registry by a key
     *
     * @return Mage_CatalogSearch_Model_Query
     */
    public function getQuery()
    {
        return $this->_registryManager->registry('current_catalog_search');
    }
}
