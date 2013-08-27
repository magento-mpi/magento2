<?php
/**
 * Page cache processor restriction model.
 * Check if processor is allowed for current HTTP request.
 * Disable processing HTTPS requests and requests with "NO_CACHE" cookie
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_Processor_Restriction
    implements Enterprise_PageCache_Model_Processor_RestrictionInterface
{

    /**
     * @var Magento_Core_Model_Cache_StateInterface
     */
    protected $_cacheState;

    /**
     * Flag is denied mode
     *
     * @var bool
     */
    protected $_isDenied = false;

    /**
     * Application environment
     *
     * @var Enterprise_PageCache_Model_Environment
     */
    protected $_environment;

    /**
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     * @param Enterprise_PageCache_Model_Environment $environment
     */
    public function __construct(
        Magento_Core_Model_Cache_StateInterface $cacheState,
        Enterprise_PageCache_Model_Environment $environment
    ) {
        $this->_cacheState = $cacheState;
        $this->_environment = $environment;
    }

    /**
     * Check if processor is allowed for current HTTP request.
     *
     * @param string $requestId
     * @return bool
     */
    public function isAllowed($requestId)
    {
        if (true === $this->_isDenied || !$requestId) {
            return false;
        }

        if ('on' === $this->_environment->getServer('HTTPS')) {
            return false;
        }

        if ($this->_environment->hasCookie(self::NO_CACHE_COOKIE)) {
            return false;
        }

        if ($this->_environment->hasQuery('no_cache')) {
            return false;
        }

        if ($this->_environment->hasQuery(Magento_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM)) {
            return false;
        }

        if (!$this->_cacheState->isEnabled('full_page')) {
            return false;
        }

        return true;
    }

    /**
     * Set is denied mode for FPC processors
     */
    public function setIsDenied()
    {
        $this->_isDenied = true;
    }
}
