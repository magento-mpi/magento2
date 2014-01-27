<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Full page cache
 *
 * @category   Magento
 * @package    Magento_FullPageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\FullPageCache\Model;

class Cache extends \Magento\App\Cache
{
    const REQUEST_MESSAGE_GET_PARAM = 'frontend_message';

    /**
     * @var string
     */
    protected $_frontendIdentifier = 'full_page_cache';

    /**
     * @param \Magento\App\Cache\Frontend\Pool $frontendPool
     */
    public function __construct(\Magento\App\Cache\Frontend\Pool $frontendPool)
    {
        \Magento\Profiler::start('magento_fullpage_cache_create', array(
            'group' => 'magento_fullpage_cache',
            'operation' => 'magento_fullpage_cache:create'
        ));

        parent::__construct($frontendPool);
        \Magento\Profiler::stop('magento_fullpage_cache_create');
    }
}
