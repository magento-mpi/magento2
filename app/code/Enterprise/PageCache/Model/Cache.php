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
class Enterprise_PageCache_Model_Cache extends Magento_Core_Model_Cache
{
    const REQUEST_MESSAGE_GET_PARAM = 'frontend_message';

    /**
     * @var string
     */
    protected $_frontendIdentifier = Enterprise_PageCache_Model_Cache_Type::TYPE_IDENTIFIER;

    /**
     * @param Magento_Core_Model_Cache_Frontend_Pool $frontendPool
     */
    public function __construct(Magento_Core_Model_Cache_Frontend_Pool $frontendPool)
    {
        Magento_Profiler::start('enterprise_page_cache_create', array(
            'group' => 'enterprise_page_cache',
            'operation' => 'enterprise_page_cache:create'
        ));

        parent::__construct($frontendPool);
        Magento_Profiler::stop('enterprise_page_cache_create');
    }
}
