<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event resource setup
 */
class Magento_CatalogEvent_Model_Resource_Setup extends Magento_Sales_Model_Resource_Setup
{
    /**
     * Block model factory
     *
     * @var Magento_Cms_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * @param Magento_Cms_Model_BlockFactory $modelBlockFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_Resource_Setup_Context $context
     * @param string $resourceName
     * @param string $moduleName
     * @param string $connectionName
     */
    public function __construct(
        Magento_Cms_Model_BlockFactory $modelBlockFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_Resource_Setup_Context $context,
        $resourceName,
        $moduleName = 'Magento_CatalogEvent',
        $connectionName = ''
    ) {
        $this->_blockFactory = $modelBlockFactory;
        parent::__construct($coreData, $cache, $context, $resourceName, $moduleName, $connectionName);
    }


    /**
     * Get model block factory
     *
     * @return Magento_Cms_Model_BlockFactory
     */
    public function getBlockFactory()
    {
        return $this->_blockFactory;
    }
}
