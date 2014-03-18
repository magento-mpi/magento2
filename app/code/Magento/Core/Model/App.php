<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

/**
 * Application model
 *
 * Application should have: areas, store, locale, translator, design package
 */
class App implements \Magento\AppInterface
{
    /**#@+
     * Product edition labels
     */
    const EDITION_COMMUNITY    = 'Community';
    const EDITION_ENTERPRISE   = 'Enterprise';
    /**#@-*/

    /**
     * Current Magento edition.
     *
     * @var string
     * @static
     */
    protected $_currentEdition = self::EDITION_COMMUNITY;

    /**
     * Magento version
     */
    const VERSION = '2.0.0.0-dev68';

    /**
     * Application run code
     */
    const PARAM_RUN_CODE = 'MAGE_RUN_CODE';

    /**
     * Application run type (store|website)
     */
    const PARAM_RUN_TYPE = 'MAGE_RUN_TYPE';

    /**
     * Disallow cache
     */
    const PARAM_BAN_CACHE = 'global_ban_use_cache';

    /**
     * Allowed modules
     */
    const PARAM_ALLOWED_MODULES = 'allowed_modules';

    /**
     * Caching params, that applied for all cache frontends regardless of type
     */
    const PARAM_CACHE_FORCED_OPTIONS = 'cache_options';

    /**
     * Application loaded areas array
     *
     * @var array
     */
    protected $_areas = array();

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\ObjectManager $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

}
