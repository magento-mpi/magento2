<?php
/**
 * Application interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento;

interface AppInterface
{
    /**
     * Default application locale
     */
    const DISTRO_LOCALE_CODE = 'en_US';

    /**
     * Throw an exception, if the application has not been installed yet
     *
     * @throws \Magento\Exception
     */
    public function requireInstalledInstance();

    /**
     * Retrieve cookie object
     *
     * @return \Magento\Stdlib\Cookie
     */
    public function getCookie();

    /**
     * Re-declare custom error handler
     *
     * @param   string $handler
     * @return  \Magento\AppInterface
     */
    public function setErrorHandler($handler);

    /**
     * Loading part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  \Magento\AppInterface
     */
    public function loadAreaPart($area, $part);

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  \Magento\Core\Model\App\Area
     */
    public function getArea($code);

    /**
     * Get distributive locale code
     *
     * @return string
     */
    public function getDistroLocaleCode();

    /**
     * Retrieve application locale resolver object
     *
     * @return \Magento\Locale\ResolverInterface
     */
    public function getLocaleResolver();

    /**
     * Retrieve layout object
     *
     * @return \Magento\View\LayoutInterface
     */
    public function getLayout();

    /**
     * Retrieve application base currency code
     *
     * @return string
     */
    public function getBaseCurrencyCode();

    /**
     * Retrieve configuration object
     *
     * @return \Magento\App\ConfigInterface
     */
    public function getConfig();

    /**
     * Retrieve front controller object
     *
     * @return \Magento\App\FrontController
     */
    public function getFrontController();

    /**
     * Get core cache model
     *
     * @return \Magento\App\CacheInterface
     */
    public function getCacheInstance();


    /**
     * Retrieve cache object
     *
     * @return \Zend_Cache_Core
     */
    public function getCache();

    /**
     * Loading cache data
     *
     * @param   string $cacheId
     * @return  mixed
     */
    public function loadCache($cacheId);

    /**
     * Saving cache data
     *
     * @param mixed $data
     * @param string $cacheId
     * @param array $tags
     * @param bool $lifeTime
     * @return \Magento\AppInterface
     */
    public function saveCache($data, $cacheId, $tags = array(), $lifeTime = false);

    /**
     * Remove cache
     *
     * @param   string $cacheId
     * @return  \Magento\AppInterface
     */
    public function removeCache($cacheId);

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  \Magento\AppInterface
     */
    public function cleanCache($tags = array());

    /**
     * Deletes all session files
     *
     * @return \Magento\AppInterface
     */
    public function cleanAllSessions();

    /**
     * Retrieve request object
     *
     * @return \Magento\App\RequestInterface
     */
    public function getRequest();

    /**
     * Request setter
     *
     * @param \Magento\App\RequestInterface $request
     * @return \Magento\AppInterface
     */
    public function setRequest(\Magento\App\RequestInterface $request);

    /**
     * Retrieve response object
     *
     * @return \Magento\App\ResponseInterface
     */
    public function getResponse();

    /**
     * Response setter
     *
     * @param \Magento\App\ResponseInterface $response
     * @return \Magento\AppInterface
     */
    public function setResponse(\Magento\App\ResponseInterface $response);

    /**
     * Check if developer mode is enabled.
     *
     * @return bool
     */
    public function isDeveloperMode();
}
