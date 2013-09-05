<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Search_Model_Resource_Search_Grid_Collection
    extends Magento_CatalogSearch_Model_Resource_Query_Collection
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Registry $registry,
        $resource = null
    ) {
        $this->_registryManager = $registry;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * @return Magento_Search_Model_Resource_Search_Grid_Collection
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
     * @return Magento_CatalogSearch_Model_Query
     */
    public function getQuery()
    {
        return $this->_registryManager->registry('current_catalog_search');
    }
}
