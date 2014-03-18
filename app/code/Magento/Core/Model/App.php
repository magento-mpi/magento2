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
     * Cache object
     *
     * @var \Magento\App\CacheInterface
     */
    protected $_cache;

    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Response object
     *
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Data base updater object
     *
     * @var \Magento\Module\UpdaterInterface
     */
    protected $_dbUpdater;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Config\Scope
     */
    protected $_configScope;

    /**
     * @param \Magento\App\CacheInterface $cache
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\App\State $appState
     * @param \Magento\Config\Scope $configScope
     */
    public function __construct(
        \Magento\App\CacheInterface $cache,
        \Magento\ObjectManager $objectManager,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\App\State $appState,
        \Magento\Config\Scope $configScope
    ) {
        $this->_cache = $cache;
        $this->_objectManager = $objectManager;
        $this->_appState = $appState;
        $this->_eventManager = $eventManager;
        $this->_configScope = $configScope;
    }

    /**
     * Retrieve cookie object
     *
     * @return \Magento\Stdlib\Cookie
     */
    public function getCookie()
    {
        return $this->_objectManager->get('Magento\Stdlib\Cookie');
    }

    /**
     * Loading part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  $this
     */
    public function loadAreaPart($area, $part)
    {
        $this->getArea($area)->load($part);
        return $this;
    }

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  \Magento\Core\Model\App\Area
     */
    public function getArea($code)
    {
        if (!isset($this->_areas[$code])) {
            $this->_areas[$code] = $this->_objectManager->create(
                'Magento\Core\Model\App\Area',
                array('areaCode' => $code)
            );
        }
        return $this->_areas[$code];
    }

    /**
     * Retrieve layout object
     *
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout()
    {
        return $this->_objectManager->get('Magento\View\LayoutInterface');
    }

    /**
     * Retrieve cache object
     *
     * @return \Magento\Cache\FrontendInterface
     */
    public function getCache()
    {
        return $this->_cache->getFrontend();
    }

    /**
     * Loading cache data
     *
     * @param   string $cacheId
     * @return  string
     */
    public function loadCache($cacheId)
    {
        return $this->_cache->load($cacheId);
    }

    /**
     * Saving cache data
     *
     * @param mixed $data
     * @param string $cacheId
     * @param array $tags
     * @param bool $lifeTime
     * @return $this
     */
    public function saveCache($data, $cacheId, $tags = array(), $lifeTime = false)
    {
        $this->_cache->save($data, $cacheId, $tags, $lifeTime);
        return $this;
    }

    /**
     * Remove cache
     *
     * @param   string $cacheId
     * @return  $this
     */
    public function removeCache($cacheId)
    {
        $this->_cache->remove($cacheId);
        return $this;
    }

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  $this
     */
    public function cleanCache($tags = array())
    {
        $this->_cache->clean($tags);
        return $this;
    }

    /**
     * Retrieve request object
     *
     * @return \Magento\App\RequestInterface
     */
    public function getRequest()
    {
        if (!$this->_request) {
            $this->_request = $this->_objectManager->get('Magento\App\RequestInterface');
        }
        return $this->_request;
    }
}
