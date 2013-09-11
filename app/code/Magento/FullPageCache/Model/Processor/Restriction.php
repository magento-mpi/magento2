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

namespace Magento\FullPageCache\Model\Processor;

class Restriction
    implements \Magento\FullPageCache\Model\Processor\RestrictionInterface
{

    /**
     * @var \Magento\Core\Model\Cache\StateInterface
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
     * @var \Magento\FullPageCache\Model\Environment
     */
    protected $_environment;

    /**
     * @param \Magento\Core\Model\Cache\StateInterface $cacheState
     * @param \Magento\FullPageCache\Model\Environment $environment
     */
    public function __construct(
        \Magento\Core\Model\Cache\StateInterface $cacheState,
        \Magento\FullPageCache\Model\Environment $environment
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

        if ($this->_environment->hasQuery(\Magento\Core\Model\Session\AbstractSession::SESSION_ID_QUERY_PARAM)) {
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
