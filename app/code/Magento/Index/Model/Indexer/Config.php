<?php
/**
 * Indexer configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_Config implements Magento_Index_Model_Indexer_ConfigInterface
{
    /**
     * Indexer configuration data container
     *
     * @var Magento_Index_Model_Indexer_Config_Data
     */
    protected $_dataContainer;

    /**
     * @param Magento_Index_Model_Indexer_Config_Data $dataContainer
     */
    public function __construct(Magento_Index_Model_Indexer_Config_Data $dataContainer)
    {
        $this->_dataContainer = $dataContainer;
    }

    /**
     * Get indexer data by name
     *
     * @param string $name
     * @return array
     */
    public function getIndexer($name)
    {
        return $this->_dataContainer->get($name, array());
    }

    /**
     * Get indexers configuration
     *
     * @return array
     */
    public function getAll()
    {
        return $this->_dataContainer->get();
    }
}
