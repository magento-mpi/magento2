<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Full page cache
 *
 * @category   Enterprise
 * @package    Enterprise_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PageCache_Model_Cache extends Mage_Core_Model_Cache
{
    const REQUEST_MESSAGE_GET_PARAM = 'frontend_message';

    /**
     * @var string
     */
    protected $_frontendIdentifier = Enterprise_PageCache_Model_Cache_Type::TYPE_IDENTIFIER;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Cache_Frontend_Pool $frontendPool
     * @param Mage_Core_Model_Cache_Types $cacheTypes
     * @param Mage_Core_Model_ConfigInterface $config
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Cache_Frontend_Pool $frontendPool,
        Mage_Core_Model_Cache_Types $cacheTypes,
        Mage_Core_Model_ConfigInterface $config,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Factory_Helper $helperFactory
    ) {
        Magento_Profiler::start('enterprise_page_cache_create', array(
            'group' => 'enterprise_page_cache',
            'operation' => 'enterprise_page_cache:create'
        ));
        parent::__construct($objectManager, $frontendPool, $cacheTypes, $config, $dirs, $helperFactory);
        Magento_Profiler::stop('enterprise_page_cache_create');
    }
}
